<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof \App\Models\User ? $user->id : $user;

        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password'      => ['nullable', 'confirmed', Password::defaults()],
            'role_id'       => ['nullable', 'integer', 'exists:roles,id'],
            'departemen_id' => ['nullable', 'integer', 'exists:departemens,id'],
            'lokasi_id'     => ['nullable', 'integer', 'exists:lokasi_units,id'],
        ];
    }
}
