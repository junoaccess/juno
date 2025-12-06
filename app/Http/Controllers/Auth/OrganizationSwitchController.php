<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationSwitchController extends Controller
{
    public function __construct(
        protected OrganizationService $organizationService,
    ) {}

    /**
     * Show organization selection page for authenticated users.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();

        return Inertia::render('auth/organizations/select', [
            'organizations' => $user->organizations()->get(['id', 'name', 'slug']),
            'current' => $user->currentOrganization,
        ]);
    }

    /**
     * Switch to a different organization.
     */
    public function store(Request $request, Organization $organization): RedirectResponse
    {
        $this->organizationService->switchUserOrganization(Auth::user(), $organization);

        return redirect()->away($this->organizationService->getOrganizationUrl($organization))
            ->with('success', "Switched to {$organization->name}");
    }
}
