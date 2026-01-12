<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the matches table.
 * Stores individual match results within tournaments.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('round'); // 1 = first round, 2 = second, etc.
            $table->unsignedTinyInteger('match_number'); // Position within the round
            $table->foreignId('player1_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('player2_id')->nullable()->constrained('players')->onDelete('set null');
            $table->unsignedTinyInteger('player1_score')->nullable();
            $table->unsignedTinyInteger('player2_score')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('players')->onDelete('set null');
            $table->unsignedInteger('next_match_id')->nullable(); // ID of the next match winner advances to
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['tournament_id', 'round']);
            $table->index(['tournament_id', 'status']);
            $table->index('winner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
