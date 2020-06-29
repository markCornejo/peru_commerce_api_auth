<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class LoginRequest extends FormRequest
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
                    'email' => 'required|email|max:50',
                    'password' => 'required|string|min:7',
                ];
            break;
            case 'PUT':
                return [
                    // validation for put method
                ];
            break;
            default:
                return [];
            break;
       }
    }
}
