<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TodoList;
use App\Models\Todo;
use Illuminate\Http\Request;

Route::get('/', TodoList::class);

Route::post('/livewire/update-todo-completed', function (Request $request) {
    $todo = Todo::find($request->id);
    if ($todo) {
        $todo->completed = $request->completed;
        $todo->save();
    }
    return response()->json(['success' => true]);
})->name('update-todo-completed');