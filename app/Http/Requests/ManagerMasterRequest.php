<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManagerMasterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /*
    public function all($keys = null)
    {
        $data = parent::all($keys);

        switch ($this->method()) {
            case 'PATCH':
            case 'PUT':
                // $data['user_id'] = $this->route('manager');
                break;
            default:
                break;
        }
        return $data;
    }
    */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // you can also customize your validation for different methods as below
        switch ($this->method()){
            case 'POST':
                return [
                    // 'language_main' => 'required|json',
                    'user_id' => 'required|exists:us_users,id',
                    'role_id' => 'required|exists:pc_roles,id',
                ];
                break;
            case 'GET':
            case 'HEAD':
                return [];
                break;
            case 'DELETE':
                return [];
                break;
            case 'PATCH':
            case 'PUT':
                return [
                    // 'language_main' => 'required|json',
                    'user_id' => 'exists:us_users,id',
                    'role_id' => 'exists:pc_roles,id',
                ];
                break;
            default:
                return [];
                break;
       }

    }
}
