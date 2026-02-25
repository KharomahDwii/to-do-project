<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
<<<<<<< HEAD
=======
    // database/migrations/xxxx_create_activity_logs_table.php
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
public function up()
{
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('todo_id')->nullable()->constrained()->onDelete('set null');
        $table->string('action');
        $table->text('description');
<<<<<<< HEAD
        $table->json('metadata')->nullable();
=======
        $table->json('metadata')->nullable(); // HARUS JSON!
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('media_path');
        });
    }
};