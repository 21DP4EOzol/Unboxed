<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToSwipesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('swipes', function (Blueprint $table) {
            if (!Schema::hasColumn('swipes', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('swipes', 'type')) {
                $table->enum('type', ['product', 'category'])->default('product');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('swipes', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'type']);
        });
    }
}