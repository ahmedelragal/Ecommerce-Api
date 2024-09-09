<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
            $table->index('description');
            $table->index('price');
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['description']);
            $table->dropIndex(['price']);
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });
    }
};
