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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('state_from');
            $table->tinyInteger('state_to');
            $table->text('note')->nullable();
            $table->foreignId('guest_id')->nullable()->references('id')->on('guests');
            $table->foreignId('apartment_id')->references('id')->on('apartments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
