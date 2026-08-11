<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // assigned_to sütununu sayılardan oluşacak (kullanıcı ID'si) ve boş bırakılabilir şekilde ekliyoruz
            $table->unsignedBigInteger('assigned_to')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }
};
