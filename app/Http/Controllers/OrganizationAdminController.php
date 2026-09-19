<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrganizationAdminController extends Controller
{
    public function index(): View
    {
        $administrators = User::with('organization')
            ->where('role', User::ROLE_ORGANISATION_ADMIN)
            ->orderBy('name')
            ->paginate(5);

        return view('site.organization-admins.index', [
            'administrators' => $administrators,
        ]);
    }

    public function create(): View
    {
        $organizations = Organization::orderBy('name')->get();

        return view('site.organization-admins.create', [
            'organizations' => $organizations,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'organization_id' => [
                'required',
                'exists:organizations,id',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $activeAdministratorExists = User::where(
            'role',
            User::ROLE_ORGANISATION_ADMIN
        )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where('is_active', true)
            ->exists();

        if ($activeAdministratorExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'organization_id' =>
                        'This organisation already has an active administrator.',
                ]);
        }

        DB::transaction(function () use ($validated) {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => User::ROLE_ORGANISATION_ADMIN,
                'organization_id' => $validated['organization_id'],
                'is_active' => true,
                'must_change_password' => true,
            ]);
        });

        return redirect()
            ->route('site.organization-admins.index')
            ->with(
                'success',
                'Organisation Administrator created successfully.'
            );
    }

    public function deactivate(User $user): RedirectResponse
    {
        abort_unless(
            $user->role === User::ROLE_ORGANISATION_ADMIN,
            404
        );

        $user->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('site.organization-admins.index')
            ->with(
                'success',
                'Organisation Administrator access deactivated.'
            );
    }

    public function reactivate(User $user): RedirectResponse
    {
        abort_unless(
            $user->role === User::ROLE_ORGANISATION_ADMIN,
            404
        );

        $activeAdministratorExists = User::where(
            'role',
            User::ROLE_ORGANISATION_ADMIN
        )
            ->where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->whereKeyNot($user->id)
            ->exists();

        if ($activeAdministratorExists) {
            return back()->withErrors([
                'administrator' =>
                    'This organisation already has an active administrator.',
            ]);
        }

        $user->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('site.organization-admins.index')
            ->with(
                'success',
                'Organisation Administrator access reactivated.'
            );
    }

    public function replace(User $user): View
{
    abort_unless(
        $user->role === User::ROLE_ORGANISATION_ADMIN,
        404
    );

    abort_unless(
        $user->is_active,
        422,
        'Only an active Organisation Administrator can be replaced.'
    );

    $user->load('organization');

    abort_unless(
        $user->organization,
        422,
        'This administrator does not have an organisation assigned.'
    );

    return view('site.organization-admins.replace', [
        'administrator' => $user,
        'organization' => $user->organization,
    ]);
}


    public function storeReplacement(
        Request $request,
        User $user
    ): RedirectResponse {
        abort_unless(
            $user->role === User::ROLE_ORGANISATION_ADMIN,
            404
        );

        abort_unless(
            $user->is_active,
            422,
            'Only an active Organisation Administrator can be replaced.'
        );

        abort_unless(
            $user->organization_id !== null,
            422,
            'This administrator does not have an organisation assigned.'
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'is_active' => false,
            ]);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => User::ROLE_ORGANISATION_ADMIN,
                'organization_id' => $user->organization_id,
                'is_active' => true,
                'must_change_password' => true,
            ]);
        });

        return redirect()
            ->route('site.organization-admins.index')
            ->with(
                'success',
                'Organisation Administrator replaced successfully.'
            );
    }

}