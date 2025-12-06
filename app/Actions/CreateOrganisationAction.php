<?php

namespace App\Actions;

use App\DataTransferObjects\OwnerData;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class CreateOrganisationAction
{
    /**
     * Create a new organisation with owner details.
     */
    public function execute(array $data, OwnerData $ownerData): Organization
    {
        return DB::transaction(function () use ($data, $ownerData) {
            $organization = Organization::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['name']),
                'email' => $ownerData->email,
                'phone' => $ownerData->phone,
                'website' => $data['website'] ?? null,
                'owner_name' => trim($ownerData->firstName.' '.$ownerData->lastName),
                'owner_email' => $ownerData->email,
                'owner_phone' => $ownerData->phone,
            ]);

            // Store owner data for the observer to access
            $organization->ownerData = $ownerData->toArray();

            return $organization;
        });
    }
}
