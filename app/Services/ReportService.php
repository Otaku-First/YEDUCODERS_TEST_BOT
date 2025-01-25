<?php

namespace App\Services;

use App\Services\User\TrelloService;
use Illuminate\Support\Collection;

class ReportService
{

    protected TrelloService $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function generateTaskStatusReport(Collection $users): string
    {
        $report = "<b>Звіт по завданнях у роботі:</b>\n";

        foreach ($users as $user) {
            $taskCount = $this->trelloService->getTasksCount($user->trello_id);

            $report .= "👤 {$user->name} (Telegram ID: {$user->telegram_id}): {$taskCount} завдань\n";
        }

        return $report;
    }
}
