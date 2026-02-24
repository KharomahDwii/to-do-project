<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('todo_id')->nullable()->constrained()->onDelete('set null');
        $table->string('action');
        $table->text('description');
        $table->json('metadata')->nullable();
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