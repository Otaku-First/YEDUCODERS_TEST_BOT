<?php

namespace App\Http\Controllers;

use App\Services\User\TelegramService;
use App\Services\User\TrelloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrelloWebhookController extends Controller
{

    public TrelloService $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function handleWebhook(Request $request)
    {

        if ($request->isMethod('get')) {
            return response()->json(['message' => 'Webhook verified'], 200);
        }

        $action = $request->input('action');
        $type = $action['type'] ?? null;
        $data = $action['data'] ?? null;

        $taskName = $data['card']['name'] ?? null;

        Log::info('Card updated: ',$action);

        if ($type === 'updateCard' ||  $type === 'createCard') {

            $this->trelloService->handleUpdateCard($action);

            Log::info('Card updated: '.$taskName);
        }



        return response()->json(['status' => 'success']);
    }
}
