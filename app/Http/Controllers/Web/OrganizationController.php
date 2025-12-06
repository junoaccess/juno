<?php

namespace App\Http\Controllers\Web;

use App\Filters\OrganizationFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function __construct(
        protected OrganizationService $organizationService,
    ) {}

    public function index(OrganizationFilter $filter): Response
    {
        return Inertia::render('organisations/index', [
            'organizations' => $this->organizationService->paginate(15, $filter),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Organization::class);

        return Inertia::render('Organizations/Create');
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $organization = $this->organizationService->create(
            data: $request->validated(),
            ownerData: $request->getOwnerData()
        );

        return redirect()
            ->route('organizations.show', $organization)
            ->with('success', 'Organization created successfully! The owner will receive an email invitation.');
    }

    public function show(Organization $organization): Response
    {
        return Inertia::render('Organizations/Show', [
            'organization' => $this->organizationService->loadRelationships($organization),
        ]);
    }

    public function edit(Organization $organization): Response
    {
        $this->authorize('update', $organization);

        return Inertia::render('Organizations/Edit', [
            'organization' => $organization,
        ]);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $this->organizationService->update($organization, $request->validated());

        return redirect()
            ->route('organizations.show', $organization)
            ->with('success', 'Organization updated successfully!');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->organizationService->delete($organization);

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Organization deleted successfully!');
    }
}
