<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Invitation::class);

        return InvitationResource::collection(
            $this->invitationService->paginate()
        );
    }

    public function store(StoreInvitationRequest $request): JsonResponse
    {
        $invitation = $this->invitationService->create($request->validated());

        return response()->json([
            'message' => 'Invitation sent successfully!',
            'data' => new InvitationResource($invitation),
        ], 201);
    }

    public function show(Invitation $invitation): InvitationResource
    {
        $this->authorize('view', $invitation);

        return new InvitationResource(
            $this->invitationService->loadRelationships($invitation)
        );
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation): JsonResponse
    {
        $invitation = $this->invitationService->update($invitation, $request->validated());

        return response()->json([
            'message' => 'Invitation updated successfully!',
            'data' => new InvitationResource($invitation),
        ]);
    }

    public function destroy(Invitation $invitation): JsonResponse
    {
        $this->authorize('delete', $invitation);

        $this->invitationService->delete($invitation);

        return response()->json([
            'message' => 'Invitation deleted successfully!',
        ]);
    }
}
