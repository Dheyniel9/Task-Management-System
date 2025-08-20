<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    // Allow anyone to make this request (no restrictions)
    public function authorize(): bool
    {
        return true;
    }

    // Set the rules for what is required when registering
    public function rules(): array
    {
        return [
            // Name is required, must be text, and not too long
            'name' => ['required', 'string', 'max:255'],
            // Email is required, must be a valid email, not too long, and not already used
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // Password is required, must be confirmed (typed twice), and follow default password rules
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    // Custom error messages for some rules
    public function messages(): array
    {
        return [
            // If the email is already used, show this message
            'email.unique' => 'This email address is already registered.',
            // If the password confirmation does not match, show this message
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
