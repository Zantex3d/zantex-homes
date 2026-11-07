<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TENANCY TABLES
        Schema::create('tenants', function (Blueprint $table) {
            $table->foreignId('party_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->json('emergency_contact_json')->nullable();
            $table->timestamps();
        });

        Schema::create('landlords', function (Blueprint $table) {
            $table->foreignId('party_id')->primary()->constrained()->cascadeOnDelete();
            $table->json('payout_preferences_json')->nullable();
            $table->timestamps();
        });

        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('guarantor_party_id')->constrained('parties')->cascadeOnDelete();
            $table->string('relationship')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamps();
            $table->unique(['tenant_party_id', 'guarantor_party_id']);
        });

        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('landlord_party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->string('lease_no')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('rent_frequency', ['annual', 'half_yearly', 'quarterly', 'monthly', 'weekly', 'daily', 'custom'])->default('monthly');
            $table->integer('rent_frequency_days')->nullable();
            $table->decimal('rent_amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->decimal('rent_escalation_rate', 5, 2)->default(0);
            $table->decimal('security_deposit', 19, 4)->nullable();
            $table->decimal('agency_fee', 19, 4)->nullable();
            $table->decimal('legal_fee', 19, 4)->nullable();
            $table->integer('grace_period_days')->default(0);
            $table->text('special_clauses')->nullable();
            $table->enum('status', ['draft', 'active', 'suspended', 'terminated', 'expired'])->default('active');
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status', 'unit_id']);
            $table->index('start_date');
            $table->index('end_date');
        });

        Schema::create('lease_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->enum('charge_type', ['rent', 'service_charge', 'utility', 'security_deposit', 'agency_fee', 'legal_fee', 'other'])->default('rent');
            $table->decimal('amount', 19, 4);
            $table->enum('frequency', ['annual', 'half_yearly', 'quarterly', 'monthly', 'weekly', 'daily', 'once'])->default('monthly');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('rent_schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->date('due_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 19, 4);
            $table->enum('status', ['pending', 'invoiced', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->index(['lease_id', 'due_date', 'status']);
        });

        Schema::create('deposit_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->date('received_date');
            $table->decimal('amount_refunded', 19, 4)->default(0);
            $table->date('refund_date')->nullable();
            $table->enum('status', ['held', 'applied_to_rent', 'refunded', 'forfeited'])->default('held');
            $table->timestamps();
        });

        Schema::create('tenant_inspections', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->enum('inspection_type', ['move_in', 'move_out', 'routine', 'damage_assessment'])->default('move_in');
            $table->date('date');
            $table->json('checklist_json')->nullable();
            $table->foreignId('condition_report_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // SHORTLET TABLES
        Schema::create('shortlet_units', function (Blueprint $table) {
            $table->foreignId('unit_id')->primary()->constrained()->cascadeOnDelete();
            $table->decimal('base_rate', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->decimal('cleaning_fee', 19, 4)->nullable();
            $table->integer('max_guests')->default(2);
            $table->string('checkin_time')->default('14:00');
            $table->string('checkout_time')->default('11:00');
            $table->json('rules_json')->nullable();
            $table->timestamps();
        });

        Schema::create('availability_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->date('blocked_from');
            $table->date('blocked_to');
            $table->enum('reason', ['maintenance', 'cleaning', 'unavailable', 'blocked_by_owner'])->default('unavailable');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('booking_no')->unique();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_party_id')->constrained('parties')->cascadeOnDelete();
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->integer('guests')->default(1);
            $table->decimal('amount', 19, 4);
            $table->decimal('deposit', 19, 4)->default(0);
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['hold', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('hold');
            $table->string('source')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['unit_id', 'checkin_date', 'checkout_date', 'status']);
        });

        Schema::create('guest_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checkin_at');
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('guest_checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checkout_at');
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damage'])->default('good');
            $table->text('damage_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->enum('cancellation_reason', ['guest_request', 'maintenance_issue', 'owner_request', 'payment_failed'])->default('guest_request');
            $table->decimal('refund_amount', 19, 4)->default(0);
            $table->date('refund_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cancellations');
        Schema::dropIfExists('guest_checkouts');
        Schema::dropIfExists('guest_checkins');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('availability_blocks');
        Schema::dropIfExists('shortlet_units');
        Schema::dropIfExists('tenant_inspections');
        Schema::dropIfExists('deposit_records');
        Schema::dropIfExists('rent_schedules');
        Schema::dropIfExists('lease_charges');
        Schema::dropIfExists('leases');
        Schema::dropIfExists('guarantors');
        Schema::dropIfExists('landlords');
        Schema::dropIfExists('tenants');
    }
};
