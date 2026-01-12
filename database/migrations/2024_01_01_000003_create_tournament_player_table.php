<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the tournament_player pivot table.
 * Tracks which players are registered in which tournaments.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournament_player', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('seed')->nullable(); // Player seed/position in bracket
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps(); // Required for withTimestamps() in Eloquent relationships
            
            // Ensure a player can only be registered once per tournament
            $table->unique(['tournament_id', 'player_id']);
            
            // Index for tournament lookups
            $table->index('tournament_id');
            $table->index('player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_player');
    }
};
