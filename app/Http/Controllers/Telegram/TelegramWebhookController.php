<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\User\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\BotsManager;

class TelegramWebhookController extends Controller
{

    private BotsManager $botsManager;

    private TelegramService $telegramService;


    public function __construct(
        BotsManager $botsManager,
        TelegramService $telegramService
    ) {
        $this->botsManager = $botsManager;
        $this->telegramService = $telegramService;
    }



    public function __invoke(Request $request)
    {
        $webhook = $this->botsManager->bot()
            ->commandsHandler(true);

        $message = $webhook->getMessage();
        $bot = $this->botsManager->bot();



        if ($webhook->callbackQuery !== null && $webhook['callback_query']['data'] == '/report'){
            $this->telegramService->sendTaskStatusReport($message['chat']['id'],$webhook['callback_query']['id']);
        }



        return response(null, Response::HTTP_OK);
    }

}
