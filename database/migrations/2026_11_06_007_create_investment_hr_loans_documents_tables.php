<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // INVESTMENT & SHAREHOLDERS
        Schema::create('investors', function (Blueprint $table) {
            $table->foreignId('party_id')->primary()->constrained()->cascadeOnDelete();
            $table->enum('investor_type', ['individual', 'institutional', 'family_office', 'fund'])->default('individual');
            $table->enum('kyc_status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->timestamps();
        });

        Schema::create('investment_agreements', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('investor_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('investee_company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->enum('instrument_type', ['equity', 'shareholder_loan', 'project_finance', 'revenue_share', 'joint_venture'])->default('equity');
            $table->string('agreement_no')->unique();
            $table->decimal('committed_amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->date('start_date');
            $table->date('maturity_date')->nullable();
            $table->foreignId('terms_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('active');
            $table->timestamps();
            $table->index(['investor_party_id', 'status']);
        });

        Schema::create('investment_commitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained('investment_agreements')->cascadeOnDelete();
            $table->decimal('amount', 19, 4);
            $table->date('due_date');
            $table->enum('status', ['pending', 'partially_paid', 'paid', 'waived'])->default('pending');
            $table->timestamps();
            $table->index(['agreement_id', 'due_date']);
        });

        Schema::create('investment_contributions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('agreement_id')->constrained('investment_agreements')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 19, 4);
            $table->timestamp('contributed_at');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('distribution_runs', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('run_no')->unique();
            $table->text('description')->nullable();
            $table->date('distribution_date');
            $table->decimal('total_amount', 19, 4)->default(0);
            $table->enum('status', ['draft', 'calculated', 'approved', 'processed', 'cancelled'])->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('distribution_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investor_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('agreement_id')->nullable()->constrained('investment_agreements')->nullOnDelete();
            $table->decimal('distribution_amount', 19, 4);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->timestamps();
            $table->index(['distribution_run_id', 'investor_party_id']);
        });

        Schema::create('shareholders', function (Blueprint $table) {
            $table->foreignId('party_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('shareholder_no')->unique();
            $table->timestamps();
        });

        Schema::create('share_classes', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('class_name')->unique();
            $table->text('description')->nullable();
            $table->decimal('par_value', 19, 4);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // HR & AGENTS
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_no')->unique();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('job_title')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'temporary'])->default('full_time');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'on_leave', 'suspended', 'terminated'])->default('active');
            $table->timestamps();
        });

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('employee_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('party_id')->constrained()->cascadeOnDelete();
            $table->string('agency_no')->unique();
            $table->foreignId('agreement_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
        });

        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->integer('days_per_year')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
        });

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
            $table->unique(['company_id', 'start_date', 'end_date']);
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->string('run_no')->unique();
            $table->decimal('total_gross', 19, 4)->default(0);
            $table->decimal('total_deductions', 19, 4)->default(0);
            $table->decimal('total_net', 19, 4)->default(0);
            $table->enum('status', ['draft', 'submitted', 'approved', 'processed', 'paid'])->default('draft');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('payslip_no')->unique();
            $table->decimal('gross_salary', 19, 4);
            $table->decimal('total_deductions', 19, 4)->default(0);
            $table->decimal('net_salary', 19, 4);
            $table->enum('status', ['draft', 'issued', 'viewed'])->default('draft');
            $table->timestamps();
        });

        // LOANS & LIABILITIES
        Schema::create('lenders', function (Blueprint $table) {
            $table->foreignId('party_id')->primary()->constrained()->cascadeOnDelete();
            $table->enum('lender_type', ['bank', 'private', 'government'])->default('bank');
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lender_party_id')->constrained('parties')->cascadeOnDelete();
            $table->string('facility_no')->unique();
            $table->decimal('principal', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('interest_type', ['fixed', 'variable'])->default('fixed');
            $table->decimal('interest_rate', 10, 4);
            $table->date('start_date');
            $table->date('maturity_date');
            $table->json('collateral_json')->nullable();
            $table->enum('status', ['draft', 'active', 'paid_off', 'defaulted'])->default('active');
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->date('repayment_date');
            $table->decimal('principal_amount', 19, 4);
            $table->decimal('interest_amount', 19, 4);
            $table->decimal('total_amount', 19, 4);
            $table->enum('status', ['scheduled', 'paid', 'overdue', 'waived'])->default('scheduled');
            $table->date('paid_date')->nullable();
            $table->timestamps();
            $table->index(['loan_id', 'repayment_date']);
        });

        // DOCUMENTS & LEGAL
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('document_no')->unique();
            $table->string('title');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'needs_clarification'])->default('pending');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('storage_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->enum('confidentiality_level', ['public', 'internal', 'confidential', 'restricted'])->default('internal');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'verification_status', 'expiry_date']);
        });

        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // SERVICE CHARGES
        Schema::create('service_charge_schemes', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('estate_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('charge_period', ['annual', 'quarterly', 'monthly'])->default('monthly');
            $table->enum('allocation_method', ['equal_per_unit', 'floor_area', 'unit_type', 'consumption', 'fixed_amount', 'custom'])->default('equal_per_unit');
            $table->foreignId('approval_id')->nullable()->constrained('approval_requests')->nullOnDelete();
            $table->enum('status', ['draft', 'active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('service_charge_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('service_charge_schemes')->cascadeOnDelete();
            $table->string('category_name');
            $table->decimal('budgeted_amount', 19, 4)->nullable();
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // REFERENCE DATA
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['company_id', 'name']);
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('lgas', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
            $table->unique(['state_id', 'name']);
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->char('code', 3)->primary();
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->timestamps();
        });

        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->char('from_currency', 3)->constrained('currencies', 'code')->cascadeOnDelete();
            $table->char('to_currency', 3)->constrained('currencies', 'code')->cascadeOnDelete();
            $table->decimal('rate', 18, 8);
            $table->date('effective_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['from_currency', 'to_currency', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('lgas');
        Schema::dropIfExists('states');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('property_types');
        Schema::dropIfExists('service_charge_categories');
        Schema::dropIfExists('service_charge_schemes');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('loan_repayments');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('lenders');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('share_classes');
        Schema::dropIfExists('shareholders');
        Schema::dropIfExists('distribution_lines');
        Schema::dropIfExists('distribution_runs');
        Schema::dropIfExists('investment_contributions');
        Schema::dropIfExists('investment_commitments');
        Schema::dropIfExists('investment_agreements');
        Schema::dropIfExists('investors');
    }
};
