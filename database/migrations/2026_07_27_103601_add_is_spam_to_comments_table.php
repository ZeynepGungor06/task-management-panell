<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('task_comments', function (Blueprint $table) {
            $table->boolean('is_spam')->default(false)->after('comment');
        });
    }

    
    public function down(): void
    {
        Schema::table('task_comments', function (Blueprint $table) {
            $table->dropColumn('is_spam');
        });
    }
};
