<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('records', 'apartment_records');

        Schema::table('apartment_records', function (Blueprint $table) {
            $table->foreignId('user_id')->after('apartment_id')->nullable()->constrained()->nullOnDelete();
        });

        DB::table('apartment_records')->update(['user_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartment_records', function (Blueprint $table) {
            $table->dropForeign('apartment_records_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::rename('apartment_records', 'records');
    }
};