<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class PrivilegeRequest extends FormRequest
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
                    'action_name' => 'array',
                    'action_name.*.pc_privileges_action_name' => [
                        'string',
                        'exists:pc_privileges_actions,action_name'
                    ]
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
                return [];
                break;
            default:
                return [];
                break;
       }

    }
}
