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
            'postalAddress.line1' => ['required_with:postalAddress', 'string', 'max:255'],
            'postalAddress.line2' => ['string', 'nullable', 'max:255'],
            'postalAddress.line3' => ['string', 'nullable', 'max:255'],
            'postalAddress.city' => ['string', 'nullable', 'max:255'],
            'postalAddress.county' => ['string', 'nullable', 'max:255'],
            'postalAddress.postcode' => ['required_with:postalAddress', 'string', 'max:255'],
            'membershipType' => [
                'required',
                Rule::enum(MembershipType::class),
            ],
            'trustee' => [
                'required',
                'boolean',
            ],
            'discordUserId' => [
                'present',
                'nullable',
                'string',
                'exists:discord_users,id',
                'max:255',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->exists('postalAddress')) {
            $this->mergeIfMissing([
                'postalAddress.line1' => '',
                'postalAddress.line2' => '',
                'postalAddress.line3' => '',
                'postalAddress.city' => '',
                'postalAddress.county' => '',
                'postalAddress.postcode' => '',
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

    public function messages(): array
    {
        return [
            'postalAddress.line1.required_with' => 'Address Line 1 is required',
            'postalAddress.postcode.required_with' => 'A postcode is required',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'name',
            'knownAs' => 'known as',
            'emailAddresses.*.emailAddress' => 'email address',
            'emailAddresses.*.isPrimary' => 'primary email Address',
            'emailAddresses' => 'email addresses',
            'postalAddress' => 'address',
            'postalAddress.line1' => 'address line 1',
            'postalAddress.line2' => 'address line 2',
            'postalAddress.line3' => 'address line 3',
            'postalAddress.city' => 'city',
            'postalAddress.county' => 'county',
            'postalAddress.postcode' => 'postcode',
            'membershipType' => 'membership type',
            'trustee' => 'trustee',
        ];
    }
}
