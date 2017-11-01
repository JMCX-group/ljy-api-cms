<?php

namespace App\Http\Controllers;

use App\Http\Helper\EasyWeChat;

class WxAuthController extends Controller
{
    public function index()
    {
        // 将响应输出
        return EasyWeChat::getResponse();
    }
}
