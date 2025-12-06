<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrganizationController extends Controller
{
    public function __construct(
        protected OrganizationService $organizationService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return OrganizationResource::collection(
            $this->organizationService->paginate()
        );
    }

    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = $this->organizationService->create(
            data: $request->validated(),
            ownerData: $request->getOwnerData()
        );

        return response()->json([
            'message' => 'Organization created successfully! The owner will receive an email invitation.',
            'data' => new OrganizationResource($organization),
        ], 201);
    }

    public function show(Organization $organization): OrganizationResource
    {
        return new OrganizationResource(
            $this->organizationService->loadRelationships($organization)
        );
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        $organization = $this->organizationService->update($organization, $request->validated());

        return response()->json([
            'message' => 'Organization updated successfully!',
            'data' => new OrganizationResource($organization),
        ]);
    }

    public function destroy(Organization $organization): JsonResponse
    {
        $this->organizationService->delete($organization);

        return response()->json([
            'message' => 'Organization deleted successfully!',
        ]);
    }
}
