<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PROJECTS & CONSTRUCTION
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estate_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('project_code')->unique();
            $table->string('name');
            $table->enum('project_type', ['residential', 'commercial', 'mixed', 'infrastructure'])->default('residential');
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->decimal('approved_budget', 19, 4)->nullable();
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['planning', 'active', 'suspended', 'completed', 'cancelled'])->default('planning');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status']);
        });

        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->timestamps();
        });

        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->integer('progress_percentage')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed', 'cancelled'])->default('pending');
            $table->date('completion_date')->nullable();
            $table->timestamps();
            $table->index(['project_id', 'due_date']);
        });

        Schema::create('boqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('boq_no')->unique();
            $table->integer('version')->default(1);
            $table->decimal('total_amount', 19, 4)->nullable();
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['draft', 'submitted', 'approved', 'active', 'superseded'])->default('draft');
            $table->timestamps();
        });

        Schema::create('contractors', function (Blueprint $table) {
            $table->foreignId('party_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('contractor_code')->unique();
            $table->decimal('performance_rating', 3, 1)->nullable();
            $table->enum('status', ['approved', 'suspended', 'banned'])->default('approved');
            $table->timestamps();
        });

        Schema::create('project_variations', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('variation_no')->unique();
            $table->text('description');
            $table->decimal('variation_amount', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'implemented'])->default('draft');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('site_diaries', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('diary_date');
            $table->text('report');
            $table->integer('labour_count')->nullable();
            $table->text('weather_notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['project_id', 'diary_date']);
        });

        // MAINTENANCE & EQUIPMENT
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('asset_tag')->unique();
            $table->string('category');
            $table->string('serial_no')->nullable();
            $table->date('installed_at')->nullable();
            $table->date('warranty_end')->nullable();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'beyond_repair'])->default('good');
            $table->enum('status', ['active', 'inactive', 'for_disposal'])->default('active');
            $table->timestamps();
        });

        Schema::create('maintenance_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ticket_no')->unique();
            $table->foreignId('requester_party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->string('category');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('description');
            $table->enum('status', ['open', 'assigned', 'in_progress', 'completed', 'closed', 'cancelled'])->default('open');
            $table->timestamp('sla_due_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('ticket_id')->constrained('maintenance_tickets')->cascadeOnDelete();
            $table->string('work_order_no')->unique();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['scheduled', 'started', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('approval_id')->nullable()->constrained('approval_requests')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('preventive_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name');
            $table->text('description')->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            $table->date('next_due_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // PROCUREMENT & INVENTORY
        Schema::create('suppliers', function (Blueprint $table) {
            $table->foreignId('party_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('supplier_code')->unique();
            $table->string('tax_id')->nullable();
            $table->text('bank_details_encrypted')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });

        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('requisition_no')->unique();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('required_date')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'ordered'])->default('draft');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('po_no')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->decimal('total_amount', 19, 4)->nullable();
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('status', ['draft', 'submitted', 'approved', 'sent', 'received', 'invoiced', 'closed', 'cancelled'])->default('draft');
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('grn_no')->unique();
            $table->foreignId('po_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->date('receipt_date');
            $table->enum('status', ['received', 'inspected', 'accepted', 'rejected', 'partial'])->default('received');
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit_of_measure');
            $table->decimal('reorder_level', 12, 4)->default(0);
            $table->enum('status', ['active', 'inactive', 'obsolete'])->default('active');
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->enum('movement_type', ['receipt', 'issue', 'transfer', 'adjustment', 'return', 'disposal'])->default('receipt');
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_cost', 19, 4)->nullable();
            $table->date('movement_date');
            $table->text('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['stock_item_id', 'warehouse_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_requisitions');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('preventive_plans');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('maintenance_tickets');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('site_diaries');
        Schema::dropIfExists('project_variations');
        Schema::dropIfExists('contractors');
        Schema::dropIfExists('boqs');
        Schema::dropIfExists('project_milestones');
        Schema::dropIfExists('project_phases');
        Schema::dropIfExists('projects');
    }
};
