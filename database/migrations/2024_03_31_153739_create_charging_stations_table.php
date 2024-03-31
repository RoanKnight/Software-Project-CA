<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('charging_stations', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
      $table->string('address');
      $table->string('charging_efficiency');
      $table->boolean('deleted')->default(false);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('charging_stations');
  }
};
