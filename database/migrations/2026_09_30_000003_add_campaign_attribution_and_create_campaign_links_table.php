<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_visits', function (Blueprint $table): void {
            $table->string('utm_medium', 120)->nullable()->index();
            $table->string('utm_campaign', 190)->nullable()->index();
            $table->string('utm_content', 190)->nullable();
            $table->string('utm_term', 190)->nullable();
        });

        Schema::create('campaign_links', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('destination_path', 500);
            $table->string('destination_label', 190);
            $table->string('utm_source', 120)->index();
            $table->string('utm_medium', 120)->default('paid_social')->index();
            $table->string('utm_campaign', 190)->index();
            $table->string('utm_content', 190)->nullable();
            $table->string('utm_term', 190)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_links');

        Schema::table('site_visits', function (Blueprint $table): void {
            $table->dropColumn(['utm_medium', 'utm_campaign', 'utm_content', 'utm_term']);
        });
    }
};
