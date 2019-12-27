<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    public function trackers() {
        return $this->hasMany('\App\Models\Tracker', 'webhook_id');
    }

    protected $fillable = [
        'url', 'manager_id', 'name', 'description', 'channel_id', 'guild_id', 'channel_name', 'guild_name', 'state', 'discord_id'
    ];

    protected $table = "t_webhook";
}
