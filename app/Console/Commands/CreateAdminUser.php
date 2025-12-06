<?php

namespace App\Console\Commands;

use App\Actions\CreateAdminUserAction;
use App\DataTransferObjects\OwnerData;
use App\Enums\Role;
use App\Services\OrganizationService;
use Illuminate\Console\Command;
use Illuminate\Console\Command\Attributes\AsCommand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

#[AsCommand(
    name: 'admin:create',
    description: 'Create an admin user with full permissions and assign to an organisation'
)]
class CreateAdminUser extends Command
{
    public function __construct(
        protected OrganizationService $organizationService,
        protected CreateAdminUserAction $createAdminUserAction,
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:create
                            {--email= : The email address for the admin user}
                            {--password= : The password for the admin user}
                            {--first-name= : The first name of the admin user}
                            {--last-name= : The last name of the admin user}
                            {--organization= : The name of the organisation (will be created if it doesn\'t exist)}
                            {--force : Skip confirmation prompts}';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating admin user...');
        $this->newLine();

        $input = $this->gatherInput();

        if (! $this->validateInput($input)) {
            return self::FAILURE;
        }

        $this->displaySummary($input);

        if (! $this->confirmCreation()) {
            return self::SUCCESS;
        }

        return $this->createAdminUser($input);
    }

    /**
     * Gather input from options or prompts.
     */
    protected function gatherInput(): array
    {
        $input = [
            'email' => $this->option('email') ?? $this->ask('Email address', 'admin@example.com'),
            'password' => $this->option('password') ?? $this->secret('Password (leave empty to generate)'),
            'first_name' => $this->option('first-name') ?? $this->ask('First name', 'Admin'),
            'last_name' => $this->option('last-name') ?? $this->ask('Last name', 'User'),
            'organization' => $this->option('organization') ?? $this->ask('Organization name', 'Acme Inc'),
        ];

        // Generate password if not provided
        if (empty($input['password'])) {
            $input['password'] = Str::random(16);
            $this->warn("Generated password: {$input['password']}");
            $this->warn('Please save this password securely!');
            $this->newLine();
        }

        return $input;
    }

    /**
     * Validate the gathered input.
     */
    protected function validateInput(array $input): bool
    {
        $validator = Validator::make($input, [
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

            return false;
        }

        return true;
    }

    /**
     * Display summary of what will be created.
     */
    protected function displaySummary(array $input): void
    {
        $this->table(
            ['Field', 'Value'],
            [
                ['Email', $input['email']],
                ['First Name', $input['first_name']],
                ['Last Name', $input['last_name']],
                ['Organization', $input['organization']],
                ['Role', Role::ADMIN->value],
            ]
        );
    }

    /**
     * Confirm creation with the user.
     */
    protected function confirmCreation(): bool
    {
        if ($this->option('force')) {
            return true;
        }

        if (! $this->confirm('Create this admin user?', true)) {
            $this->warn('Operation cancelled.');

            return false;
        }

        return true;
    }

    /**
     * Create the admin user and organization.
     */
    protected function createAdminUser(array $input): int
    {
        try {
            $organization = $this->findOrCreateOrganization(
                $input['organization'],
                $input['email'],
                $input['first_name'],
                $input['last_name']
            );

            $user = $this->createAdminUserAction->execute($organization, [
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            $this->displaySuccessMessage($user, $organization, $input['password']);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user:');
            $this->error($e->getMessage());

            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }

    /**
     * Find existing organization or create a new one.
     */
    protected function findOrCreateOrganization(string $name, string $email, string $firstName, string $lastName): \App\Models\Organization
    {
        $slug = Str::slug($name);
        $organization = \App\Models\Organization::where('slug', $slug)->first();

        if ($organization) {
            $this->info("✓ Using existing organisation: {$organization->name}");

            return $organization;
        }

        // Create organization with owner data (skip events to avoid automatic onboarding)
        $ownerData = new OwnerData(
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            phone: null,
        );

        $organization = \App\Models\Organization::withoutEvents(function () use ($name, $ownerData) {
            return $this->organizationService->create([
                'name' => $name,
                'website' => null,
            ], $ownerData);
        });

        $this->info("✓ Created organisation: {$organization->name}");

        return $organization;
    }

    /**
     * Display success message with user credentials.
     */
    protected function displaySuccessMessage(\App\Models\User $user, \App\Models\Organization $organization, string $password): void
    {
        $this->newLine();
        $this->info('✅ Admin user created successfully!');
        $this->newLine();

        $this->table(
            ['Credential', 'Value'],
            [
                ['Email', $user->email],
                ['Password', $password],
                ['Organization', $organization->name],
                ['Organization Slug', $organization->slug],
            ]
        );
    }
}
