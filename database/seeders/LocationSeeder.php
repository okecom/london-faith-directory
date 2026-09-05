<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Barking and Dagenham',
            'Barnet',
            'Bexley',
            'Brent',
            'Bromley',
            'Camden',
            'City of London',
            'Croydon',
            'Ealing',
            'Enfield',
            'Greenwich',
            'Hackney',
            'Hammersmith and Fulham',
            'Haringey',
            'Harrow',
            'Havering',
            'Hillingdon',
            'Hounslow',
            'Islington',
            'Kensington and Chelsea',
            'Kingston upon Thames',
            'Lambeth',
            'Lewisham',
            'Merton',
            'Newham',
            'Redbridge',
            'Richmond upon Thames',
            'Southwark',
            'Sutton',
            'Tower Hamlets',
            'Waltham Forest',
            'Wandsworth',
            'Westminster',
        ];

        foreach ($locations as $location) {
            Location::create([
                'name' => $location,
            ]);
        }
    }
}