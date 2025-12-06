<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--email= : The email address for the admin user}
                            {--password= : The password for the admin user}
                            {--first-name= : The first name of the admin user}
                            {--last-name= : The last name of the admin user}
                            {--organization= : The name of the organization (will be created if it doesn\'t exist)}
                            {--force : Skip confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user with full permissions and assign to an organization';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating admin user...');
        $this->newLine();

        // Gather user details
        $email = $this->option('email') ?? $this->ask('Email address', 'admin@example.com');
        $password = $this->option('password') ?? $this->secret('Password (leave empty to generate)');
        $firstName = $this->option('first-name') ?? $this->ask('First name', 'Admin');
        $lastName = $this->option('last-name') ?? $this->ask('Last name', 'User');
        $organizationName = $this->option('organization') ?? $this->ask('Organization name', 'Acme Inc');

        // Generate password if not provided
        if (empty($password)) {
            $password = Str::random(16);
            $this->warn("Generated password: {$password}");
            $this->warn('Please save this password securely!');
            $this->newLine();
        }

        // Validate inputs
        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ], [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("  • {$error}");
            }

            return self::FAILURE;
        }

        // Show summary
        $this->table(
            ['Field', 'Value'],
            [
                ['Email', $email],
                ['First Name', $firstName],
                ['Last Name', $lastName],
                ['Organization', $organizationName],
                ['Role', Role::ADMIN->value],
            ]
        );

        // Confirm if not forced
        if (!$this->option('force')) {
            if (!$this->confirm('Create this admin user?', true)) {
                $this->warn('Operation cancelled.');

                return self::SUCCESS;
            }
        }

        try {
            DB::beginTransaction();

            // Check if organization already exists
            $organization = Organization::where('slug', Str::slug($organizationName))->first();
            $isNewOrganization = is_null($organization);

            if ($isNewOrganization) {
                // Create organization without triggering observer (to avoid onboarding job)
                $organization = Organization::withoutEvents(function () use ($organizationName, $email, $firstName, $lastName) {
                    return Organization::create([
                        'name' => $organizationName,
                        'slug' => Str::slug($organizationName),
                        'email' => $email,
                        'owner_email' => $email,
                        'owner_name' => "{$firstName} {$lastName}",
                    ]);
                });

                $this->info("✓ Created organization: {$organization->name}");
            } else {
                $this->info("✓ Using existing organization: {$organization->name}");
            }

            // Create user
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password,
                'email_verified_at' => now(),
                'current_organization_id' => $organization->id,
            ]);

            $this->info("✓ Created admin user: {$user->email}");

            // Attach user to organization with is_default flag
            $user->organizations()->attach($organization->id, [
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("✓ Assigned user to organization: {$organization->name}");

            // Create or find admin role for this organization
            $adminRole = \App\Models\Role::firstOrCreate(
                [
                    'name' => Role::ADMIN->value,
                    'organization_id' => $organization->id,
                ],
                [
                    'description' => 'Administrator with full access to all resources',
                ]
            );

            if ($adminRole->wasRecentlyCreated) {
                $this->info('✓ Created ADMIN role for organization');

                // Attach all permissions to admin role if there are any
                $allPermissions = Permission::all();
                if ($allPermissions->isNotEmpty()) {
                    $adminRole->permissions()->sync($allPermissions->pluck('id'));
                    $this->info("✓ Granted all permissions ({$allPermissions->count()}) to ADMIN role");
                }
            } else {
                $this->info('✓ Using existing ADMIN role');
            }

            // Attach role to user within the organization
            $user->roles()->attach($adminRole->id, [
                'organization_id' => $organization->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info('✓ Assigned ADMIN role to user');

            DB::commit();

            $this->newLine();
            $this->info('✅ Admin user created successfully!');
            $this->newLine();

            $this->table(
                ['Credential', 'Value'],
                [
                    ['Email', $email],
                    ['Password', $password],
                    ['Organization', $organization->name],
                    ['Organization Slug', $organization->slug],
                ]
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('Failed to create admin user:');
            $this->error($e->getMessage());

            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }
}
