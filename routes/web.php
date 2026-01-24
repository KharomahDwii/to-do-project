<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\TodoList;
use App\Models\Todo;
use Illuminate\Http\Request;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
})->name('login.submit');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $user = \App\Models\User::create($validated);
    Auth::login($user);
    return redirect('/');
})->name('register.submit');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', TodoList::class)->name('home');

    Route::post('/livewire/update-todo-completed', function (Request $request) {
        $todo = Todo::find($request->id);
        if ($todo && $todo->user_id === auth()->id()) {
            $todo->completed = $request->completed;
            $todo->save();
        }
        return response()->json(['success' => true]);
    })->name('update-todo-completed');
});