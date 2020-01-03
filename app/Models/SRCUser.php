<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SRCUser extends Model
{
    protected $fillable = [
        'src_name', 'src_id'
    ];

    protected $table = "t_src_user";
}
