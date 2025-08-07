<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persons;

class PersonsSeeder extends Seeder
{
    public function run(): void
    {
        $people = [
            ['title' => 'Mr',  'first_name' => 'John',  'initial' => null, 'last_name' => 'Smith'],
            ['title' => 'Mrs', 'first_name' => 'Jane',  'initial' => null, 'last_name' => 'Smith'],
            ['title' => 'Mister', 'first_name' => 'John', 'initial' => null, 'last_name' => 'Doe'],
            ['title' => 'Mr',  'first_name' => 'Bob',   'initial' => null, 'last_name' => 'Lawblaw'],
            ['title' => 'Mr and Mrs', 'first_name' => null, 'initial' => null, 'last_name' => 'Smith'],
            ['title' => 'Mr',  'first_name' => 'Craig', 'initial' => null, 'last_name' => 'Charles'],
            ['title' => 'Mr',  'first_name' => null,    'initial' => 'M',  'last_name' => 'Mackie'],
            ['title' => 'Mrs', 'first_name' => 'Jane',  'initial' => null, 'last_name' => 'McMaster'],
            ['title' => 'Mr Tom Staff and Mr John', 'first_name' => null, 'initial' => null, 'last_name' => 'Doe'],
            ['title' => 'Dr',  'first_name' => null,    'initial' => 'P',  'last_name' => 'Gunn'],
            ['title' => 'Dr & Mrs', 'first_name' => 'Joe', 'initial' => null, 'last_name' => 'Bloggs'],
            ['title' => 'Ms',  'first_name' => 'Claire','initial' => null, 'last_name' => 'Robbo'],
            ['title' => 'Prof','first_name' => 'Alex',  'initial' => null, 'last_name' => 'Brogan'],
            ['title' => 'Mrs', 'first_name' => 'Faye',  'initial' => null, 'last_name' => 'Hughes-Eastwood'],
            ['title' => 'Mr',  'first_name' => null,    'initial' => 'F.', 'last_name' => 'Fredrickson'],
        ];

        foreach ($people as $person) {
            Persons::create($person);
        }
    }
}
