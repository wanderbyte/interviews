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
        Schema::create('materials', function (Blueprint $table) {
            $table->id(); // Internal material id
            $table->unsignedBigInteger('category_id');
            $table->string('material_name');
            $table->decimal('opening_balance', 10, 2);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at  soft delete column
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
