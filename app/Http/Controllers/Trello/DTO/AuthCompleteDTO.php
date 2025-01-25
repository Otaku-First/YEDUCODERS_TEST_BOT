<?php

namespace App\Http\Controllers\Trello\DTO;

use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class AuthCompleteDTO extends Data
{

    #[Required]
    public string $state;
    #[Required]
    public string $token;


}
