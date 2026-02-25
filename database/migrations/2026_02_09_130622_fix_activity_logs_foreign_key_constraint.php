<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
<<<<<<< HEAD
            $table->foreignId('todo_id')->nullable()->change();
            
            $table->dropForeign(['todo_id']);
            
            $table->foreign('todo_id')
                  ->references('id')
                  ->on('todos')
                  ->onDelete('set null');
=======
            // 1. Pastikan kolom todo_id nullable (harus ada untuk SET NULL)
            $table->foreignId('todo_id')->nullable()->change();
            
            // 2. Hapus constraint lama
            $table->dropForeign(['todo_id']);
            
            // 3. Tambah constraint baru dengan SET NULL
            $table->foreign('todo_id')
                  ->references('id')
                  ->on('todos')
                  ->onDelete('set null'); // ✅ INI SOLUSINYA
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['todo_id']);
            
            $table->foreign('todo_id')
                  ->references('id')
                  ->on('todos')
<<<<<<< HEAD
                  ->onDelete('cascade');
=======
                  ->onDelete('cascade'); // rollback ke cascade
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
        });
    }
};