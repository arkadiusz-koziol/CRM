<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                "name" => "BMW",
                "description" => "test description",
                "registration_number" => "DBLTEST123",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Mazda",
                "description" => "test description",
                "registration_number" => "DBLTEST321",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Volvo",
                "description" => "test description",
                "registration_number" => "DBLTEST312",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Opel",
                "description" => "test description",
                "registration_number" => "DBLTEST132",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Renault",
                "description" => "test description",
                "registration_number" => "DBLTEST213",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Alfa Romeo",
                "description" => "test description",
                "registration_number" => "DBLTEST231",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Ford",
                "description" => "test description",
                "registration_number" => "DBLTEST1234",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Porsche",
                "description" => "test description",
                "registration_number" => "DBLTEST1324",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Toyota",
                "description" => "test description",
                "registration_number" => "DBLTEST1432",
                "technical_details" => "test technical details"
            ],
            [
                "name" => "Audi",
                "description" => "test description",
                "registration_number" => "DBLTEST1423",
                "technical_details" => "test technical details"
            ],
        ];

        foreach ($cars as $car) {
            DB::table('cars')->updateOrInsert(
                ['registration_number' => $car['registration_number']],
                [
                    'name' => $car['name'],
                    'description' => $car['description'],
                    'technical_details' => $car['technical_details']
                ]
                );
        }
    }
}
