<?php

namespace App\Http\Controllers\Web;

use App\Filters\TeamFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
    ) {}

    public function index(TeamFilter $filter): Response
    {
        $this->authorize('viewAny', Team::class);

        return Inertia::render('Teams/Index', [
            'teams' => $this->teamService->paginate(15, $filter),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Team::class);

        return Inertia::render('Teams/Create');
    }

    public function store(StoreTeamRequest $request): RedirectResponse
    {
        $team = $this->teamService->create($request->validated());

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    public function show(Team $team): Response
    {
        $this->authorize('view', $team);

        return Inertia::render('Teams/Show', [
            'team' => $this->teamService->loadRelationships($team),
        ]);
    }

    public function edit(Team $team): Response
    {
        $this->authorize('update', $team);

        return Inertia::render('Teams/Edit', [
            'team' => $team,
        ]);
    }

    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        $this->teamService->update($team, $request->validated());

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        $this->teamService->delete($team);

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }
}
