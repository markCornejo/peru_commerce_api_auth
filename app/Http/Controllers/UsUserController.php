<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\UsUser;
use App\Http\Requests\RegisterUserRequest;
use App\Traits\ApiResponserGateway;
use App\Services\UserService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UsUserController extends Controller
{

    use ApiResponserGateway;

    private $_serviceUser;

    public function __construct(UserService $userService)
    {
        $this->_serviceUser = $userService;
    }

    public function register(RegisterUserRequest $request){

        $input = $request->all();
        $input['password'] = Hash::make($request->password);
        $input['date_add'] = Carbon::now()->format('Y-m-d');
        // var_dump($input); exit;
        $user = UsUser::create($input);

        return $this->successResponse(true, $user);

    }

    public function login(LoginRequest $request) {

        // $user = UsUser::whereEmail($request->email)->first();
        $data = $request->only(['email', 'password']);
        $loginre = $this->_serviceUser->login($data);

        if($loginre) {
            $user = Auth::user();
            $user->token = $loginre;
            return $this->successResponse(true, $user);
        } else {
            return $this->successResponse(false, [], 'Invalid credentials');
        }

    }

    public function logout(Request $request){

        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return $this->successResponse(true, [], 'Disconnected');
        }else{
            return $this->successResponse(false, [], 'Problems disconnecting');
        }

    }
}
