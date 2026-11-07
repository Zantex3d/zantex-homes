<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name')->unique();
            $table->string('action');
            $table->string('resource');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['action', 'resource']);
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id', 'company_id']);
            $table->index(['user_id', 'company_id']);
        });

        Schema::create('user_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('estate_id')->nullable()->constrained('estates')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_scopes');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
