<?php

namespace App\Http\Requests\Api;


class UserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'iv' => 'required|string',
            'encryptData' => 'required|string',
        ];
    }
}
