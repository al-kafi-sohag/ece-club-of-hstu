<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:4|confirmed',
            'password_confirmation' => 'required|string|min:4',
            'token' => 'required|string',
        ];
    }
}
