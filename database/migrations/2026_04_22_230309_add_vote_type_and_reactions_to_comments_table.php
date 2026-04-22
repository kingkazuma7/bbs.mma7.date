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
        Schema::table('comments', function (Blueprint $table) {
            $table->enum('vote_type', ['strong', 'weak'])->nullable()->after('content');
            $table->unsignedInteger('good_count')->default(0)->after('vote_type');
            $table->unsignedInteger('bad_count')->default(0)->after('good_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['vote_type', 'good_count', 'bad_count']);
        });
    }
};
