<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\Actions;
use Telegram\Bot\BotsManager;

class TelegramWebhookController extends Controller
{

    private BotsManager $botsManager;


    public function __construct(
        BotsManager $botsManager
    ) {
        $this->botsManager = $botsManager;
    }



    public function __invoke(Request $request)
    {
        $webhook = $this->botsManager->bot()
            ->commandsHandler(true);

        $message = $webhook->getMessage();

        $bot = $this->botsManager->bot();


        return response(null, Response::HTTP_OK);
    }

}
