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
    Schema::create('locations', function (Blueprint $table) {
      $table->string('MPRN')->primary();
      $table->timestamps();
      $table->string('address');
      $table->string('EirCode');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->boolean('deleted')->default(false);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('locations');
  }
};