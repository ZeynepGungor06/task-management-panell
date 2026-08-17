<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('is_completed');
            $table->index('priority');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['is_completed']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['due_date']);

        });
    }
};
