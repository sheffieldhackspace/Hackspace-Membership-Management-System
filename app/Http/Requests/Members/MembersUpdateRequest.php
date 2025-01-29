<?php

namespace App\Http\Requests\Members;

use App\Enums\MembershipType;
use App\Models\Member;
use App\Models\User;
use App\Rules\OnePrimaryEmailAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembersUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var ?Member $member */
        $member = $this->route('member');

        return [
            'name' => ['required', 'string', 'max:255'],
            'knownAs' => ['required', 'string', 'max:255'],
            'emailAddresses.*.emailAddress' => [
                'required',
                'email',
                'distinct',
                Rule::unique('email_addresses', 'email_address')->ignore($member?->id, 'member_id'),
                'max:255',
            ],
            'emailAddresses.*.isPrimary' => ['required', 'boolean'],
            'emailAddresses' => ['required', 'array', new OnePrimaryEmailAddress, 'min:1'],
            'postalAddress' => ['nullable', 'array'],
            'postalAddress.line1' => ['string', 'required_with:postalAddress', 'max:255'],
            'postalAddress.line2' => ['string', 'nullable', 'max:255'],
            'postalAddress.line3' => ['string', 'nullable', 'max:255'],
            'postalAddress.city' => ['string', 'nullable', 'max:255'],
            'postalAddress.county' => ['string', 'nullable', 'max:255'],
            'postalAddress.postcode' => ['string', 'required_with:postalAddress', 'max:255'],
            'membershipType' => [
                'required',
                Rule::enum(MembershipType::class),
            ],
            'trustee' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->exists('postalAddress')) {
            $this->merge([
                'postalAddress.line1' => $this->get('postalAddress.line1', ''),
                'postalAddress.line2' => $this->get('postalAddress.line2', ''),
                'postalAddress.line3' => $this->get('postalAddress.line3', ''),
                'postalAddress.city' => $this->get('postalAddress.city', ''),
                'postalAddress.county' => $this->get('postalAddress.county', ''),
                'postalAddress.postcode' => $this->get('postalAddress.postcode', ''),
            ]);
        }

        /** @var User $user */
        $user = $this->user();
        $member = Member::find($this->route('member'))->firstOrFail();

        if ($user->cant('changeMembershipType', $member)) {
            $this->merge([
                'membershipType' => $member->getMembershipType()->value,
                'trustee' => $member->getIsActiveTrustee(),
            ]);
        }

    }
}
