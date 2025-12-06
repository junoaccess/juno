<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'profile_photo_url' => $this->profile_photo_url,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'organizations' => OrganizationResource::collection($this->whenLoaded('organizations')),
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
        ];
    }
}
