<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\Formlequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends Formlequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Email is not updatable, so we don't validate it
        ];
    }
}
