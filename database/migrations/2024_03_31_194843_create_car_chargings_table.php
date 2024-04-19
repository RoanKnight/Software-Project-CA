<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up()
  {
    Schema::create('car_chargings', function (Blueprint $table) {
      $table->id();
      $table->timestamp('start_time');
      $table->timestamp('end_time');
      $table->decimal('charging_amount', 8, 2);
      $table->string('location_MPRN');
      $table->foreign('location_MPRN')->references('MPRN')->on('locations')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('car_chargings');
  }
};
