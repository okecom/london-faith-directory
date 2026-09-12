<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ReligionSeeder::class,
            DenominationSeeder::class,
            LocationSeeder::class,
            OrganizationSeeder::class,

            EventTypeSeeder::class,
            HeadOfficeGroupSeeder::class,
        ]);
    }
}