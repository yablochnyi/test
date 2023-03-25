<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('message') && $request->input('message')['text']) {
            $text = mb_strtolower($request->input('message')['text']);
            if (strpos($text, 'нарисуй') !== false) {
                $image = new ImageController();
                return $image->index($request);
            } else {
                $messages = new MessageController();
                return $messages->index($request);
            }
        }
    }

}
