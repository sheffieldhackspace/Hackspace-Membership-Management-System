<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class OnePrimaryEmailAddress implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $emailAddressData = collect($value);
        $primaryEmailAddressCount = $emailAddressData->filter(function ($emailAddress) {
            return $emailAddress['isPrimary'] == true;
        })->count();

        if ($primaryEmailAddressCount != 1) {
            $fail(
                'Only one primary email address allowed'
            );
        }
    }
}
