<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $eventTypes = [
            'Worship Service',
            'Youth',
            'Men',
            'Women',
            'Choir',
            'Community Outreach',
            'Prayer',
            'Study / Education',
            'Social',
            'Other',
        ];

        foreach ($eventTypes as $eventType) {
            EventType::firstOrCreate([
                'name' => $eventType,
            ]);
        }
    }
}