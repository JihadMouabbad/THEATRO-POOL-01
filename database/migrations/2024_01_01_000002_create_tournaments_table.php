<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the tournaments table.
 * Tournaments are single-elimination 8-ball pool competitions.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('location');
            $table->unsignedTinyInteger('max_players'); // 8, 16, or 32
            $table->enum('status', ['upcoming', 'ongoing', 'finished'])->default('upcoming');
            $table->unsignedTinyInteger('total_rounds')->default(0);
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('status');
            $table->index('start_date');
            $table->index(['status', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
