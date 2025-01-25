<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\User\DTO\CreateUserDTO;
use App\Services\User\Repository\Eloquent\UserRepository;
use App\Services\User\Repository\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UserService
{

    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    public function createUser(CreateUserDTO $DTO): User|false
    {
        if ($this->repository->userExistByTelegramId($DTO->telegram_id)) {
            Log::info('user already exist');

            return false;
        }


        return $this->repository->create($DTO);
    }

    public function setUserTrelloId(int $telegramId, string $trello_id): int
    {
        if (!$this->repository->userExistByTelegramId($telegramId)) {
            Log::info('user not exist');

            return false;
        }


        return $this->repository->setTrelloId($telegramId, $trello_id);
    }

    public function getUsersWithTrelloId() : Collection
    {
      return  $this->repository->getWithTrelloId();
    }

}
