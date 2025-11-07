<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('manufacturer')->index();
            $table->decimal('price', 10, 2)->index();
            $table->text('description')->nullable();
            // for CSV idempotence
            $table->string('source_category');
            $table->string('source_id');
            $table->timestamps();
            $table->unique(['source_category','source_id']);
        });

        // PostgreSQL full‑text search column + index
        DB::statement(<<<SQL
            ALTER TABLE products
            ADD COLUMN searchable tsvector GENERATED ALWAYS AS (
                setweight(to_tsvector('simple', coalesce(name,'')), 'A') ||
                setweight(to_tsvector('simple', coalesce(manufacturer,'')), 'B') ||
                setweight(to_tsvector('simple', coalesce(description,'')), 'C')
            ) STORED;
        SQL);
        DB::statement("CREATE INDEX products_searchable_gin ON products USING GIN (searchable);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
