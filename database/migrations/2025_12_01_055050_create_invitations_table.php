<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('token_hash')->unique();
            $table->foreignIdFor(User::class, 'invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('roles')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['organization_id', 'email', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
