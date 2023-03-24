<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class IndexController extends Controller
{
    public function index(Request $request)
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

        $messages[] = ['role' => 'user', 'content' => $request->input('message')['text']];
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages
        ]);
        $messages[] = ['role' => 'assistant', 'content' => $response->choices[0]->message->content];
        Chat::updateOrCreate(
            [
                'telegram_id' => $request->input('message')['from']['id'],
            ],
            [
                'context' => $messages
            ]);
        Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendMessage', [
            'chat_id' => 294041458,
            'text' => $response->choices[0]->message->content
        ]);

    }
}
