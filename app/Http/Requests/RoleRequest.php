<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
        // you can also customize your validation for different methods as below
        switch ($this->method()){
            case 'POST':
                return [
                    // 'language_main' => 'required|json',
                    'language_main.name' => 'required|string|max:45',
                    'language_main.description' => 'required|string|max:200',
                    'language_secondary' => 'array|max:10',
                        'language_secondary.*.name' => 'required|string|max:45',
                        'language_secondary.*.description' => 'required|string|max:200',
                        'language_secondary.*.language' => 'required|string|max:3'
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
                    'language_main.name' => 'required|string|max:45',
                    'language_main.description' => 'required|string|max:200',
                    'language_main.status' => 'integer|in:0,1,2',
                    'language_secondary' => 'array|max:10',
                        'language_secondary.*.name' => 'string|max:45',
                        'language_secondary.*.description' => 'string|max:200',
                        'language_secondary.*.language' => 'string|max:3'
                ];
                break;
            default:
                return [];
                break;
       }
    }
}
