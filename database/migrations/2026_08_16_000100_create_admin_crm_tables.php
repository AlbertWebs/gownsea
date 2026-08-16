<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 40)->nullable()->after('email');
            $table->string('role', 40)->default('sales_rep')->after('phone');
            $table->string('status', 20)->default('active')->after('role');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedInteger('price_amount')->nullable();
            $table->string('price_label')->nullable();
            $table->unsignedInteger('sale_price_amount')->nullable();
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->string('availability', 30)->default('in_stock');
            $table->boolean('featured')->default(false);
            $table->string('status', 20)->default('published');
            $table->string('visibility', 20)->default('public');
            $table->string('image')->nullable();
            $table->string('cta')->nullable();
            $table->string('location')->nullable();
            $table->string('url_path')->nullable();
            $table->json('options')->nullable();
            $table->json('details')->nullable();
            $table->json('size_guide')->nullable();
            $table->text('fit_note')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->json('tags')->nullable();
            $table->string('brand')->nullable();
            $table->unsignedInteger('min_order_qty')->default(1);
            $table->boolean('is_hire')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'visibility', 'category_id']);
            $table->index('featured');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone', 40)->nullable();
            $table->string('phone_normalized', 40)->nullable()->index();
            $table->text('address')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('inquiry_id')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('source', 40)->default('manual');
            $table->unsignedInteger('estimated_value')->default(0);
            $table->unsignedTinyInteger('probability')->default(10);
            $table->string('stage', 30)->default('new');
            $table->string('priority', 20)->default('normal');
            $table->timestamp('next_follow_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->string('lost_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['stage', 'assigned_to', 'source']);
        });

        Schema::table('assistant_requests', function (Blueprint $table) {
            $table->string('type', 20)->default('general')->after('message');
            $table->string('source', 40)->default('website')->after('type');
            $table->string('landing_url', 500)->nullable()->after('source');
            $table->foreignId('product_id')->nullable()->after('landing_url')->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            $table->string('status', 30)->default('new')->after('assigned_at');
            $table->string('priority', 20)->default('normal')->after('status');
            $table->timestamp('follow_up_at')->nullable()->after('priority');
            $table->json('tags')->nullable()->after('follow_up_at');
            $table->boolean('is_read')->default(false)->after('tags');
            $table->foreignId('lead_id')->nullable()->after('is_read')->constrained('leads')->nullOnDelete();
            $table->index(['status', 'type', 'created_at']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('inquiry_id')->references('id')->on('assistant_requests')->nullOnDelete();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('salesperson_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 40)->default('manual');
            $table->string('status', 30)->default('draft');
            $table->string('payment_status', 30)->default('unpaid');
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('tax')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'payment_status', 'created_at']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('unit_price')->default(0);
            $table->unsignedInteger('line_total')->default(0);
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->nullableMorphs('subject');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->string('status', 20)->default('completed');
            $table->string('outcome')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['status', 'due_at']);
        });

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('body', 500)->nullable();
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('leads');

        Schema::table('assistant_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn([
                'type', 'source', 'landing_url', 'assigned_at', 'status', 'priority',
                'follow_up_at', 'tags', 'is_read',
            ]);
        });

        Schema::dropIfExists('customers');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'status', 'last_login_at']);
        });
    }
};
