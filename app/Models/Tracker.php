<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    public function webhook()
    {
        return $this->belongsTo('\App\Models\Webhook', 'webhook_id', 'id');
    }

    protected $fillable = [
        'src_user_id', 'webhook_id', 'last_updated'
    ];

    protected $table = "t_tracker";
}
