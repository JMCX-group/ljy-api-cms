<?php

namespace App\Api\Controllers;

use App\Http\Helper\EasyWeChat;

class WxAuthController extends BaseController
{
    public function index()
    {
        // 将响应输出
        return EasyWeChat::getResponse();
    }
}
