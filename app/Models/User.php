<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'discord_id', 'avatar_url', 'api_token'
    ];

    protected $hidden = [
        'remember_token', 'api_token',
    ];

    protected $table = "t_user";
}
