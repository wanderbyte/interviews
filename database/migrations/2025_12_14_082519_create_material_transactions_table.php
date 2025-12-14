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
        Schema::create('material_transactions', function (Blueprint $table) {
            $table->id(); // Internal inward id
            $table->unsignedBigInteger('material_id');
            $table->date('transaction_date');
            $table->decimal('quantity', 10, 2);
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at  soft delete column
            $table->foreign('material_id')->references('id')->on('materials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_transactions');
    }
};
