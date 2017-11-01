<?php

namespace App\Models\cms;

use Illuminate\Database\Eloquent\Model;

class WxUser extends Model
{
    protected $table = "wx_users";

    protected $fillable = [
        'id',
        'name',
        'open_id',
        'nickname',
        'head_img_url',
        'profession',
        'total_score',
        'status'
    ];
}
