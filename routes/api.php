<?php

use Illuminate\Http\Request;

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

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
], function ($api) {
    // 登录接口节流
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function ($api) {
        // 登录认证接口
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
    });

    // 普通接口节流
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expries'),
    ], function ($api) {

        // 需要认证的接口
        $api->group([
            'middleware' => 'api.auth'
        ], function ($api) {
            // 更新用户信息
            $api->put('user', 'UsersController@update')
                ->name('api.user.update');

            // 人脸检测
            $api->post('face/detect', 'FaceController@detect')
                ->name('api.face.detect');
        });
    });
});
