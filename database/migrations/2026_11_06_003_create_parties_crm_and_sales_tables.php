<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PARTIES & CRM TABLES
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('party_type', ['individual', 'company', 'ngo', 'government'])->default('individual');
            $table->string('display_name');
            $table->string('legal_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('lga_id')->nullable()->constrained('lgas')->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->index(['company_id', 'status']);
            $table->index('email');
        });

        Schema::create('party_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('party_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_stages', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('sequence')->default(0);
            $table->enum('type', ['prospect', 'engaged', 'qualified', 'negotiating', 'lost', 'won'])->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'name']);
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('party_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained('lead_stages')->nullOnDelete();
            $table->integer('score')->default(0);
            $table->enum('status', ['new', 'active', 'qualified', 'lost', 'won', 'contacted'])->default('new');
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status', 'assigned_agent_id']);
        });

        Schema::create('viewings', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamp('scheduled_at');
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['scheduled', 'attended', 'no_show', 'rescheduled', 'cancelled'])->default('scheduled');
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->json('terms_json')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['draft', 'offered', 'accepted', 'rejected', 'countered', 'expired'])->default('draft');
            $table->timestamps();
            $table->index('status');
        });

        // SALES & COMMISSIONS TABLES
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('reserved_at');
            $table->timestamp('expires_at')->nullable();
            $table->decimal('deposit_due', 19, 4)->nullable();
            $table->decimal('deposit_received', 19, 4)->default(0);
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['active', 'expired', 'converted_to_sale', 'cancelled'])->default('active');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('seller_party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->string('transaction_no')->unique();
            $table->date('sale_date');
            $table->decimal('total_price', 19, 4);
            $table->decimal('discount', 19, 4)->default(0);
            $table->decimal('tax', 19, 4)->default(0);
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['draft', 'negotiating', 'reserved', 'agreement_signed', 'completed', 'cancelled', 'refunded'])->default('draft');
            $table->foreignId('agreement_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status', 'sale_date']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('unit_price', 19, 4);
            $table->integer('quantity')->default(1);
            $table->json('terms_snapshot')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->date('due_date');
            $table->decimal('amount_due', 19, 4);
            $table->decimal('amount_paid', 19, 4)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->index(['sale_id', 'due_date']);
        });

        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('basis', ['fixed', 'percentage', 'tiered', 'split_agent', 'referral'])->default('percentage');
            $table->decimal('value', 10, 2)->nullable();
            $table->json('tiers_json')->nullable();
            $table->boolean('requires_collection')->default(false);
            $table->decimal('cap_amount', 19, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('agent_commissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('commission_rules')->nullOnDelete();
            $table->decimal('basis_amount', 19, 4);
            $table->decimal('rate', 10, 2);
            $table->decimal('amount', 19, 4);
            $table->enum('status', ['draft', 'approved', 'paid', 'reversed'])->default('draft');
            $table->date('payable_at')->nullable();
            $table->date('paid_at')->nullable();
            $table->timestamps();
            $table->index(['agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_commissions');
        Schema::dropIfExists('commission_rules');
        Schema::dropIfExists('sale_installments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('viewings');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('lead_stages');
        Schema::dropIfExists('lead_sources');
        Schema::dropIfExists('party_documents');
        Schema::dropIfExists('party_contacts');
        Schema::dropIfExists('parties');
    }
};
