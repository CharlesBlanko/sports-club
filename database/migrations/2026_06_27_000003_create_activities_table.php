<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('strava_id')->unique();
            $table->string('name');
            $table->string('sport_type')->nullable()->index();
            $table->string('type')->nullable()->index();
            $table->decimal('distance', 12, 2)->default(0);
            $table->unsignedInteger('moving_time')->default(0);
            $table->unsignedInteger('elapsed_time')->default(0);
            $table->decimal('total_elevation_gain', 10, 2)->default(0);
            // This is a Strava event time, not a database-managed timestamp.
            // DATETIME avoids MySQL's legacy implicit CURRENT_TIMESTAMP rules.
            $table->dateTime('started_at')->index();
            $table->string('timezone')->nullable();
            $table->boolean('commute')->default(false);
            $table->boolean('trainer')->default(false);
            $table->boolean('manual')->default(false);
            $table->json('raw')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
