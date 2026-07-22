<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // DATETIME has no implicit CURRENT_TIMESTAMP behavior on MySQL and
            // stores the local Strava clock value without timezone conversion.
            $table->dateTime('started_at')->change();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->timestamp('started_at')->change();
        });
    }
};
