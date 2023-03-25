<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $text = $request->input('message')['text'];

        if (strpos($text, 'нарисуй') !== false) {
            $image = new ImageController();
            return $image->index($request);
        } else {
            $messages = new MessageController();
            return $messages->index($request);
        }
    }

}
