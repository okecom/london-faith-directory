<?php

namespace Database\Seeders;

use App\Models\Denomination;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Religion;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $christianity = Religion::where(
            'name',
            'Christianity'
        )->firstOrFail();

        $anglican = Denomination::where(
            'religion_id',
            $christianity->id
        )
            ->where('name', 'Anglican')
            ->firstOrFail();

        $westminster = Location::where(
            'name',
            'Westminster'
        )->firstOrFail();

        for ($i = 1; $i <= 13; $i++) {

            Organization::create([
                'name' => "Example Anglican Community {$i}",

                'denomination_id' => $anglican->id,

                'location_id' => $westminster->id,

                'description' =>
                    'A sample religious organisation used for development and testing.',

                'address' =>
                    "{$i} Example Street, Westminster, London",

                'website' =>
                    "https://example.org/organisation-{$i}",

                'telephone' =>
                    '020 0000 ' .
                    str_pad(
                        (string) $i,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),

                'email' =>
                    "organisation{$i}@example.org",

                'head' =>
                    "Example Leader {$i}",

                'photo' =>
                    'images/organisation-placeholder.jpg',
            ]);
        }
    }
}