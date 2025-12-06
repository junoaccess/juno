<?php

namespace App\Actions;

use App\Enums\Role as RoleEnum;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateAdminUserAction
{
    public function __construct(
        protected UserService $userService,
    ) {}

    /**
     * Create an admin user for an organization.
     */
    public function execute(Organization $organization, array $userData): User
    {
        return DB::transaction(function () use ($organization, $userData) {
            // Create user
            $user = $this->userService->create([
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'email_verified_at' => now(),
            ]);

            // Set current organization
            $user->setCurrentOrganization($organization);

            // Attach user to organization with is_default flag
            $organization->addUser($user);
            $organization->users()->updateExistingPivot($user->id, ['is_default' => true]);

            // Assign admin role
            $this->assignAdminRole($organization, $user);

            return $user;
        });
    }

    /**
     * Assign admin role to the user with all permissions.
     */
    protected function assignAdminRole(Organization $organization, User $user): void
    {
        // Create or find admin role for this organization
        $adminRole = Role::firstOrCreate(
            [
                'name' => RoleEnum::ADMIN->value,
                'organization_id' => $organization->id,
            ],
            [
                'slug' => Str::slug(RoleEnum::ADMIN->value),
                'description' => 'Administrator with full access to all resources',
            ]
        );

        // Attach all permissions to admin role if it was just created
        if ($adminRole->wasRecentlyCreated) {
            $allPermissions = Permission::all();
            if ($allPermissions->isNotEmpty()) {
                $adminRole->permissions()->sync($allPermissions->pluck('id'));
            }
        }

        // Attach role to user within the organization
        $user->roles()->syncWithoutDetaching([
            $adminRole->id => ['organization_id' => $organization->id],
        ]);
    }
}
