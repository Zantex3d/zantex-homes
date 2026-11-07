<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PROPERTY & ASSETS TABLES
        Schema::create('estates', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('lga_id')->nullable()->constrained('lgas')->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['active', 'inactive', 'under_development', 'completed'])->default('active');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'status']);
            $table->index('created_at');
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estate_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('parent_property_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('address')->nullable();
            $table->decimal('area', 12, 4)->nullable();
            $table->string('area_unit')->default('sqm');
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'unavailable', 'under_development'])->default('available');
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_cost', 19, 4)->nullable();
            $table->char('currency_code', 3)->default('NGN');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'status', 'estate_id']);
            $table->index('created_at');
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('building_code')->unique();
            $table->integer('floors')->nullable();
            $table->integer('construction_year')->nullable();
            $table->enum('condition_status', ['excellent', 'good', 'fair', 'poor', 'under_repair'])->nullable();
            $table->timestamps();
            $table->index('property_id');
        });

        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->integer('floor_no');
            $table->string('label')->nullable();
            $table->timestamps();
            $table->unique(['building_id', 'floor_no']);
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('unit_code')->unique();
            $table->boolean('rentable')->default(true);
            $table->decimal('area', 12, 4)->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'unavailable'])->default('available');
            $table->timestamps();
            $table->index(['property_id', 'status']);
        });

        Schema::create('property_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_party_id')->constrained('parties')->cascadeOnDelete();
            $table->enum('ownership_type', ['outright', 'joint', 'third_party_managed', 'leased_in', 'mortgaged'])->default('outright');
            $table->decimal('ownership_percentage', 5, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
            $table->index(['property_id', 'effective_from', 'effective_to']);
        });

        // LAND & PLOTS TABLES
        Schema::create('land_parcels', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estate_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('parcel_code')->unique();
            $table->string('survey_no')->nullable();
            $table->decimal('area', 14, 4)->nullable();
            $table->string('area_unit')->default('sqm');
            $table->enum('title_status', ['freehold', 'leasehold', 'disputed', 'under_verification', 'restricted'])->default('freehold');
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_cost', 19, 4)->nullable();
            $table->foreignId('seller_party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->text('geometry_ref')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->unique(['company_id', 'parcel_code']);
            $table->index(['company_id', 'estate_id']);
        });

        Schema::create('subdivisions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('parent_parcel_id')->constrained('land_parcels')->cascadeOnDelete();
            $table->string('plan_no')->unique();
            $table->date('approved_at')->nullable();
            $table->foreignId('survey_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('parcel_id')->constrained('land_parcels')->cascadeOnDelete();
            $table->foreignId('subdivision_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('plot_code')->unique();
            $table->decimal('area', 14, 4)->nullable();
            $table->json('dimensions_json')->nullable();
            $table->text('geometry_ref')->nullable();
            $table->decimal('asking_price', 19, 4)->nullable();
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['available', 'reserved', 'under_negotiation', 'sold', 'allocated', 'disputed', 'restricted', 'under_development', 'transferred'])->default('available');
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('plot_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->timestamp('allocated_at');
            $table->enum('transfer_status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->index(['plot_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plot_allocations');
        Schema::dropIfExists('plots');
        Schema::dropIfExists('subdivisions');
        Schema::dropIfExists('land_parcels');
        Schema::dropIfExists('property_owners');
        Schema::dropIfExists('units');
        Schema::dropIfExists('floors');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('estates');
    }
};
