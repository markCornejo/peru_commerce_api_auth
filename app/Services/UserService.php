<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserService
{

    /**
     * login
     *
     * @param  Array $data ['email', 'password']
     * @return void
     */
    public function login($data = [])
    {
        if (!Auth::attempt($data)) {
            return false;
        }

        $token = Auth::user()->createToken('peru commerce')->accessToken;

        return $token;
    }

}
