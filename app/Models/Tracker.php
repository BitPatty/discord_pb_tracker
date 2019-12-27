<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    public function webhook()
    {
        return $this->hasOne('\App\Models\Webhook', 'id', 'webhook_id');
    }

    protected $fillable = [
        'src_name', 'src_id', 'webhook_id', 'last_updated'
    ];

    protected $table = "t_tracker";
}
