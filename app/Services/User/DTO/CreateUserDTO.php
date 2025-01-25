<?php

namespace App\Services\User\DTO;

use Spatie\LaravelData\Data;

class CreateUserDTO extends Data
{

    public string $name;
    public int $telegram_id;

}
