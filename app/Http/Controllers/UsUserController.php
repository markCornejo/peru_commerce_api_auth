<?php

namespace App\Http\Controllers;

use App\UsUser;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UsUser as UsUserResource;
use App\Services\UserService;
use App\Traits\ApiResponserGateway;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsUserController extends Controller
{

    use ApiResponserGateway;

    private $_serviceUser;

    public function __construct(UserService $userService)
    {
        $this->_serviceUser = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterUserRequest $request)
    {
        $user = null;
        DB::transaction(function () use ($request, &$user) {

            $input = $request->all();
            $input['password'] = Hash::make($request->password);

            $user = UsUser::create($input);
            $user->token = null;

            // registro en us_userlogins
            $user->us_socialnetworks()->attach(
                1,
                ['id_social' => 'peru_'.Str::random(20), 'us_users_email' => $request->email, 'user_add' => $user->id, 'date_add' => $user->date_add]
            );

            // registro en us_subscribes_and_follows
            // Aqui Invocar a la Api de Sites para subscribir al usuario con su SITE (¿Como trabajo los registros transaccionales con 2 apis?)

        });

        return $this->successResponse(true, new UsUserResource($user));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterUserRequest $request, UsUser $usUser, $user)
    {
        $user = Auth::id();
        DB::transaction(function () use ($request, $usUser, $user) {

            $input = $request->all();
            $input = $usUser->defineDateUpd($input); // definir fecha y usuario que actualiza el registro
            unset($input['email']); unset($input['password']); // se eliminar estos campos para no actualizarlos

            // actualizar un usuario
            $data = $usUser->findOrFail($user);
            $data->fill($input)->save(); // guardar

            // registro en us_subscribes_and_follows
            // Aqui Invocar a la Api de Sites para actualizar el subscribir del usuario con su SITE (¿Como trabajo los registros transaccionales con 2 apis?)
        });

        return $this->successResponse(true, []);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function login(LoginRequest $request) {
        // $user = UsUser::whereEmail($request->email)->first();
        $data = $request->only(['email', 'password']);
        $loginre = $this->_serviceUser->login($data);

        if($loginre) {
            $user = Auth::user();
            $user->token = $loginre;
            return $this->successResponse(true, new UsUserResource($user));
        }

        return $this->successResponse(false, [], 'Invalid credentials');

    }

    /**
     * cerrar sesion
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request){

        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return $this->successResponse(true, [], 'Disconnected');
        }

        return $this->successResponse(false, [], 'Problems disconnecting');
    }


    /**
     * Verificar token
     *
     * @return @return \Illuminate\Http\Response  true => token habilitado / UnAuthorization => token no habilitado
     */
    public function check(/*Request $request*/)
    {
        return response()->json(true);
    }


}
