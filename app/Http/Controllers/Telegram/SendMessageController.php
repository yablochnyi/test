<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\NoReturn;

class SendMessageController extends Controller
{
    public function __invoke($message)
    {
        $users = User::get();
        foreach ($users as $user)
        {
            Http::post('https://api.tlgr.org/bot' . config('bots')['api'] . '/sendMessage', [
                'chat_id' => $user['telegram_id'],
                'text' => $message,
            ]);
        }
    }
}
