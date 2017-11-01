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



/**
 * Api Doc
 */
Route::get('/doc', '\App\Api\Controllers\ApiDoc@index');



$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
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
     * WeChat Notify Url
     */
    $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {
        $api->group(['prefix' => 'pay'], function ($api) {
            $api->post('pay_notify_url', 'WxPayController@payNotifyUrl');
            $api->post('notify_url', 'WxPayController@notifyUrl');
            $api->post('video_buy_notify_url', 'WxPayController@videoBuyNotifyUrl');
        });
    });
});


/**
 * 响应微信服务器
 */
Route::get('wx', 'WxAuthController@index');
