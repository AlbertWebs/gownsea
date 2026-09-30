<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 100)->default('General');
            $table->text('excerpt');
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->date('published_at')->nullable()->index();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();
        });

        foreach (config('gownsea.journal_posts', []) as $post) {
            DB::table('journal_posts')->insert([
                'title' => $post['title'],
                'slug' => $post['slug'],
                'category' => $post['category'] ?? 'General',
                'excerpt' => $post['excerpt'] ?? '',
                'image' => $post['image'] ?? null,
                'status' => 'published',
                'published_at' => $post['date'] ?? now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_posts');
    }
};
