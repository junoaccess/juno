<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Role::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Organization::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'user_id', 'organization_id']);
            $table->index(['user_id', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
