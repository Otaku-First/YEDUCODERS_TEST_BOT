<?php

namespace App\Services\User\Repository;

use App\Models\User;
use App\Services\User\DTO\CreateUserDTO;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function create(CreateUserDTO $data): User;

    public function userExistByTelegramId(int $telegramId): bool;

    public function setTrelloId(int $telegramId, string $trello_id): int;

    public function getWithTrelloId(): Collection;
}
