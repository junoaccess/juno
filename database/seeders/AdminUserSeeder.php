<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding admin user...');

        Artisan::call('admin:create', [
            '--email' => 'admin@junoaccess.site',
            '--password' => 'Password123!',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--organization' => 'Acme Inc',
            '--force' => true,
        ]);

        $this->command->info(Artisan::output());
    }
}
