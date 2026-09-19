<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\DB;
use App\Models\Denomination;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Religion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize(
            'viewAny',
            Organization::class
        );

        $query = Organization::query();

        /*
         * Organisation Administrators may only see
         * their assigned organisation.
         *
         * Site Administrators may see all organisations.
         */
        if (
            $request->user()->role ===
            User::ROLE_ORGANISATION_ADMIN
        ) {
            $query->where(
                'id',
                $request->user()->organization_id
            );
        }

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
        /*
         * Only Site Administrators may create
         * new organisations.
         */
        $this->authorize(
            'create',
            Organization::class
        );

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
        /*
         * Protect against a forged direct POST.
         * Only Site Administrators may create organisations.
         */
        $this->authorize(
            'create',
            Organization::class
        );

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
                time() . '_' .
                $photo->getClientOriginalName();

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
         *
         * Create the organisation and its required
         * Head Office group in one transaction.
         */
        $organization = DB::transaction(
            function () use ($validated, $photoPath) {
                $organization = Organization::create([
                    'name' =>
                        $validated['name'],

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


                /*
                 * Every organisation must have
                 * one Head Office group.
                 */
                Group::create([
                    'organization_id' =>
                        $organization->id,

                    'name' =>
                        'Head Office',

                    'description' =>
                        'Main group for ' .
                        $organization->name,

                    'contact_name' =>
                        $organization->head,

                    'telephone' =>
                        $organization->telephone,

                    'email' =>
                        $organization->email,

                    'is_head_office' =>
                        true,
                ]);


                return $organization;
            }
        );


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
        /*
         * Site Administrators may edit any organisation.
         * Organisation Administrators may edit only
         * their assigned organisation.
         */
        $this->authorize(
            'update',
            $organization
        );

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
        /*
         * Authorize before validating or changing
         * any submitted data.
         */
        $this->authorize(
            'update',
            $organization
        );

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
                time() . '_' .
                $photo->getClientOriginalName();

            $photo->move(
                public_path('images/organisations'),
                $fileName
            );

            $photoPath =
                'images/organisations/' . $fileName;
        }


        $organization->update([
            'name' =>
                $validated['name'],

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
        /*
         * Organisation archiving is a Site
         * Administrator operation.
         */
        $this->authorize(
            'delete',
            $organization
        );

        $organizationId =
            $organization->id;

        $organizationName =
            $organization->name;

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
        /*
         * Organisation archive management is
         * restricted to Site Administrators.
         *
         * The create ability is intentionally used
         * here because OrganizationPolicy::create()
         * is Site Administrator only, while the
         * before() method grants Site Administrators.
         */
        $this->authorize(
            'create',
            Organization::class
        );

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


    public function restore(
        int $organization
    ): RedirectResponse {
        $organization = Organization::onlyTrashed()
            ->findOrFail($organization);

        /*
         * Authorize the actual archived Organisation
         * before restoring it.
         */
        $this->authorize(
            'restore',
            $organization
        );

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