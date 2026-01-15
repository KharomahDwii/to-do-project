<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('todos', function (Blueprint $table) {
        $table->boolean('notified')->default(false)->after('reminder_at');
    });
}

public function down()
{
    Schema::table('todos', function (Blueprint $table) {
        $table->dropColumn('notified');
    });
}
};