<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persons>
 */
class PersonsFactory extends Factory
{
    protected $model = \App\Models\Persons::class;

    public function definition(): array
    {
        return [
            'title'      => $this->faker->title(),
            'first_name' => $this->faker->firstName(),
            'initial'    => strtoupper(substr($this->faker->firstName(), 0, 1)),
            'last_name'  => $this->faker->lastName(),
        ];
    }
}
