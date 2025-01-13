<?php

namespace Database\Factories;

use App\Models\PostalAddress;
use Faker\Provider\en_GB\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostalAddressFactory extends Factory
{
    protected $model = PostalAddress::class;

    public function definition(): array
    {
        $provider = new Address($this->faker);

        return [
            'line_1' => $provider->streetAddress(),
            'line_2' => $provider->streetName(),
            'line_3' => '',
            'city' => $provider->optional(0.7)->city(),
            'county' => '',
            'postcode' => $provider->postcode(),
        ];
    }
}
