<?php

namespace App\Http\Controllers;

use App\Models\Denomination;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Religion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Organization::query();

        if ($request->filled('record_number')) {
            $query->where(
                'id',
                $request->input('record_number')
            );
        }

        $organizations = $query
            ->orderBy('id')
            ->paginate(5)
            ->withQueryString();

        return view('organizations.manage.index', [
            'organizations' => $organizations,
        ]);
    }


    public function create(): View
    {
        $religions = Religion::orderBy('name')->get();

        $denominations = Denomination::orderBy('name')->get();

        $locations = Location::orderBy('name')->get();

        return view('organizations.manage.create', [
            'religions' => $religions,
            'denominations' => $denominations,
            'locations' => $locations,
        ]);
    }


    public function store(Request $request): RedirectResponse
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

                'location_id' => [
                    'required',
                    'exists:locations,id',
                ],

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],

                'address' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'website' => [
                    'nullable',
                    'url',
                    'max:255',
                ],

                'telephone' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],

                'head' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'photo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
            ],
            [
                'religion_id.required' =>
                    'Please select a religion.',

                'denomination_id.required' =>
                    'Please select a denomination.',

                'location_id.required' =>
                    'Please select a London location.',

                'name.required' =>
                    'Please enter the organisation name.',

                'address.required' =>
                    'Please enter the organisation address.',
            ]
        );


        /*
         * Check that the denomination belongs
         * to the selected religion.
         */
        $denomination = Denomination::findOrFail(
            $validated['denomination_id']
        );

        abort_unless(
            $denomination->religion_id ==
                $validated['religion_id'],
            422,
            'The selected denomination does not belong to the selected religion.'
        );


        /*
         * Upload the optional organisation photo.
         */
        $photoPath = null;

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            $fileName =
                time() . '_' . $photo->getClientOriginalName();

            $photo->move(
                public_path('images/organisations'),
                $fileName
            );

            $photoPath =
                'images/organisations/' . $fileName;
        }


        /*
         * Religion ID is deliberately NOT saved
         * in the organizations table.
         */
        $organization = Organization::create([
            'name' => $validated['name'],

            'denomination_id' =>
                $validated['denomination_id'],

            'location_id' =>
                $validated['location_id'],

            'description' =>
                $validated['description'] ?? null,

            'address' =>
                $validated['address'],

            'website' =>
                $validated['website'] ?? null,

            'telephone' =>
                $validated['telephone'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            'head' =>
                $validated['head'] ?? null,

            'photo' =>
                $photoPath,
        ]);


        return redirect()
            ->route('organizations.manage.index')
            ->with(
                'success',
                'Organisation #' .
                $organization->id .
                ' was created successfully.'
            );
    }


    public function edit(
        Organization $organization
    ): View {
        $organization->load([
            'denomination.religion',
            'location',
        ]);

        $religions = Religion::orderBy('name')->get();

        $denominations = Denomination::orderBy('name')->get();

        $locations = Location::orderBy('name')->get();

        return view('organizations.manage.edit', [
            'organization' => $organization,
            'religions' => $religions,
            'denominations' => $denominations,
            'locations' => $locations,
        ]);
    }


    public function update(
        Request $request,
        Organization $organization
    ): RedirectResponse {
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

                'location_id' => [
                    'required',
                    'exists:locations,id',
                ],

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],

                'address' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'website' => [
                    'nullable',
                    'url',
                    'max:255',
                ],

                'telephone' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],

                'head' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'photo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
            ],
            [
                'religion_id.required' =>
                    'Please select a religion.',

                'denomination_id.required' =>
                    'Please select a denomination.',

                'location_id.required' =>
                    'Please select a London location.',

                'name.required' =>
                    'Please enter the organisation name.',

                'address.required' =>
                    'Please enter the organisation address.',
            ]
        );


        /*
        * Confirm that the selected denomination
        * belongs to the selected religion.
        */
        $denomination = Denomination::findOrFail(
            $validated['denomination_id']
        );

        abort_unless(
            $denomination->religion_id ==
                $validated['religion_id'],
            422,
            'The selected denomination does not belong to the selected religion.'
        );


        /*
        * Keep the existing photo unless
        * a replacement is uploaded.
        */
        $photoPath = $organization->photo;

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            $fileName =
                time() . '_' . $photo->getClientOriginalName();

            $photo->move(
                public_path('images/organisations'),
                $fileName
            );

            $photoPath =
                'images/organisations/' . $fileName;
        }


        $organization->update([
            'name' => $validated['name'],

            'denomination_id' =>
                $validated['denomination_id'],

            'location_id' =>
                $validated['location_id'],

            'description' =>
                $validated['description'] ?? null,

            'address' =>
                $validated['address'],

            'website' =>
                $validated['website'] ?? null,

            'telephone' =>
                $validated['telephone'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            'head' =>
                $validated['head'] ?? null,

            'photo' =>
                $photoPath,
        ]);


        return redirect()
            ->route('organizations.manage.index')
            ->with(
                'success',
                'Organisation #' .
                $organization->id .
                ' was updated successfully.'
            );
    } 
    
    public function destroy(
            Organization $organization
        ): RedirectResponse {

            $organizationId = $organization->id;
            $organizationName = $organization->name;

            $organization->delete();

            return redirect()
                ->route('organizations.manage.index')
                ->with(
                    'success',
                    'Organisation #' .
                    $organizationId .
                    ' (' .
                    $organizationName .
                    ') was archived successfully.'
                );
        }  

    public function archived(Request $request): View
    {
        $query = Organization::onlyTrashed();

        if ($request->filled('record_number')) {
            $query->where(
                'id',
                $request->input('record_number')
            );
        }

        $organizations = $query
            ->orderBy('id')
            ->paginate(5)
            ->withQueryString();

        return view('organizations.manage.archived', [
            'organizations' => $organizations,
        ]);
    }


    public function restore(int $organization): RedirectResponse
    {
        $organization = Organization::onlyTrashed()
            ->findOrFail($organization);

        $organization->restore();

        return redirect()
            ->route('organizations.manage.archived')
            ->with(
                'success',
                'Organisation #' .
                $organization->id .
                ' (' .
                $organization->name .
                ') was restored successfully.'
            );
    }

}