<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Transformers\UserTransformer;
use Auth;

class UsersController extends Controller
{
    public function update(UserRequest $request)
    {
        $user = Auth::guard('api')->getUser();

        $iv = $request->input('iv');
        $encryptData = $request->input('encryptData');

        $miniProgram = \EasyWeChat::miniprogram();
        $decryptData = $miniProgram->encryptor->decryptData($user->session_key, $iv, $encryptData);

        $user->nickname = $decryptData['nickName'];
        $user->avatar_url = $decryptData['avatarUrl'];
        $user->gender = $decryptData['gender'];
        $user->city = $decryptData['city'];
        $user->province = $decryptData['province'];
        $user->country = $decryptData['country'];
        $user->save();

        $this->response->item($user,new UserTransformer());
    }
}
