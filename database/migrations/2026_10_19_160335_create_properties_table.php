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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('external_code')->unique();
            $table->string('title');
            $table->decimal('price', 12, 2)->nullable();

            $table->string('city');
            $table->string('state');
            $table->string('neighborhood')->nullable();
            $table->string('address')->nullable();

            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('garage_spaces')->nullable();

            $table->decimal('area_m2', 12, 2)->nullable();

            $table->string('status')->default('available');   // available, sold, rented
            $table->string('source')->default('manual');      // manual, import, api

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('property_type_id')->constrained('property_types');
            $table->index('city');
            $table->index('status');
            $table->index('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
