<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrivilegePackageMasterRequest extends FormRequest
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
                    'privileges_packages' => 'required|array|max:30',
                        'privileges_packages.*.pc_privileges_action_name' => 'required|string',
                    // 'pc_sales_packages_id' => 'required|integer'
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
                break;
            default:
                return [];
                break;
       }
    }
}
