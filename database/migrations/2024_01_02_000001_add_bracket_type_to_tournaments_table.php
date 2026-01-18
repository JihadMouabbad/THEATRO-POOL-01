<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add bracket format and champion tracking to tournaments table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->enum('bracket_type', ['single_elimination', 'double_elimination', 'round_robin'])
                ->default('single_elimination')
                ->after('status');

            $table->foreignId('champion_id')
                ->nullable()
                ->constrained('players')
                ->onDelete('set null')
                ->after('total_rounds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropForeignIdFor('players', 'champion_id');
            $table->dropColumn('champion_id');
            $table->dropColumn('bracket_type');
        });
    }
};
