<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
        $memberId = $this->route('member')?->id;

        return [
            'uuid' => ['nullable', 'string', 'unique:members,uuid,' . $memberId],
            'user_id' => ['required', 'exists:users,id'],
            'county_id' => ['required', 'exists:counties,id'],
            'constituency_id' => ['required', 'exists:constituencies,id'],
            'ward_id' => ['required', 'exists:wards,id'],
            'passport_number' => ['nullable', 'string', 'max:50', 'unique:members,passport_number,' . $memberId],
            'national_identification_number' => ['nullable', 'string', 'max:50', 'unique:members,national_identification_number,' . $memberId],
            'party_membership_number' => ['required', 'string', 'max:50', 'unique:members,party_membership_number,' . $memberId],
            'configuration' => ['nullable', 'json'],
            'is_featured' => ['boolean'],
            'is_synced' => ['boolean'],
            'metadata' => ['nullable', 'json'],
            'last_verified_at' => ['nullable', 'date'],
            'verified_by' => ['nullable', 'exists:users,id'],
        ];
    }
}
