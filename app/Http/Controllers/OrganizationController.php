<?php

namespace App\Http\Controllers;

use App\Models\Denomination;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Religion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function search(): View
    {
        $religions = Religion::orderBy('name')->get();

        $denominations = Denomination::orderBy('name')->get();

        $locations = Location::orderBy('name')->get();

        return view('organizations.search', [
            'religions' => $religions,
            'denominations' => $denominations,
            'locations' => $locations,
        ]);
    }


    public function results(Request $request): View
    {
        $validated = $request->validate(
            [
                'religion_id' => [
                    'required',
                    'exists:religions,id',
                ],

                'denomination_id' => [
                    'required',
                    'exists:denominations,id',
                ],

                'location' => [
                    'required',
                    'string',
                    'max:255',
                    'exists:locations,name',
                ],
            ],
            [
                'location.exists' =>
                    'Please select a valid London borough from the list.',
            ]
        );


        $religion = Religion::findOrFail(
            $validated['religion_id']
        );


        $denomination = Denomination::findOrFail(
            $validated['denomination_id']
        );


        /*
        * Ensure the denomination belongs
        * to the selected religion.
        */
        abort_unless(
            $denomination->religion_id === $religion->id,
            422,
            'The selected denomination does not belong to the selected religion.'
        );


        $locationName = trim(
            $validated['location']
        );


        $location = Location::where(
            'name',
            $locationName
        )->firstOrFail();


        $organizations = Organization::with([
                'denomination.religion',
                'location',
            ])
            ->where(
                'denomination_id',
                $denomination->id
            )
            ->where(
                'location_id',
                $location->id
            )
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();


        return view('organizations.results', [
            'religion' => $religion,
            'denomination' => $denomination,
            'location' => $location,
            'organizations' => $organizations,
        ]);
    }
}