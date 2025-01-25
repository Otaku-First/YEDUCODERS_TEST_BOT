<?php

namespace App\Services\User\Repository\Eloquent;

use App\Models\User;
use App\Services\User\DTO\CreateUserDTO;
use App\Services\User\Repository\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{

    private User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }


    public function create(CreateUserDTO $data): User
    {
        return $this->model->query()->create($data->toArray());
    }

    public function userExistByTelegramId(int $telegramId): bool
    {
        return $this->model->query()->where('telegram_id', $telegramId)->get()->count() > 0;
    }

    public function setTrelloId(int $telegramId, string $trello_id): int
    {
        return $this->model->query()->where('telegram_id', $telegramId)->update(['trello_id' => $trello_id]);
    }

    public function getWithTrelloId(): Collection
    {
      return  $this->model->query()->whereNotNull('trello_id')->get();

    }



}
