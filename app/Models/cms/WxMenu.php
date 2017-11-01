<?php

namespace App\Models\cms;

use Illuminate\Database\Eloquent\Model;

class WxMenu extends Model
{
    protected $table = "wx_menus";

    protected $fillable = [
        'name',
        'json'
    ];
}
