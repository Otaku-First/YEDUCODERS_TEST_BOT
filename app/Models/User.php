<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'telegram_id',
        'trello_id',


    ];



}
