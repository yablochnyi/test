<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ImageController extends Controller
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
        $user = User::firstOrCreate(
            [
                'telegram_id' => $request->input('message')['from']['id']
            ],
            [
                'telegram_id' => $request->input('message')['from']['id'],
                'username' => $request->input('message')['from']['username'],
                'name' => $request->input('message')['from']['first_name'],
            ]);
        return $user;
    }

    public function typing(Request $request)
    {
        Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendChatAction', [
            'chat_id' => $request->input('message')['from']['id'],
            'action' => 'typing'
        ]);
    }

    public function getUserMessage(Request $request): array
    {
        $messages[] = ['role' => 'user', 'content' => $request->input('message')['text']];
        return $messages;
    }

    public function getAssistantResponse($messages): \OpenAI\Responses\Images\CreateResponse
    {
        $message = str_replace("нарисуй", "", $messages[0]['content']);
        $message = $this->translate($message);
        $response = OpenAI::images()->create([
            'prompt' => $messages,
            'n' => 1,
            'size' => '512x512',
            'response_format' => 'url',
        ]);

        $response->created;

        return $response;
    }

    public function translate($message)
    {
        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
//        $tr->setSource('en'); // Translate from English
        $tr->setSource(); // Detect language automatically
        $tr->setTarget('en'); // Translate to Georgian
        $tr->translate($message);
        return $tr;
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
            Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendMessage', [
                'chat_id' => $request->input('message')['from']['id'],
                'text' => $data->url
            ]);
        }
    }
}
