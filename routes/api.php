<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    /**
     * Api Doc
     */
    $api->get('/doc', '\App\Api\Controllers\ApiDoc@index');
    $api->group(['namespace' => 'App\Api\Controllers', 'middleware' => ['jwt.api.auth']], function ($api) {
        /**
         * Register & Login
         */
        $api->post('verify-code', 'AuthController@sendVerifyCode');
        $api->post('login', 'AuthController@authenticate');

        /**
         * Token Auth
         */
        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            // Init
            $api->group(['prefix' => 'init'], function ($api) {
//                $api->get('/', 'InitController@index');
            });
        });
    });

    /**
     * 响应微信服务器
     */
    $api->get('wx', '\App\Api\Controllers\WxAuthController@index');
    /**
     * WeChat Notify Url
     */
    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->group(['prefix' => 'pay'], function ($api) {
            $api->post('pay_notify_url', 'WxPayController@payNotifyUrl');
            $api->post('notify_url', 'WxPayController@notifyUrl');
            $api->post('video_buy_notify_url', 'WxPayController@videoBuyNotifyUrl');
        });
    });
});
