<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SelectOrganization;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationSelectionController extends Controller
{
    /**
     * Display the organization selection form.
     */
    public function show(): Response
    {
        return Inertia::render('auth/organization-select', [
            'mainDomain' => config('app.main_domain'),
        ]);
    }

    /**
     * Handle the organization selection and redirect to the organization's subdomain.
     */
    public function store(Request $request, SelectOrganization $selectOrganization)
    {
        $subdomainUrl = $selectOrganization->handle($request);

        // For Inertia requests, return the external URL
        if ($request->header('X-Inertia')) {
            return Inertia::location($subdomainUrl);
        }

        return redirect()->away($subdomainUrl);
    }
}
