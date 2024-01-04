<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|string|max:255|unique:users',
            'password' => 'required|max:255',
            'role'=> 'required|in:admin,user',
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Login is required!',
            'password.required' => 'Password is required!',
            'role.required' => 'Role is required!',
            'role.in' => 'Role must be admin or user!',
        ];
    }
}
