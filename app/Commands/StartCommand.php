<?php

namespace App\Commands;

use App\Services\User\DTO\CreateUserDTO;
use App\Services\User\TelegramService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Crypt;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';

    protected string $description = 'Start command';

    private UserService $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function handle()
    {
        $telegram = $this->getTelegram();
        $update = $this->getUpdate();
        $message = $update->getMessage();
        $user = $message->getFrom();

        $userId = $user->getId();
        $firstName = $user->getFirstName();

        $this->userService->createUser(CreateUserDTO::from([
            'name' => $firstName,
            'telegram_id' => $userId
        ]));


        $this->replyWithChatAction([
            'action' => Actions::TYPING,
        ]);


        $replyMessage = [
            'text' => 'Привіт, ' . $firstName,
        ];


        if ($message['chat'] && $this->isUserInGroup($telegram,$userId)) {
            $initialKeyboard = $this->initialKeyboard($userId, $message['chat']['type']);
            if ($initialKeyboard) {
                $replyMessage['reply_markup'] = $initialKeyboard;
            }

        }

        $this->replyWithMessage($replyMessage);
    }


    private function initialKeyboard(int $chatId, string $chatType)
    {
        $state = Crypt::encrypt($chatId);
        $appUrl = config('app.url');
        $authUrl = "https://trello.com/1/authorize?expiration=1day&name=TelegramBot&scope=read,write&response_type=token&key=" . config(
                'trello.api_key'
            ) . "&callback_method=fragment&return_url={$appUrl}/trello/auth_callback?state={$state}";

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => 'Звіт', 'callback_data' => '/report']),
        ]);

        if ($chatType == 'private') {
            return
                $keyboard
                    ->row([
                        Keyboard::inlineButton(['text' => 'Підключити Trello акаунт', 'url' => $authUrl]),
                    ]);
        } else {
            if ($chatType == 'supergroup') {
                return $keyboard;
            }
        }

        return false;
    }

    private function isUserInGroup(Api $telegram,$userId)
    {
        try {
            $response = $telegram->getChatMember([
                'chat_id' => config('telegram.bots.mybot.group_id'),
                'user_id' => $userId,
            ]);

            $status = $response['status'];


            return in_array($status, ['member', 'administrator', 'creator']);
        } catch (\Exception $e) {
            return false;
        }
    }

}
