<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relationships (loaded conditionally)
            'users' => UserResource::collection($this->whenLoaded('users')),
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'invitations' => InvitationResource::collection($this->whenLoaded('invitations')),

            // Counts
            'users_count' => $this->whenCounted('users'),
            'teams_count' => $this->whenCounted('teams'),
            'roles_count' => $this->whenCounted('roles'),
        ];
    }
}
