<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed all permissions first
        $this->call([
            PermissionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
