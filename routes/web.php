<?php

use App\Http\Controllers\Telegram\TelegramWebhookController;
use App\Http\Controllers\Trello\TrelloWebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('api')->post('telegram/webhook', TelegramWebhookController::class)->name('webhook')->withoutMiddleware(VerifyCsrfToken::class);
Route::match(['get', 'head','post'],'trello/webhook', [TrelloWebhookController::class, 'handleWebhook'])->middleware('api')->withoutMiddleware(VerifyCsrfToken::class);
Route::post('trello/auth_complete', [TrelloWebhookController::class,'authComplete'])->middleware('api')->withoutMiddleware(VerifyCsrfToken::class);

Route::get('trello/auth_callback', function (Request $request){
return view('components.trello');
})->middleware('api')->withoutMiddleware(VerifyCsrfToken::class);
