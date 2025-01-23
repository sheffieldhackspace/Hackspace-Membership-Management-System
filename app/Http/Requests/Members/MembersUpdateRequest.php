<?php

namespace App\Http\Requests\Members;

use App\Enums\MembershipType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembersUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'known_as' => ['required', 'string', 'max:255'],
            'emailAddresses.*.id' => ['uuid'],
            'emailAddresses.*.email' => ['required', 'email', 'max:255'],
            'emailAddresses.*.isPrimary' => ['required', 'boolean'],
            'postalAddress.line1' => ['nullable', 'string', 'max:255'],
            'postalAddress.line2' => ['nullable', 'string', 'max:255'],
            'postalAddress.line3' => ['nullable', 'string', 'max:255'],
            'postalAddress.city' => ['nullable', 'string', 'max:255'],
            'postalAddress.county' => ['nullable', 'string', 'max:255'],
            'postalAddress.postcode' => ['nullable', 'string', 'max:255'],
            'membership_type' => ['required', Rule::enum(MembershipType::class)],
            'trustee' => ['required', 'boolean'],
        ];
    }
}
