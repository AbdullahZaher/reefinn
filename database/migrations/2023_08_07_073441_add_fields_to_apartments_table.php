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
        Schema::table('apartments', function (Blueprint $table) {
            $table->integer('type')->default(1)->after('name');
            $table->dropColumn('description');
        });

        Schema::table('apartments', function (Blueprint $table) {
            $table->integer('description')->default(21)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('description');
        });

        Schema::table('apartments', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });
    }
};