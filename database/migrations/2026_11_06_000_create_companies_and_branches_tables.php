<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('legal_name');
            $table->string('trading_name')->nullable();
            $table->string('registration_no')->unique();
            $table->string('tax_id')->nullable();
            $table->char('base_currency', 3)->default('NGN');
            $table->string('timezone')->default('Africa/Lagos');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('timezone')->default('Africa/Lagos');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'status']);
            $table->index('created_at');
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index('status');
        });

        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('scope')->default('company');
            $table->timestamps();
            $table->unique(['company_id', 'key', 'scope']);
            $table->index(['company_id', 'scope']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');
    }
};
