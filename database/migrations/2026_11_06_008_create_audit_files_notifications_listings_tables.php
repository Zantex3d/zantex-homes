<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AUDIT & ACTIVITY
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->longText('before_json')->nullable();
            $table->longText('after_json')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
            $table->index(['actor_id', 'timestamp']);
            $table->index('action');
        });

        Schema::create('activity_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type');
            $table->text('description');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'timestamp']);
        });

        // FILES & DOCUMENT VAULT
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('storage_key')->unique();
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('checksum')->nullable();
            $table->enum('visibility', ['private', 'public', 'restricted'])->default('private');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('owner_scope')->nullable();
            $table->timestamps();
            $table->index(['owner_id', 'visibility']);
        });

        Schema::create('file_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('storage_key')->unique();
            $table->string('checksum')->nullable();
            $table->text('change_description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['file_id', 'version_number']);
        });

        Schema::create('file_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->string('linked_entity_type');
            $table->unsignedBigInteger('linked_entity_id');
            $table->enum('link_type', ['document', 'attachment', 'evidence', 'media', 'evidence'])->default('attachment');
            $table->timestamps();
            $table->unique(['file_id', 'linked_entity_type', 'linked_entity_id']);
            $table->index(['linked_entity_type', 'linked_entity_id']);
        });

        // NUMBERING SEQUENCES
        Schema::create('numbering_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type');
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->unsignedBigInteger('next_value')->default(1);
            $table->integer('padding')->default(6);
            $table->string('period_format')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unique(['company_id', 'entity_type']);
        });

        // APPROVALS
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->string('subject_type');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->integer('sequence');
            $table->string('step_name');
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('minimum_amount', 19, 4)->nullable();
            $table->decimal('maximum_amount', 19, 4)->nullable();
            $table->enum('approval_type', ['single', 'any', 'all'])->default('single');
            $table->timestamps();
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 19, 4)->nullable();
            $table->text('justification')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamps();
            $table->index(['company_id', 'status', 'created_at']);
        });

        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('step_id')->constrained('approval_steps')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('decision', ['approved', 'rejected'])->default('approved');
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->index(['approval_request_id', 'step_id']);
        });

        // NOTIFICATIONS
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('channel', ['in_app', 'email', 'sms', 'whatsapp'])->default('in_app');
            $table->text('subject')->nullable();
            $table->longText('body');
            $table->text('variables_json')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('notification_templates')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('action_url')->nullable();
            $table->enum('status', ['unread', 'read', 'archived'])->default('unread');
            $table->timestamps();
            $table->read_at_timestamp('read_at')->nullable();
            $table->index(['user_id', 'status']);
        });

        Schema::create('notification_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['in_app', 'email', 'sms', 'whatsapp'])->default('in_app');
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->string('provider_ref')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'status', 'created_at']);
        });

        Schema::create('notification_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_type');
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'push'])->default('email');
            $table->boolean('consented')->default(true);
            $table->date('consent_date')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'notification_type', 'channel']);
        });

        // LISTINGS & MARKETING
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('headline');
            $table->longText('description');
            $table->decimal('price', 19, 4);
            $table->char('currency_code', 3)->default('NGN');
            $table->enum('listing_type', ['for_sale', 'for_rent', 'for_shortlet'])->default('for_sale');
            $table->enum('status', ['draft', 'pending_approval', 'published', 'archived', 'sold', 'withdrawn'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('seo_slug')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['company_id', 'status', 'published_at']);
            $table->unique('seo_slug');
        });

        Schema::create('listing_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->enum('media_type', ['photo', 'video', 'floorplan', 'virtual_tour'])->default('photo');
            $table->integer('sort_order')->default(0);
            $table->string('alt_text')->nullable();
            $table->timestamps();
        });

        Schema::create('listing_features', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->enum('feature_type', ['amenity', 'property_type', 'location_feature', 'safety', 'utility'])->default('amenity');
            $table->timestamps();
        });

        Schema::create('listing_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('listing_features')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['listing_id', 'feature_id']);
        });

        Schema::create('public_inquiries', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('inquiry_no')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'lost', 'converted'])->default('new');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['listing_id', 'status', 'created_at']);
        });

        Schema::create('saved_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'listing_id']);
        });

        // REPORTS & ANALYTICS
        Schema::create('kpi_definitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('formula')->nullable();
            $table->text('description')->nullable();
            $table->enum('kpi_type', ['financial', 'operational', 'performance'])->default('operational');
            $table->string('unit')->nullable();
            $table->timestamps();
        });

        Schema::create('dashboard_layouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('layout_json')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('report_type');
            $table->json('filters_json')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('report_runs', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('saved_report_id')->nullable()->constrained()->nullOnDelete();
            $table->string('report_type');
            $table->json('parameters_json')->nullable();
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->string('output_format')->default('pdf');
            $table->string('output_file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_runs');
        Schema::dropIfExists('saved_reports');
        Schema::dropIfExists('dashboard_layouts');
        Schema::dropIfExists('kpi_definitions');
        Schema::dropIfExists('saved_properties');
        Schema::dropIfExists('public_inquiries');
        Schema::dropIfExists('listing_amenities');
        Schema::dropIfExists('listing_features');
        Schema::dropIfExists('listing_media');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('notification_consents');
        Schema::dropIfExists('notification_deliveries');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('approval_actions');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_steps');
        Schema::dropIfExists('approval_workflows');
        Schema::dropIfExists('numbering_sequences');
        Schema::dropIfExists('file_links');
        Schema::dropIfExists('file_versions');
        Schema::dropIfExists('files');
        Schema::dropIfExists('activity_events');
        Schema::dropIfExists('audit_logs');
    }
};
