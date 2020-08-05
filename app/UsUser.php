<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class UsUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'us_users';
    // protected $guard = 'api';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', "lastname", 'email', 'password', "phone", "address", "birthdate", "genere", "photo", "remember_token", "date_add", "date_upd", "user_upd", "pc_countries_id"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /*
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    */

    /* ************************************************************************************************************************************/
    /* ***************************************************RELATIONSHIPS******************************************************************/
    /* ************************************************************************************************************************************/


    /**
     * Many to Many with us_socialnetworks
     *
     *
     */
    public function us_socialnetworks()
    {
        return $this->belongsToMany('App\UsSocialnetworks', 'us_userlogins', 'us_users_id', 'us_socialnetworks_id');
    }

    /**
     * Many to Many with pc_users_roles, manager
     *
     *
     */
    public function pc_users_roles() {
        return $this->belongsToMany('App\PcRole', 'pc_users_roles', 'us_users_id', 'pc_roles_id');
    }

    /* ************************************************************************************************************************************/
    /* ******************************************************CONFIGURATIONS****************************************************************/
    /* ************************************************************************************************************************************/

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return static
    */
    public static function create(array $attributes = [])
    {
        $model = new static($attributes);
        $model = self::defineDateAdd($model);
        $model->save();
        return $model;
    }

    /**
     * defineDateUpd
     *
     * @param  array $model
     * @return array
     */
    public static function defineDateUpd(array $model) {
        $model['date_upd'] = Carbon::now()->format('Y-m-d H:i:s');
        if(@Auth::id()) {
            $model['user_upd'] = @Auth::id();
        }
        return $model;
    }

    /**
     * Agregar fecha y usuario a la tabla
     *
     * @param  App\UsUser $array
     * @return App\UsUser
     */
    public static function defineDateAdd($model) {
        $model->date_add = Carbon::now()->format('Y-m-d H:i:s');

        if(@Auth::id()) {
            $model->user_add = @Auth::id();
        }

        return $model;
    }

    /* ************************************************************************************************************************************/
    /* ******************************************************* SCOPE *********************************************************************/
    /* ************************************************************************************************************************************/

    /**
     * scope es un a method de Model Eloquent
     * Obtener el usuario con su rol y agregar el sitio
     *
     * @param  App\UsUser $query
     * @param  int $user_id
     * @return App\UsUser
     */
    public function scopeGetDataUserRole($query, $user_id) {
        $result = $query->select('id', 'firstname', 'lastname')
                        ->where('id', $user_id)
                        ->with(['pc_users_roles' => function($query) {
                            return $query->select('id', 'name', 'description');
                        }])
                        ->first()
                        ;

        if(@$result) {

            // Aqui Invocar a la API de sites para obtener y unir los lugares con los usuarios con rol.


            return $result;
        }

        return $result; // abort(Response::HTTP_FORBIDDEN, "Query error scopeGetDataUserRole.");;

    }


    /**
     * scope es un method de Model Eloquent
     * Obtener todos los usuarios con roles de master o admins
     *
     * @param  App\UsUser $query
     * @return App\UsUser
     */
    public function scopeGetFullDataUserRole($query) {
        $result = $query->whereHas('pc_users_roles', function($query) {
                            return true;
                        })
                        ->with('pc_users_roles')
                        ->paginate(100)
                        ;

        if(@$result) {

            // Aqui Invocar a la API de sites para obtener y unir los lugares con los usuarios con rol.
            return $result;
        }

        return $result; // abort(Response::HTTP_FORBIDDEN, "Query error scopeGetFullDataUserRole.");
    }


    /**
     * registrar usuario con rol para administración de sitio
     *
     * @param  App\UsUser $query
     * @param  int $user_id
     * @param  int $role_id
     * @param  int $site
     *
     */
    public function scopeStoreRoleAdmin($query, int $user_id, int $role_id, int $site) {

        $existRolMaster = $query->whereHas('pc_users_roles', function($query) use ($user_id) {
                                    return $query->where('pc_users_roles.us_users_id', $user_id)->whereNull('pc_users_roles.pc_sites_id');
                                })->count();

        if($existRolMaster == "0") {
            $existRolAdmin = self::whereHas('pc_users_roles', function($query) use ($site, $user_id, $role_id) {
                                    return $query->where('pc_users_roles.pc_roles_id', $role_id)->where('pc_users_roles.us_users_id', $user_id)->where('pc_users_roles.pc_sites_id', $site);
                                })->count();

            if($existRolAdmin == "0") {
                self::findOrFail($user_id)->pc_users_roles()->attach($role_id, ['pc_sites_id' => $site, 'user_add' => Auth::id(), 'date_add' => Carbon::now()->format('Y-m-d H:i:s')]);
            } else {
                abort(400, "The data was already recorded.");
            }
        } else {
            abort(400, "The data was already recorded.");
        }
    }

}
