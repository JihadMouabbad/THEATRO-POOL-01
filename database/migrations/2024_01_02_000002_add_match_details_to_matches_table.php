<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add detailed match information: table number, referee, format, and frames.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedTinyInteger('table_number')->nullable()->after('scheduled_at');

            $table->foreignId('referee_id')
                ->nullable()
                ->constrained('players')
                ->onDelete('set null')
                ->after('table_number');

            $table->enum('match_format', ['race_to', 'best_of'])
                ->default('race_to')
                ->after('referee_id');

            $table->unsignedTinyInteger('frames_to_win')
                ->default(5)
                ->after('match_format');

            $table->boolean('is_admin_override')->default(false)->after('frames_to_win');

            // Indexes
            $table->index('table_number');
            $table->index('referee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeignIdFor('players', 'referee_id');
            $table->dropIndex(['referee_id']);
            $table->dropIndex(['table_number']);
            $table->dropColumn([
                'table_number',
                'referee_id',
                'match_format',
                'frames_to_win',
                'is_admin_override',
            ]);
        });
    }
};
