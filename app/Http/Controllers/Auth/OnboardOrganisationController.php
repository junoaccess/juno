<?php

namespace App\Http\Controllers\Auth;

use App\DataTransferObjects\OwnerData;
use App\Http\Controllers\Controller;
use App\Http\Requests\OnboardOrganisationRequest;
use App\Services\OrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OnboardOrganisationController extends Controller
{
    public function __construct(
        protected OrganizationService $organizationService,
    ) {}

    /**
     * Show the organisation onboarding form.
     */
    public function show(Request $request): Response
    {
        return Inertia::render('onboarding/new-organisation', [
            'mainDomain' => config('app.main_domain'),
        ]);
    }

    /**
     * Handle the organisation onboarding submission.
     */
    public function store(OnboardOrganisationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Create organisation with owner
        $organization = $this->organizationService->create(
            [
                'name' => $validated['organisation_name'],
                'slug' => $validated['organisation_slug'],
                'email' => $validated['organisation_email'] ?? null,
            ],
            new OwnerData(
                firstName: $validated['owner_first_name'],
                lastName: $validated['owner_last_name'],
                email: $validated['owner_email'],
                phone: $validated['owner_phone'] ?? null,
                password: $validated['password'],
            )
        );

        // Get the owner user (created by the service)
        $owner = $organization->users()->where('email', $validated['owner_email'])->first();

        // Log the owner in
        Auth::login($owner);

        // Redirect to organisation subdomain
        return redirect()->away($this->organizationService->getOrganizationUrl($organization))
            ->with('success', "Welcome to {$organization->name}! Your organisation has been created.");
    }
}
