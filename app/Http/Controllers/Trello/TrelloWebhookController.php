<?php

namespace App\Http\Controllers\Trello;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Trello\DTO\AuthCompleteDTO;
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


        if ($type === 'updateCard' || $type === 'createCard') {
            $this->trelloService->handleUpdateCard($action);
        }


        return response()->json(['status' => 'success']);
    }


    public function authComplete(AuthCompleteDTO $DTO)
    {
         $this->trelloService->trelloAuthComplete($DTO);
    }
}
