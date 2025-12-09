<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:admins,email,' . auth()->guard('admin')->user()->id . '|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',

            'current_password' => 'nullable|string|min:4',
            'new_password' => 'string|min:4|required_if:current_password,!null',
            'confirm_password' => 'same:new_password|string|min:4|required_if:current_password,!null',
        ];
    }
}
