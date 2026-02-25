<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\TodoList;
use App\Models\Todo;
use Illuminate\Http\Request;

<<<<<<< HEAD
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes([
    'verify' => false,
    'register' => true,
]);

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', TodoList::class)->name('dashboard');
=======
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
    Route::get('/', TodoList::class)->name('dashboard'); // 🔥 Tambahkan name
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
    
    Route::post('/livewire/update-todo-completed', function (Request $request) {
        $todo = Todo::find($request->id);
        if ($todo && $todo->user_id === auth()->id()) {
            $todo->completed = $request->completed;
            $todo->save();
        }
        return response()->json(['success' => true]);
    })->name('update-todo-completed');
});

<<<<<<< HEAD
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.register');
})->name('register');

Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/password/reset/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->name('password.reset');

Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->name('password.update');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/test-email', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    try {
        $todo = \App\Models\Todo::where('user_id', auth()->id())
                    ->where('completed', false)
                    ->whereNotNull('reminder_at')
                    ->first();
                    
        if (!$todo) {
            return response()->json(['error' => 'Tidak ada todo dengan deadline']);
        }
        
        $user = auth()->user();
        $user->notify(new \App\Notifications\DeadlineNotification($todo, 30));
        
        \Log::info('Test email sent', [
            'user' => $user->email,
            'todo' => $todo->title,
            'deadline' => $todo->reminder_at
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Email test berhasil dikirim ke ' . $user->email,
            'todo' => $todo->title,
            'deadline' => $todo->reminder_at->format('d M Y H:i')
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Test email failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'hint' => 'Periksa konfigurasi email di .env dan pastikan App Password Gmail benar'
        ], 500);
    }
})->name('test-email')->middleware('auth');

Route::get('/test-activity-log', function() {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    try {
        $todo = \App\Models\Todo::where('user_id', auth()->id())->first();
        
        if (!$todo) {
            return response()->json(['error' => 'Tidak ada todo untuk diuji']);
        }
        
        $metadata = [
            'id' => $todo->id,
            'title' => $todo->title,
            'category' => $todo->metadata['category'] ?? 'lainnya',
            'deleted_by' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'deleted_at' => now()->format('Y-m-d H:i:s')
        ];
        
        $log = \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'todo_id' => $todo->id,
            'action' => 'deleted',
            'description' => 'Test delete log via route',
            'metadata' => $metadata
        ]);
        
        return response()->json([
            'success' => true,
            'log_id' => $log->id,
            'metadata' => $log->metadata,
            'message' => 'Activity log test berhasil dibuat'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth');
=======
Route::get('/test-activity-log', function() {
    $todo = \App\Models\Todo::first();
    
    $metadata = [
        'id' => $todo->id,
        'title' => $todo->title,
        'category' => 'test',
        'deleted_by' => [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ],
        'deleted_at' => now()->format('Y-m-d H:i:s')
    ];
    
    $log = \App\Models\ActivityLog::create([
        'user_id' => auth()->id(),
        'todo_id' => $todo->id,
        'action' => 'deleted',
        'description' => 'Test delete log',
        'metadata' => $metadata
    ]);
    
    dd([
        'log_created' => $log,
        'metadata_stored' => $log->metadata,
        'metadata_json' => DB::table('activity_logs')->where('id', $log->id)->value('metadata')
    ]);
});
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
