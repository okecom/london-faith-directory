<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Database\Seeder;

class ReligionSeeder extends Seeder
{
    public function run(): void
    {
        $religions = [
            'Christianity' => [
                'Anglican',
                'Roman Catholic',
                'Pentecostal',
                'Baptist',
                'Methodist',
                'Eastern Orthodox',
                'Oriental Orthodox',
                'Evangelical',
                'Other Christian',
            ],

            'Islam' => [
                'Sunni',
                'Shia',
                'Ahmadiyya',
                'Other Muslim',
            ],

            'Hinduism' => [
                'Vaishnavism',
                'Shaivism',
                'Swaminarayan',
                'Other Hindu',
            ],

            'Judaism' => [
                'Haredi',
                'Orthodox',
                'Masorti',
                'Reform',
                'Liberal',
            ],

            'Sikhism' => [
                'Sikh',
            ],

            'Buddhism' => [
                'Theravada',
                'Mahayana',
                'Tibetan',
                'Other Buddhist',
            ],

            'Other' => [
                'Other',
            ],
        ];

        foreach ($religions as $religionName => $denominations) {

            $religion = Religion::create([
                'name' => $religionName,
            ]);

            foreach ($denominations as $denominationName) {
                $religion->denominations()->create([
                    'name' => $denominationName,
                ]);
            }
        }
    }
}