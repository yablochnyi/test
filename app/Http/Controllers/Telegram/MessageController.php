<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->getOrCreateUser($request);

        $this->typing($request);

        $messages = $this->getUserMessage($request);

        $response = $this->getAssistantResponse($messages);

        $this->saveChatContext($user, $request, $messages, $response);

        $this->sendAssistantResponse($request, $response);
    }

    public function getOrCreateUser(Request $request)
    {
        $message = $request->input('message');
        $userId = $message['from']['id'];
        $username = $message['from']['username'] ?? $message['from']['last_name'];
        $firstName = $message['from']['first_name'];

        return User::firstOrCreate(
            ['telegram_id' => $userId],
            ['telegram_id' => $userId, 'username' => $username, 'name' => $firstName]
        );
    }

    public function typing(Request $request)
    {
        Http::post('https://api.tlgr.org/bot' . config('bots')['api'] . '/sendChatAction', [
            'chat_id' => $request->input('message')['from']['id'],
            'action' => 'typing'
        ]);
    }

    public function getUserMessage(Request $request): array
    {
        $messages[] = ['role' => 'user', 'content' => $request->input('message')['text']];
        return $messages;
    }

    public function getAssistantResponse($messages): CreateResponse
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages
        ]);
    }

    public function saveChatContext($user, Request $request, $messages, $response)
    {
        Chat::create([
            'telegram_id' => $request->input('message')['from']['id'],
            'user_id' => $user->id,
            'context' => array_merge($messages, [['role' => 'assistant', 'content' => $response->choices[0]->message->content]])
        ]);
    }

    public function sendAssistantResponse(Request $request, $response)
    {
        Http::post('https://api.tlgr.org/bot' . config('bots')['api'] . '/sendMessage', [
            'chat_id' => $request->input('message')['from']['id'],
            'text' => $response->choices[0]->message->content
        ]);
    }
}
