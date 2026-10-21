<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Supabase / PostgreSQL and local SQLite.
     * Adds price_effective_date, cost_price_2 (Rate B), and cost_price_3 (Rate C)
     * to catalog_ingestion_jobs, products, and product_variants tables.
     */
    public function up(): void
    {
        // 1. Add effective_date to catalog_ingestion_jobs
        if (Schema::hasTable('catalog_ingestion_jobs') && !Schema::hasColumn('catalog_ingestion_jobs', 'effective_date')) {
            Schema::table('catalog_ingestion_jobs', function (Blueprint $table) {
                $table->string('effective_date')->nullable()->after('filename');
            });
        }

        // 2. Add price_effective_date, cost_price_2, cost_price_3 to products
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'price_effective_date')) {
                    $table->string('price_effective_date')->nullable();
                }
                if (!Schema::hasColumn('products', 'cost_price_2')) {
                    $table->decimal('cost_price_2', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('products', 'cost_price_3')) {
                    $table->decimal('cost_price_3', 10, 2)->nullable();
                }
            });
        }

        // 3. Add price_effective_date, cost_price_2, cost_price_3 to product_variants
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                if (!Schema::hasColumn('product_variants', 'price_effective_date')) {
                    $table->string('price_effective_date')->nullable();
                }
                if (!Schema::hasColumn('product_variants', 'cost_price_2')) {
                    $table->decimal('cost_price_2', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('product_variants', 'cost_price_3')) {
                    $table->decimal('cost_price_3', 10, 2)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('catalog_ingestion_jobs') && Schema::hasColumn('catalog_ingestion_jobs', 'effective_date')) {
            Schema::table('catalog_ingestion_jobs', function (Blueprint $table) {
                $table->dropColumn('effective_date');
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $cols = array_filter(['price_effective_date', 'cost_price_2', 'cost_price_3'], fn($c) => Schema::hasColumn('products', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $cols = array_filter(['price_effective_date', 'cost_price_2', 'cost_price_3'], fn($c) => Schema::hasColumn('product_variants', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
