<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ACCOUNTING TABLES
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense', 'gain', 'loss'])->default('asset');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->enum('normal_balance', ['debit', 'credit'])->default('debit');
            $table->char('currency_code', 3)->default('NGN');
            $table->boolean('is_control_account')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'account_type']);
        });

        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->timestamps();
            $table->unique(['company_id', 'start_date']);
        });

        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
            $table->integer('period_no');
            $table->enum('period_type', ['monthly', 'quarterly', 'half_yearly', 'annual'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'closed', 'locked'])->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['fiscal_year_id', 'period_no']);
            $table->index('status');
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('journal_no')->unique();
            $table->date('date');
            $table->text('description')->nullable();
            $table->enum('source_type', ['manual', 'rent_invoice', 'sale', 'payment', 'expense', 'purchase', 'loan', 'investment', 'transfer', 'adjustment'])->default('manual');
            $table->foreignId('source_id')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'posted', 'reversed'])->default('draft');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reversal_of_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status', 'date']);
            $table->index('source_type');
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->decimal('debit', 19, 4)->default(0);
            $table->decimal('credit', 19, 4)->default(0);
            $table->char('currency_code', 3)->default('NGN');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->foreignId('party_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->text('narration')->nullable();
            $table->timestamps();
            $table->index(['account_id', 'journal_entry_id']);
        });

        // INVOICES & AR/AP
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('party_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no')->unique();
            $table->date('issue_date');
            $table->date('due_date');
            $table->char('currency_code', 3)->default('NGN');
            $table->decimal('subtotal', 19, 4);
            $table->decimal('tax', 19, 4)->default(0);
            $table->decimal('total', 19, 4);
            $table->decimal('balance', 19, 4);
            $table->enum('status', ['draft', 'issued', 'partial', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->enum('source_type', ['rent', 'sale', 'service_charge', 'expense', 'project', 'other'])->default('rent');
            $table->foreignId('source_id')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status', 'due_date']);
            $table->index('invoice_no');
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->decimal('quantity', 12, 4)->default(1);
            $table->decimal('unit_price', 19, 4);
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('tax_code_id')->nullable()->constrained('tax_codes')->nullOnDelete();
            $table->timestamps();
        });

        // PAYMENTS
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('payment_ref')->unique();
            $table->string('provider');
            $table->string('provider_ref')->nullable();
            $table->foreignId('payer_party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->decimal('amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['initiated', 'pending', 'successful', 'failed', 'abandoned', 'reversed', 'refunded', 'disputed'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->string('idempotency_key')->unique();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status']);
            $table->index('provider_ref');
        });

        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 19, 4);
            $table->enum('allocation_type', ['rent', 'deposit', 'service_charge', 'other'])->default('rent');
            $table->timestamps();
            $table->unique(['payment_id', 'invoice_id']);
            $table->index(['invoice_id', 'payment_id']);
        });

        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('event_id')->unique();
            $table->string('payload_hash');
            $table->longText('payload_json');
            $table->text('signature')->nullable();
            $table->enum('status', ['received', 'validated', 'processed', 'failed', 'ignored'])->default('received');
            $table->timestamp('received_at');
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['provider', 'status', 'received_at']);
        });

        // BANK & TRANSFERS
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('account_no')->unique();
            $table->string('account_name');
            $table->string('bank_name');
            $table->char('currency_code', 3)->default('NGN');
            $table->decimal('current_balance', 19, 4)->default(0);
            $table->enum('status', ['active', 'inactive', 'frozen'])->default('active');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_ref')->unique();
            $table->date('transaction_date');
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'transfer', 'charge', 'interest'])->default('deposit');
            $table->decimal('amount', 19, 4);
            $table->text('description')->nullable();
            $table->enum('status', ['posted', 'pending', 'cancelled'])->default('posted');
            $table->timestamps();
            $table->index(['bank_account_id', 'transaction_date']);
        });

        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('statement_date');
            $table->decimal('statement_opening_balance', 19, 4);
            $table->decimal('statement_closing_balance', 19, 4);
            $table->decimal('system_closing_balance', 19, 4);
            $table->decimal('difference', 19, 4);
            $table->enum('status', ['draft', 'reconciled', 'finalised'])->default('draft');
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['bank_account_id', 'statement_date']);
        });

        // EXPENSES & BUDGETS
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('expense_no')->unique();
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->text('description');
            $table->decimal('amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->date('expense_date');
            $table->enum('status', ['draft', 'submitted', 'approved', 'posted', 'rejected'])->default('draft');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status']);
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'approved', 'active', 'closed'])->default('draft');
            $table->timestamps();
            $table->unique(['company_id', 'fiscal_year_id', 'name']);
        });

        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->timestamps();
            $table->unique(['budget_id', 'account_id']);
        });

        Schema::create('cost_centres', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // TAX
        Schema::create('tax_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->enum('tax_type', ['vat', 'wht', 'corporate_tax', 'personal_tax', 'other'])->default('vat');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_codes');
        Schema::dropIfExists('cost_centres');
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('bank_reconciliations');
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('payment_events');
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounting_periods');
        Schema::dropIfExists('fiscal_years');
        Schema::dropIfExists('accounts');
    }
};
