<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * 后台管理 : 首页 | 用户管理 | 菜单管理 | 角色管理 | 权限管理
 */
Route::group(['prefix' => 'cms', 'namespace' => 'Backend', 'middleware' => ['auth', 'Entrust']], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'IndexController@index']);

    Route::resource('user', 'UserController');
    Route::resource('menu', 'MenuController');
    Route::resource('role', 'RoleController');
    Route::resource('permission', 'PermissionController');
});

Route::group(['prefix' => 'cms'], function () {
    Auth::routes();
});


/**
 * 业务分组
 */
Route::group(['namespace' => 'Business', 'middleware' => ['auth','Entrust']], function () {
    /**
     * 微信菜单管理
     */
    Route::resource('menu', 'MenuController');

    /**
     * 人员管理
     */
    Route::resource('wx-user', 'WxUserController');
});