<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationSwitchController extends Controller
{
    /**
     * Show the organization selection page.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $organizations = $user->organizations()
            ->withPivot('is_default')
            ->get()
            ->map(function ($org) use ($user) {
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                    'email' => $org->email,
                    'is_default' => $org->pivot->is_default,
                    'is_current' => $org->id === $user->current_organization_id,
                ];
            });

        return Inertia::render('Organizations/Select', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * Switch to a different organization.
     */
    public function store(Request $request, Organization $organization)
    {
        $user = Auth::user();

        // Verify user is a member of this organization
        if (!$user->organizations->contains($organization)) {
            abort(403, 'You are not a member of this organization.');
        }

        // Set as current organization
        $user->setCurrentOrganization($organization);

        // Update session
        session(['current_organization_id' => $organization->id]);

        // Redirect to organization dashboard or intended URL
        return redirect()->intended(route('organizations.show', $organization))
            ->with('success', "Switched to {$organization->name}");
    }
}
