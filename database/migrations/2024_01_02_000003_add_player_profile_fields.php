<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add player profile fields: photo and ranking points.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('phone');
            $table->unsignedInteger('ranking_points')->default(1000)->after('notes');

            // Index for sorting by ranking
            $table->index('ranking_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex(['ranking_points']);
            $table->dropColumn(['photo_path', 'ranking_points']);
        });
    }
};
