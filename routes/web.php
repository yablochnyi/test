<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat/{id?}', \App\Http\Controllers\ChatGptIndexController::class)->name('chat.show');
    Route::post('/chat/{id?}', \App\Http\Controllers\ChatGptStoreController::class)->name('chat.store');
});

Route::get('/test', function () {
    \Illuminate\Support\Facades\Http::post('https://api.tlgr.org/bot6265500701:AAEE7RplIj_t567pNCbFQk9O1xyCBSX7Yng/sendMessage', [
        'chat_id' => 294041458,
        'text' => 'Hello'
    ]);
});

require __DIR__.'/auth.php';
