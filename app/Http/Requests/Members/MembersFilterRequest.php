<?php

namespace App\Http\Requests\Members;

use App\Enums\MembershipType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembersFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['string', 'nullable'],
            'membership_type' => [Rule::enum(MembershipType::class), 'nullable'],
            'page' => ['integer', 'nullable'],
        ];
    }
}
