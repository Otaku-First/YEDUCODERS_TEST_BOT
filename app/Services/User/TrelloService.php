<?php

namespace App\Services\User;

use App\Http\Controllers\Trello\DTO\AuthCompleteDTO;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Laravel\Facades\Telegram;

class TrelloService
{


    private $apiKey;
    private $apiToken;
    private $board_id;

    private array $initial_columns;

    protected Api $botsManager;
    protected UserService $userService;

    public function __construct(BotsManager $botsManager, UserService $userService)
    {
        $this->apiKey = config('trello.api_key');
        $this->apiToken = config('trello.api_token');
        $this->board_id = config('trello.board_id');
        $this->initial_columns = ['Done', 'InProgress'];
        $this->userService = $userService;
        $this->botsManager = $botsManager->bot();
    }

    public function setupWebhook(): string
    {
        $response = Http::post('https://api.trello.com/1/webhooks/', [
            'key' => $this->apiKey,
            'token' => $this->apiToken,
            'idModel' => $this->board_id,
            'callbackURL' => config('trello.webhook_url'),
        ]);


        if ($response->successful()) {
            foreach ($this->initial_columns as $column) {
                $this->createColumn($this->board_id, $column);
            }

            return 'Webhook successfully created!';
        } else {
            return 'Failed to create webhook: ' . $response->body();
        }
    }


    public function handleUpdateCard(array $action): void
    {
        $type = $action['type'] ?? null;
        $data = $action['data'] ?? null;
        $updatedBy = $action['memberCreator']['fullName'] ?? null;

        $taskName = $data['card']['name'] ?? null;
        $boardName = $data['board']['name'] ?? null;
        $fromColumn = $data['listBefore']['name'] ?? null;
        $toColumn = $data['listAfter']['name'] ?? null;

        $message = "";
        if ($type === 'updateCard') {
            $message = "📝 <b>Оновлення завдання в Trello</b>\n"
                . "🔹 <b>Назва завдання:</b> {$taskName}\n"
                . "👤 <b>Оновив:</b> {$updatedBy}\n"
                . "📋 <b>Дошка:</b> {$boardName}\n"
                . "⬅️ <b>Переміщено з:</b> {$fromColumn}\n"
                . "➡️ <b>Переміщено до:</b> {$toColumn}";
        } else {
            if ($type === 'createCard') {
                $toColumn = $data['list']['name'] ?? null;
                $message = "🆕 <b>Нове завдання створено в Trello</b>\n"
                    . "🔹 <b>Назва завдання:</b> {$taskName}\n"
                    . "👤 <b>Створив:</b> {$updatedBy}\n"
                    . "📋 <b>Дошка:</b> {$boardName}\n"
                    . "➡️ <b>Розміщено в колонці:</b> {$toColumn}";
            }
        }

        if ($toColumn) {
            $this->botsManager->sendMessage([
                'chat_id' => config('telegram.bots.mybot.group_id'),
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
        }
    }


    public function trelloAuthComplete(AuthCompleteDTO $DTO): void
    {
        try {
            $decryptedTelegramUserID = Crypt::decrypt($DTO->state);

            $trelloApiUrl = "https://api.trello.com/1/members/me?" . http_build_query([
                    'key' => $this->apiKey,
                    'token' => $DTO->token,
                ]);

            $response = Http::get($trelloApiUrl);

            if ($response->successful()) {
                $userData = $response->json();

                $this->userService->setUserTrelloId($decryptedTelegramUserID, $userData['id']);

                Telegram::sendMessage([
                    'chat_id' => $decryptedTelegramUserID,
                    'text' => "Ваш Trello акаунт (@{$userData['username']}) успішно підключено!"
                ]);
            }
        } catch (DecryptException $e) {
            echo 'Помилка дешифрування: ', $e->getMessage();
        }
    }


    public function getTasksCount($trelloId): int
    {
        $listsUrl = "https://api.trello.com/1/boards/{$this->board_id}/lists";

        $listsResponse = Http::get($listsUrl, [
            'key' => $this->apiKey,
            'token' => $this->apiToken,
        ]);

        if (!$listsResponse->successful()) {
            Log::error('Не вдалося отримати списки з Trello', [
                'response' => $listsResponse->body(),
            ]);
            return 0;
        }


        $lists = $listsResponse->json();
        $inProgressListId = null;

        foreach ($lists as $list) {
            if (strtolower($list['name']) === 'inprogress') {
                $inProgressListId = $list['id'];
                break;
            }
        }

        if (!$inProgressListId) {
            Log::warning('Колонка "InProgress" не знайдена на дошці');
            return 0;
        }


        $cardsUrl = "https://api.trello.com/1/lists/{$inProgressListId}/cards";

        $cardsResponse = Http::get($cardsUrl, [
            'key' => $this->apiKey,
            'token' => $this->apiToken,
        ]);

        if (!$cardsResponse->successful()) {
            Log::error('Не вдалося отримати картки з колонки "InProgress"', [
                'response' => $cardsResponse->body(),
            ]);
            return 0;
        }


        $cards = $cardsResponse->json();
        $userCards = array_filter($cards, function ($card) use ($trelloId) {
            if (isset($card['idMembers']) && in_array($trelloId, $card['idMembers'])) {
                return true;
            }
            return false;
        });

        return count($userCards);
    }


    private function createColumn(string $boardId, string $columnName): void
    {
        $response = Http::post('https://api.trello.com/1/lists', [
            'key' => $this->apiKey,
            'token' => $this->apiToken,
            'idBoard' => $boardId,
            'name' => $columnName,
        ]);

        if (!$response->successful()) {
            Log::error('Failed to create column in Trello', [
                'board_id' => $boardId,
                'column_name' => $columnName,
                'response' => $response->body(),
            ]);
        }
    }
}
