<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Team::class);

        return TeamResource::collection(
            $this->teamService->paginate()
        );
    }

    public function store(StoreTeamRequest $request): JsonResponse
    {
        $this->authorize('create', Team::class);

        $team = $this->teamService->create($request->validated());

        return response()->json([
            'message' => 'Team created successfully!',
            'data' => new TeamResource($team),
        ], 201);
    }

    public function show(Team $team): TeamResource
    {
        $this->authorize('view', $team);

        return new TeamResource(
            $this->teamService->loadRelationships($team)
        );
    }

    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $this->authorize('update', $team);

        $team = $this->teamService->update($team, $request->validated());

        return response()->json([
            'message' => 'Team updated successfully!',
            'data' => new TeamResource($team),
        ]);
    }

    public function destroy(Team $team): JsonResponse
    {
        $this->authorize('delete', $team);

        $this->teamService->delete($team);

        return response()->json([
            'message' => 'Team deleted successfully!',
        ]);
    }
}
