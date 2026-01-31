<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('todo_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action'); // created, updated, deleted, completed, archived
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Tambahkan kolom media ke todos jika belum ada
        Schema::table('todos', function (Blueprint $table) {
            $table->string('media_path')->nullable();
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