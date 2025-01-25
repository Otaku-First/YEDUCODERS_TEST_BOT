<?php

namespace App\Services\User;



use App\Services\ReportService;
use Telegram\Bot\BotsManager;

use Telegram\Bot\Api;

class TelegramService
{
    private Api $botsManager;

    protected $telegramGroupId;

    private UserService $userService;
    protected ReportService $reportService;

    public function __construct(BotsManager $botsManager, UserService $userService, ReportService $reportService)
    {
        $this->botsManager = $botsManager->bot();
        $this->telegramGroupId = config('telegram.bots.mybot.group_id');
        $this->userService = $userService;
        $this->reportService = $reportService;
    }

    public function sendTaskStatusReport(int $chatId,int $callbackQueryId): void
    {

        $users = $this->userService->getUsersWithTrelloId();

        if ($users->isEmpty()) {
            $this->botsManager->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Немає учасників з підключеним Trello акаунтом.',
            ]);
            return;
        }

        $report = $this->reportService->generateTaskStatusReport($users);

        $this->botsManager->answerCallbackQuery([
            'callback_query_id' => $callbackQueryId,
        ]);

        $this->botsManager->sendMessage([
            'chat_id' => $chatId,
            'text' => $report,
            'parse_mode' => 'HTML',
        ]);

    }




}
