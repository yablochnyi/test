<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Couchbase\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

class IndexController extends Controller
{
    public function index(Request $request)
    {

        // Проверяем, есть ли у сообщения голосовое сообщение
//        if ($request->has('message') && isset($request->input('message')['voice'])) {
//            // Получаем идентификатор файла голосового сообщения
//            $file_id = $request->input('message')['voice']['file_id'];
//            // Получаем уникальный идентификатор файла голосового сообщения
//            $file_unique_id = $request->input('message')['voice']['file_unique_id'];
//
//            // Получаем ссылку на файл голосового сообщения
//            $file_url = 'https://api.telegram.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/getFile?file_id=' . $file_id;
//            $response = file_get_contents($file_url);
//            $response = json_decode($response, true);
//            $file_path = $response['result']['file_path'];
//
//            // Формируем ссылку на загрузку файла голосового сообщения
//            $file_url = 'https://api.telegram.org/file/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/' . $file_path;
//
//            // Загружаем файл голосового сообщения и сохраняем на сервере
//            $contents = file_get_contents($file_url);
//            Storage::disk('public')->put('/voice_messages/' . $file_unique_id . '.mp3', $contents);
//
//            $response = OpenAI::audio()->transcribe([
//                'model' => 'whisper-1',
//                'file' => fopen(storage_path('app/public/voice_messages/AgAD2iUAAhEdAAFJ.mp3'), 'r'),
//                'response_format' => 'verbose_json',
//            ]);
//
//            $response->task; // 'transcribe'
//            $response->language; // 'english'
//            $response->duration; // 2.95
//            $response->text; // 'Hello, how are you?'
//            Log::debug($response->text);


        if ($request->has('message') && isset($request->input('message')['text'])) {
//        } else {
            // Если это не голосовое сообщение, обрабатываем сообщение текстового формата
            $text = mb_strtolower($request->input('message')['text']);

            if (strpos($text, 'нарисуй') !== false) {
                $image = new ImageController();
                return $image->index($request);
            } elseif ($text == '/start') {
                Http::post('https://api.tlgr.org/bot' . config('bots.api') . '/sendMessage', [
                    'chat_id' => $request->input('message')['from']['id'],
                    'text' => 'Добро пожаловать!'
                ]);
            } else {
                $messages = new MessageController();
                return $messages->index($request);
            }
        }
    }
}
