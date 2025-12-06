<?php

namespace App\Services;

use App\Models\Team;

class TeamService
{
    public function paginate(int $perPage = 15)
    {
        return Team::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Team
    {
        return Team::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'organization_id' => $data['organization_id'],
        ]);
    }

    public function update(Team $team, array $data): Team
    {
        $team->update(array_filter([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ]));

        return $team->fresh();
    }

    public function delete(Team $team): bool
    {
        return $team->delete();
    }

    public function restore(Team $team): bool
    {
        return $team->restore();
    }

    public function loadRelationships(Team $team): Team
    {
        return $team->load(['organization', 'users']);
    }
}
