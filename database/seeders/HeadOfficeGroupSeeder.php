<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class HeadOfficeGroupSeeder extends Seeder
{
    public function run(): void
    {
        Organization::query()
            ->orderBy('id')
            ->each(function (Organization $organization) {

                Group::firstOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'name' => 'Head Office',
                    ],
                    [
                        'description' =>
                            'Main group for ' .
                            $organization->name,

                        'contact_name' =>
                            $organization->head,

                        'telephone' =>
                            $organization->telephone,

                        'email' =>
                            $organization->email,

                        'is_head_office' => true,
                    ]
                );
            });
    }
}