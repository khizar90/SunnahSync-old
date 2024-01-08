<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginReqeust extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'password' => 'required|'
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Please enter the Email Address',
            'email.email' => 'Please enter a valid Email Address',
            'email.exists' => 'The Email Adress is not registered ',
            'password.required' => 'Please enter the Phone number',
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
