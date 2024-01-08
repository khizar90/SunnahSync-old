<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type' => 'required',
            'full_name' => 'required',
            'email' => 'required|unique:users,email',
            'country_code' => 'required',
            'phone' => 'required',
            'password' => 'required|min:6',
        ];
    }
    public function messages()
    {
        return [
            'type.required' => 'Please enter the Type',
            'full_name.required' => 'Please enter the Full Name',
            'email.required' => 'Please enter the Email Address',
            'email.unique' => 'Email Address is already registered',
            'email.email' => 'Please enter a valid Email Address',
            'country_code.required' => 'Please enter the Country code',
            'phone.required' => 'Please enter the Phone number',
            'password.required' => 'Please enter the Password',
            'password.min' => 'Please enter atleast 6 characters in Password',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        $errorMessage = implode(', ', $validator->errors()->all());

        throw new HttpResponseException(response()->json([
            'status'   => false,
            'action' => $errorMessage
        ]));
    }
}
