<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('hire_price_amount')->nullable()->after('sale_price_amount');
        });

        DB::table('products')
            ->where('is_hire', true)
            ->whereNotNull('price_amount')
            ->update(['hire_price_amount' => DB::raw('price_amount')]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('hire_price_amount');
        });
    }
};
