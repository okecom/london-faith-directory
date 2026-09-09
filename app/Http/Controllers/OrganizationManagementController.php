<?php

namespace App\Http\Controllers;

use App\Models\Organization;
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
}