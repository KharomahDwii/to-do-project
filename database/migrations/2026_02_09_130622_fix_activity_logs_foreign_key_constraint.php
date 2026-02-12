<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('todo_id')->nullable()->change();
            
            $table->dropForeign(['todo_id']);
            
            $table->foreign('todo_id')
                  ->references('id')
                  ->on('todos')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['todo_id']);
            
            $table->foreign('todo_id')
                  ->references('id')
                  ->on('todos')
                  ->onDelete('cascade');
        });
    }
};