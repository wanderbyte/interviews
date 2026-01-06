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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();

            $table->string('school_name');
            $table->text('school_address');

            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();

            $table->date('establishment_date');
            $table->string('contact_number', 15);

            $table->string('login_id')->unique();
            $table->string('password');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
