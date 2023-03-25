<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

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
        $response = OpenAI::images()->create([
            'prompt' => $messages[0]['content'],
            'n' => 1,
            'size' => '256x256',
            'response_format' => 'url',
        ]);

        $response->created;

        return $response;
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
//    public function index(Request $request)
//    {
//        $this->typing($request);
//
//       $response = $this->createImage($request);
//
//       $this->saveChatContext($response)
//
//
//
//        foreach ($response->data as $data) {
//
//            Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendMessage', [
//                'chat_id' => $request->input('message')['from']['id'],
//                'text' => $data->url
//            ]);
//        }
//    }
//
//    public function typing(Request $request)
//    {
//        Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendChatAction', [
//            'chat_id' => $request->input('message')['from']['id'],
//            'action' => 'typing'
//        ]);
//    }
//    public function createImage(Request $request)
//    {
//        $response = OpenAI::images()->create([
//            'prompt' => 'blond',
//            'n' => 1,
//            'size' => '256x256',
//            'response_format' => 'url',
//        ]);
//
//        $response->created;
//
//        return $response;
//    }
//
//    public function saveChatContext($user, Request $request, $messages, $response)
//    {
//        foreach ($response->data as $data) {
//            Chat::create([
//                'telegram_id' => $request->input('message')['from']['id'],
//                'user_id' => $user->id,
//                'context' => array_merge($messages, [['role' => 'assistant', 'content' => $data->url]])
//            ]);
//        }
//
//    }
}
