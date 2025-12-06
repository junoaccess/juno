<?php

namespace App\Services;

use App\Enums\Role as RoleEnum;
use App\Mail\OrganizationOwnerInvitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrganizationOnboardingService
{
    public function __construct(
        protected RolePermissionSeeder $rolePermissionSeeder,
        protected UserService $userService,
    ) {}

    /**
     * Onboard a new organization with roles, permissions, and owner.
     */
    public function onboard(Organization $organization, ?array $ownerData = null): void
    {
        if ($this->isAlreadyOnboarded($organization)) {
            return;
        }

        DB::transaction(function () use ($organization, $ownerData) {
            $this->rolePermissionSeeder->seed($organization);
            $owner = $this->setupOwner($organization, $ownerData);
            $this->sendOnboardingEmail($organization, $owner);
        });
    }

    /**
     * Check if organization has already been onboarded.
     */
    protected function isAlreadyOnboarded(Organization $organization): bool
    {
        return $organization->roles()->exists();
    }

    /**
     * Setup the owner user and assign them to the organization.
     */
    protected function setupOwner(Organization $organization, ?array $ownerData): User
    {
        $email = $ownerData['email'] ?? $organization->email;

        $owner = $this->userService->findOrCreate($email, [
            'first_name' => $ownerData['first_name'] ?? 'Owner',
            'last_name' => $ownerData['last_name'] ?? '',
            'middle_name' => $ownerData['middle_name'] ?? null,
            'phone' => $ownerData['phone'] ?? $organization->phone,
        ]);

        $organization->addUser($owner);
        $this->assignOwnerRole($organization, $owner);

        return $owner;
    }

    /**
     * Assign the owner role to the user.
     */
    protected function assignOwnerRole(Organization $organization, User $owner): void
    {
        $ownerRole = $organization->roles()
            ->where('slug', RoleEnum::OWNER->value)
            ->first();

        if (! $ownerRole) {
            return;
        }

        $owner->roles()->syncWithoutDetaching([
            $ownerRole->id => ['organization_id' => $organization->id],
        ]);
    }

    /**
     * Send onboarding email to the owner.
     */
    protected function sendOnboardingEmail(Organization $organization, User $owner): void
    {
        try {
            Mail::to($owner->email)->queue(
                new OrganizationOwnerInvitation($organization, $owner)
            );
        } catch (\Throwable) {
            // Fail silently - email is not critical to onboarding
        }
    }
}
