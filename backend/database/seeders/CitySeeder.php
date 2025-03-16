<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Warszawa',
                'district' => 'Warszawa',
                'commune' => 'Warszawa',
                'voivodeship' => 'Mazowieckie',
            ],
            [
                'name' => 'Kraków',
                'district' => 'Kraków',
                'commune' => 'Kraków',
                'voivodeship' => 'Małopolskie',
            ],
            [
                'name' => 'Łódź',
                'district' => 'Łódź',
                'commune' => 'Łódź',
                'voivodeship' => 'Łódzkie',
            ],
            [
                'name' => 'Wrocław',
                'district' => 'Wrocław',
                'commune' => 'Wrocław',
                'voivodeship' => 'Dolnośląskie',
            ],
            [
                'name' => 'Poznań',
                'district' => 'Poznań',
                'commune' => 'Poznań',
                'voivodeship' => 'Wielkopolskie',
            ],
            [
                'name' => 'Gdańsk',
                'district' => 'Gdańsk',
                'commune' => 'Gdańsk',
                'voivodeship' => 'Pomorskie',
            ],
            [
                'name' => 'Szczecin',
                'district' => 'Szczecin',
                'commune' => 'Szczecin',
                'voivodeship' => 'Zachodniopomorskie',
            ],
            [
                'name' => 'Bydgoszcz',
                'district' => 'Bydgoszcz',
                'commune' => 'Bydgoszcz',
                'voivodeship' => 'Kujawsko-Pomorskie',
            ],
            [
                'name' => 'Lublin',
                'district' => 'Lublin',
                'commune' => 'Lublin',
                'voivodeship' => 'Lubelskie',
            ],
            [
                'name' => 'Katowice',
                'district' => 'Katowice',
                'commune' => 'Katowice',
                'voivodeship' => 'Śląskie',
            ],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->updateOrInsert(
                ['name' => $city['name']],
                [
                    'district' => $city['district'],
                    'commune' => $city['commune'],
                    'voivodeship' => $city['voivodeship'],
                ]
            );
        }
    }
}
