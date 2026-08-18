<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
            'auto_follow_tasks' => ['nullable', 'boolean'],
            // WhatsApp group share link (external URL). Optional because not all admins use it.
            'whatsapp_group_url' => ['nullable', 'string', 'max:2048'],
            // Email is not updatable, so we don't validate it
        ];
    }
}
