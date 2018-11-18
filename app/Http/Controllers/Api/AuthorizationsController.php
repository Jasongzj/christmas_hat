<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Models\User;
use Auth;

class AuthorizationsController extends Controller
{
    public function store(AuthorizationRequest $request)
    {
        $code = $request->input('code');

        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        $user = User::query()->where('openid', $data['openid'])->first();

        if (!$user) {
            $user = new User();
            $user->openid = $data['openid'];
            $user->session_key = $data['session_key'];
            $user->save();
        } else {
            $user->session_key = $data['session_key'];
            $user->save();
        }

        $token = Auth::guard('api')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }
}
