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
        Schema::create('electricity_usages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('location_MPRN');

            $table->foreign('location_MPRN')->references('MPRN')->on('locations');
            
            $table->boolean('deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_usages');
    }
};
