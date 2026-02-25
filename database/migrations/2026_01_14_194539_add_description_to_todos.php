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
<<<<<<< HEAD
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        $table->string('action');
        $table->text('description')->nullable();
        $table->timestamps();
=======
    Schema::table('todos', function (Blueprint $table) {
        $table->text('description')->nullable();
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
    });
}

public function down()
{
    Schema::table('todos', function (Blueprint $table) {
        $table->dropColumn('description');
    });
}
};
