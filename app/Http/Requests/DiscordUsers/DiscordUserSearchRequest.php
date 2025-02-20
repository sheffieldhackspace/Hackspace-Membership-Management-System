<?php

namespace App\Http\Requests\DiscordUsers;

use Illuminate\Foundation\Http\FormRequest;

class DiscordUserSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'term' => ['string', 'required', 'min:2'],
            'limit' => ['integer', 'required', 'min:1', 'max:100'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->mergeIfMissing([
            'limit' => 5,
        ]);
    }
}
