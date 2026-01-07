<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Middleware auth already protects this route
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'organization_verification_id' => ['nullable', 'exists:organization_verifications,id'],
            'organization' => ['nullable', 'string', 'max:255'], // For manual input if no verification selected
            'image' => ['nullable', 'image', 'max:2048'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}


