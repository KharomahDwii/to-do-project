<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
<<<<<<< HEAD
=======
// database/migrations/xxxx_add_reminder_minutes_to_todos_table.php
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
public function up()
{
    Schema::table('todos', function (Blueprint $table) {
        $table->integer('reminder_minutes')->nullable()->after('reminder_at');
    });
}

public function down()
{
    Schema::table('todos', function (Blueprint $table) {
        $table->dropColumn('reminder_minutes');
    });
}
};