<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RegisterUserRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        /*
        return [
            'name' =>'required|unique:users,name,'.$id
        ];
        */

        // you can also customize your validation for different methods as below
        switch ($this->method()){
            case 'POST':
                return [
                    'email' => 'required|email|max:50|unique:us_users,email',
                    'password' => 'required|required_with:password_confirmation|string|min:7|confirmed',
                    'firstname' => 'required|string',
                    'lastname' => 'string',
                    'phone' => 'digits_between:0,15',
                    'address' => 'string|max:100',
                    'birthdate' => 'date_format:Y-m-d|before:today',
                    'genere' => 'string|in:M,F,O',
                    'photo' => 'string|max:100',
                    'remember_token' => 'string',
                    'pc_countries_id' => 'exists:pc_countries,id',
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
                    'firstname' => 'required|string',
                    'lastname' => 'string',
                    'phone' => 'digits_between:0,15',
                    'address' => 'string|max:100',
                    'birthdate' => 'date_format:Y-m-d|before:today',
                    'genere' => 'string|in:M,F,O',
                    'photo' => 'string|max:100',
                    'pc_countries_id' => 'exists:pc_countries,id',
                ];
            break;
            default:
                return [];
            break;
       }
    }
}
