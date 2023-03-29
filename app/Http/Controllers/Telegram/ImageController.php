<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        try {

            $user = $this->getOrCreateUser($request);

            $this->typing($request);

            $messages = $this->getUserMessage($request);

            $response = $this->getAssistantResponse($messages);

            $this->saveChatContext($user, $request, $messages, $response);

            $this->sendAssistantResponse($request, $response);

        } catch (ErrorException $e) {
            $this->error($request);
        }
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
        Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendChatAction', [
            'chat_id' => $request->input('message')['from']['id'],
            'action' => 'upload_photo'
        ]);
    }

    public function getUserMessage(Request $request): array
    {
        $messages[] = ['role' => 'user', 'content' => $request->input('message')['text']];
        return $messages;
    }

    public function getAssistantResponse($messages)
    {
        $message = str_replace("нарисуй", "", mb_strtolower($messages[0]['content']));
        $message = $this->translate($message);
        $response = OpenAI::images()->create([
            'prompt' => $message,
            'n' => 1,
            'size' => '512x512',
            'response_format' => 'url',
        ]);

        $response->created;

        return $response;
    }

    public function translate($message): string
    {
        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
//        $tr->setSource('en'); // Translate from English
        $tr->setSource(); // Detect language automatically
        $tr->setTarget('en'); // Translate to Georgian
        $text = $tr->translate($message);
        return (string)$text;
    }

    public function saveChatContext($user, Request $request, $messages, $response)
    {
        foreach ($response->data as $data) {
            Chat::create([
                'telegram_id' => $request->input('message')['from']['id'],
                'user_id' => $user->id,
                'context' => array_merge($messages, [['role' => 'assistant', 'content' => $data->url]])
            ]);
        }
    }

    public function sendAssistantResponse(Request $request, $response)
    {
        foreach ($response->data as $data) {
            Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendPhoto', [
                'chat_id' => $request->input('message')['from']['id'],
                'photo' => $data->url,
                'parse_mode' => 'html'
            ]);
        }
    }

    public function error(Request $request)
    {
        Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendMessage', [
            'chat_id' => $request->input('message')['from']['id'],
            'text' => 'Я не могу создавать неприемлемый или нежелательный контента, такого как распространение ложной информации, провокационных высказываний, оскорбительных или дискриминационных комментариев и других вредных видов контента',
            'parse_mode' => 'html'
        ]);
    }
}
