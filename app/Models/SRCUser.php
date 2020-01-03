<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SRCUser extends Model
{
    public function trackers()
    {
        return $this->hasMany('\App\Models\Tracker', 'src_user_id');
    }

    protected $fillable = [
        'src_name', 'src_id'
    ];

    protected $table = "t_src_user";
}
