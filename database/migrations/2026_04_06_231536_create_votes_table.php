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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fighter_id')->constrained('fighters')->onDelete('cascade');
            $table->enum('vote_type', ['strong', 'weak']);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index('fighter_id', 'idx_votes_fighter_id');
            $table->index('created_at', 'idx_votes_created_at');
            $table->index('vote_type', 'idx_votes_type');
            $table->index(['fighter_id', 'vote_type'], 'idx_votes_fighter_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
