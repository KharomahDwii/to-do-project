<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Todo;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TodoList extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;
    public $showProfileModal = false;
    public $modalTitle = '';
    public $modalDescription = '';
    public $modalCategory = 'event';
    public $modalDeadline = '';
    public $modalMedia;
    public $editingId = null;

    public $profileName;
    public $profileAvatarFile;
    public $profileAvatar;

    public $todos;
    public $activityLogs;
    public $currentView = 'notes';
    public $currentFilter = 'all';
    public $search = '';

    // Properties untuk hapus riwayat
    public $confirmingLogDeletion = false;
    public $logToDeleteId = null;
    public $logToDeleteTitle = '';
    public $selectAllLogs = false;
    public $selectedLogs = [];

    protected $listeners = [
        'refresh-todos' => 'loadData',
        'profileUpdated' => 'refreshProfile',
        'confirmDeleteLog' => 'confirmDeleteLog',
        'deleteSelectedLogs' => 'deleteSelectedLogs',
    ];

    protected $rules = [
        'modalTitle' => 'required|string|max:50',
        'modalDescription' => 'nullable|string|max:1000',
        'modalDeadline' => 'required|date',
        'modalCategory' => 'required|in:proker,event,rapat,dana,lainnya',
        'modalMedia' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'profileAvatarFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'profileName' => 'required|string|min:3|max:255',
    ];

    protected $validationAttributes = [
        'modalTitle' => 'Judul catatan',
        'modalDescription' => 'Isi catatan',
        'modalDeadline' => 'Deadline',
        'modalCategory' => 'Kategori',
        'modalMedia' => 'Lampiran gambar',
        'profileName' => 'Nama pengguna', 
    ];

    protected $messages = [
        'modalTitle.required' => 'Judul catatan wajib diisi.',
        'modalTitle.max' => 'Judul maksimal 50 karakter.',
        'modalDeadline.required' => 'Deadline wajib diisi.',
        'modalCategory.required' => 'Kategori wajib dipilih.',
        'modalMedia.image' => 'File harus berupa gambar.',
        'modalMedia.max' => 'Ukuran gambar maksimal 2MB.',
        'modalMedia.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau GIF.',
        'profileName.required' => 'Nama pengguna wajib diisi.',
        'profileName.min' => 'Nama pengguna minimal 3 karakter.',
        'profileName.max' => 'Nama pengguna maksimal 255 karakter.',
        'profileAvatarFile.image' => 'File harus berupa gambar.',
        'profileAvatarFile.max' => 'Ukuran gambar maksimal 2MB.',
        'profileAvatarFile.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau GIF.',
    ];

    public function mount()
    {
        $this->loadData();
        $this->modalDeadline = now()->addDay()->format('Y-m-d\TH:i');
        $this->loadProfileData();
    }

    public function render()
    {
        $this->checkUpcomingDeadlines();
        
        if ($this->currentView === 'activity-log') {
            $this->loadActivityLogs();
            $this->selectAllLogs = false;
            $this->selectedLogs = [];
        }
        
        return view('livewire.todo-list');
    }
    
    private function loadProfileData()
    {
        $user = Auth::user();
        if ($user) {
            $user = $user->fresh();
            $this->profileName = $user->name;
            $this->profileAvatar = $this->getProfilePhotoUrl($user);
        } else {
            $this->profileName = 'Ketua OSIS';
            $this->profileAvatar = $this->getDefaultAvatar('K');
        }
    }
    
    private function getProfilePhotoUrl($user)
    {
        if ($user->profile_photo_path) {
            return asset('storage/' . $user->profile_photo_path);
        }
        return $this->getDefaultAvatar(substr($user->name, 0, 1));
    }
    
    private function getDefaultAvatar($letter)
    {
        $firstLetter = strtoupper(substr($letter, 0, 1));
        return "https://placehold.co/100?text=        {$firstLetter}";
    }
    
    public function refreshProfile()
    {
        $this->loadProfileData();
        $this->loadData();
    }

    public function openProfileModal()
    {
        $user = Auth::user();
        if (!$user) {
            session()->flash('error', '❌ Anda harus login untuk mengubah profil.');
            return redirect()->route('login');
        }
        
        $this->showProfileModal = true;
        $user = $user->fresh();
        $this->profileName = $user->name;
        $this->profileAvatar = $this->getProfilePhotoUrl($user);
        $this->profileAvatarFile = null;
        $this->resetErrorBag();
        
        Log::info('Profile modal opened', ['user_id' => $user->id, 'current_name' => $user->name]);
    }

    public function closeProfileModal()
    {
        $this->showProfileModal = false;
        $this->profileAvatarFile = null;
        $this->resetErrorBag();
    }

    public function updatedProfileAvatarFile()
    {
        $this->validateOnly('profileAvatarFile');
    }

    public function saveProfile()
    {
        $user = Auth::user();
        if (!$user) {
            session()->flash('error', '❌ Anda harus login untuk mengubah profil.');
            Log::error('Save profile failed: User not authenticated');
            return;
        }

        try {
            $this->validate([
                'profileName' => 'required|string|min:3|max:255',
                'profileAvatarFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $hasChanges = false;
            $changes = [];
            
            if ($this->profileName !== $user->name) {
                $oldName = $user->name;
                $user->name = $this->profileName;
                $changes[] = "nama dari '{$oldName}' menjadi '{$this->profileName}'";
                $hasChanges = true;
            }

            if ($this->profileAvatarFile) {
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $this->profileAvatarFile->store('avatars', 'public');
                $user->profile_photo_path = $path;
                $changes[] = "foto profil";
                $hasChanges = true;
            }

            if ($hasChanges) {
                $user->save();
                
                $changeDesc = !empty($changes) ? implode(', ', $changes) : 'profil diperbarui';
                $this->logActivity('profile_updated', "Memperbarui profil: {$changeDesc}", null, [
                    'changes' => $changes,
                    'has_avatar' => !!$this->profileAvatarFile,
                    'new_name' => $user->name,
                    'old_name' => $user->getOriginal('name') ?? $user->name
                ]);
                
                $this->closeProfileModal();
                $this->loadData();
                
                session()->flash('message', '✅ Profil berhasil diperbarui!');
                
                Log::info('Profile updated successfully', ['user_id' => $user->id, 'new_name' => $user->name]);
                
            } else {
                $this->closeProfileModal();
                session()->flash('message', 'ℹ️ Tidak ada perubahan yang disimpan.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            session()->flash('error', '❌ Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            $this->logActivity('logged_out', "Pengguna keluar dari akun", null, [
                'logout_time' => now()->toDateTimeString()
            ]);
        }
        
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        
        session()->flash('message', '✅ Anda telah keluar dari akun.');
        return redirect('/');
    }

    public function getFilteredTodosProperty()
    {
        if (!$this->todos) {
            return collect();
        }

        $filtered = $this->currentView === 'history' 
            ? $this->todos->where('completed', true) 
            : $this->todos->where('completed', false);

        if ($this->currentView !== 'history' && $this->currentFilter !== 'all' && $this->currentFilter !== 'completed') {
            $filtered = $filtered->filter(function($todo) {
                return ($todo->metadata['category'] ?? 'lainnya') === $this->currentFilter;
            });
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $filtered = $filtered->filter(function($todo) use ($search) {
                return str_contains(strtolower($todo->title ?? ''), $search) ||
                       str_contains(strtolower($todo->description ?? ''), $search);
            });
        }

        return $filtered->values();
    }

    public function loadData()
    {
        if (auth()->check()) {
            $this->todos = auth()->user()->todos()
                ->orderBy('reminder_at', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $this->loadActivityLogs();
        } else {
            $this->todos = collect();
            $this->activityLogs = collect();
        }
    }
    
    private function loadActivityLogs()
    {
        if (auth()->check()) {
            $this->activityLogs = auth()->user()->activityLogs()
                ->with('todo')
                ->orderBy('created_at', 'desc')
                ->get();
                
            Log::info('Activity logs loaded', [
                'count' => $this->activityLogs->count(),
                'user_id' => auth()->id()
            ]);
        } else {
            $this->activityLogs = collect();
        }
    }
    
    public function openCreateModal()
    {
        $this->resetModal();
        $this->editingId = null;
        $this->showModal = true;
        
        $tomorrow = now()->addDay();
        $this->modalDeadline = $tomorrow->format('Y-m-d\TH:i');
    }

    public function openEditModal($id)
    {
        $todo = Todo::where('id', $id)->where('user_id', auth()->id())->first();
        if ($todo) {
            $this->editingId = $id;
            $this->modalTitle = $todo->title;
            $this->modalDescription = $todo->description;
            $this->modalCategory = $this->getCategoryFromMetadata($todo);
            $this->modalDeadline = $todo->reminder_at ? $todo->reminder_at->format('Y-m-d\TH:i') : now()->addDay()->format('Y-m-d\TH:i');
            $this->modalMedia = null;
            $this->showModal = true;
        } else {
            session()->flash('error', '❌ Catatan tidak ditemukan atau tidak memiliki akses.');
        }
    }

    public function clearMedia()
    {
        $this->modalMedia = null;
        $this->resetErrorBag('modalMedia');
    }

    public function updatedModalMedia()
    {
        $this->validateOnly('modalMedia');
    }

    public function saveModal()
    {
        try {
            $rules = [
                'modalTitle' => 'required|string|max:50',
                'modalDescription' => 'nullable|string|max:1000',
                'modalDeadline' => 'required|date',
                'modalCategory' => 'required|in:proker,event,rapat,dana,lainnya',
                'modalMedia' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
            $this->validate($rules);

            if ($this->editingId) {
                $todo = Todo::where('id', $this->editingId)
                    ->where('user_id', auth()->id())
                    ->first();
                
                if (!$todo) {
                    session()->flash('error', '❌ Catatan tidak ditemukan atau tidak memiliki akses.');
                    return;
                }

                $oldTitle = $todo->title;
                $oldCategory = $this->getCategoryFromMetadata($todo);
                
                $metadata = $todo->metadata ?? [];
                $metadata['category'] = $this->modalCategory;
                
                $mediaPath = $todo->media_path;
                
                if ($this->modalMedia) {
                    if ($todo->media_path && Storage::disk('public')->exists($todo->media_path)) {
                        Storage::disk('public')->delete($todo->media_path);
                    }
                    $mediaPath = $this->modalMedia->store('todos', 'public');
                }
                
                $todo->update([
                    'title' => $this->modalTitle,
                    'description' => $this->modalDescription,
                    'reminder_at' => $this->modalDeadline ? Carbon::parse($this->modalDeadline) : null,
                    'metadata' => $metadata,
                    'media_path' => $mediaPath
                ]);

                $this->logActivity('updated', "Mengedit catatan '{$oldTitle}' menjadi '{$this->modalTitle}'", $todo->id, [
                    'old_category' => $oldCategory,
                    'new_category' => $this->modalCategory,
                    'has_media' => !!$mediaPath
                ]);

                session()->flash('message', '✅ Catatan berhasil diperbarui!');
                
            } else {
                $metadata = ['category' => $this->modalCategory];
                $mediaPath = null;
                
                if ($this->modalMedia) {
                    $mediaPath = $this->modalMedia->store('todos', 'public');
                }
                
                $todo = auth()->user()->todos()->create([
                    'title' => $this->modalTitle,
                    'description' => $this->modalDescription,
                    'completed' => false,
                    'reminder_at' => Carbon::parse($this->modalDeadline),
                    'metadata' => $metadata,
                    'media_path' => $mediaPath
                ]);

                $this->logActivity('created', "Menambahkan catatan baru '{$this->modalTitle}'", $todo->id, [
                    'category' => $this->modalCategory,
                    'has_media' => !!$mediaPath
                ]);

                session()->flash('message', '✅ Catatan baru berhasil ditambahkan!');
            }
            
            $this->closeModal();
            $this->loadData();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving todo: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'todo_id' => $this->editingId ?? 'new',
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '❌ Gagal menyimpan catatan. Silakan coba lagi.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    private function resetModal()
    {
        $this->modalTitle = '';
        $this->modalDescription = '';
        $this->modalCategory = 'event';
        $this->modalDeadline = '';
        $this->modalMedia = null;
        $this->editingId = null;
        $this->resetErrorBag();
    }
    
    public function toggleCompleted($id)
    {
        $todo = Todo::where('id', $id)->where('user_id', auth()->id())->first();
        if ($todo) {
            $wasCompleted = $todo->completed;
            $todo->completed = !$todo->completed;
            $todo->save();
            
            if ($todo->completed) {
                $this->logActivity('completed', "Menandai catatan '{$todo->title}' sebagai selesai", $todo->id);
                session()->flash('message', '✅ Catatan ditandai sebagai selesai!');
            } else {
                $this->logActivity('archived', "Mengaktifkan kembali catatan '{$todo->title}'", $todo->id);
                session()->flash('message', '✅ Catatan diaktifkan kembali!');
            }
            
            $this->loadData();
        } else {
            session()->flash('error', '❌ Catatan tidak ditemukan atau tidak memiliki akses.');
        }
    }

    public function deleteTodo($id)
    {
        try {
            $todo = Todo::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            
            $todoTitle = $todo->title;
            
            $deletedTodoInfo = [
                'id' => $todo->id,
                'title' => $todo->title,
                'category' => $this->getCategoryFromMetadata($todo),
                'deadline' => $todo->reminder_at?->format('Y-m-d H:i:s') ?? null,
                'description_preview' => Str::limit($todo->description ?? '', 50, '...'),
                'had_media' => !empty($todo->media_path),
                'created_at' => $todo->created_at?->format('Y-m-d H:i:s') ?? null,
                'deleted_at' => now()->format('Y-m-d H:i:s'),
                'deleted_by' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name ?? 'User',
                    'email' => auth()->user()->email ?? 'unknown@example.com',
                ]
            ];
            
            if ($todo->media_path && Storage::disk('public')->exists($todo->media_path)) {
                Storage::disk('public')->delete($todo->media_path);
            }
            $this->logActivity('deleted', "Menghapus catatan '{$todoTitle}'", $id, $deletedTodoInfo);
            
            $todo->delete();
            
            $this->loadData();
            
            session()->flash('message', '🗑️ Catatan berhasil dihapus!');
            Log::info('Todo deleted successfully', [
                'todo_id' => $id,
                'user_id' => auth()->id(),
                'title' => $todoTitle
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', '❌ Catatan tidak ditemukan atau tidak memiliki akses.');
            Log::warning('Todo not found for deletion', [
                'todo_id' => $id,
                'user_id' => auth()->id() ?? 'guest'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting todo: ' . $e->getMessage(), [
                'todo_id' => $id,
                'user_id' => auth()->id() ?? 'guest',
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '❌ Terjadi kesalahan saat menghapus catatan.');
        }
    }
    
    private function logActivity(string $action, string $description, ?int $todoId = null, array $metadata = [])
    {
        try {
            if (!auth()->check()) {
                Log::warning('logActivity: User not authenticated');
                return null;
            }

            Log::info('=== PREPARING TO SAVE ACTIVITY LOG ===', [
                'action' => $action,
                'description' => $description,
                'todo_id' => $todoId,
                'user_id' => auth()->id(),
                'metadata' => $metadata
            ]);

            $log = ActivityLog::create([
                'user_id' => auth()->id(),
                'todo_id' => $todoId,
                'action' => $action,
                'description' => $description,
                'metadata' => $metadata
            ]);

            if ($log && $log->wasRecentlyCreated) {
                Log::info('✅ Activity log saved successfully', [
                    'log_id' => $log->id,
                    'action' => $action,
                    'todo_id' => $todoId
                ]);
                
                $this->dispatch('activityLogCreated', [
                    'log_id' => $log->id,
                    'action' => $action
                ]);
            } else {
                Log::warning('⚠️ Activity log may not be saved', [
                    'log_exists' => $log !== null,
                    'was_recent' => $log->wasRecentlyCreated ?? false
                ]);
            }

            return $log;
            
        } catch (\Exception $e) {
            Log::error('❌ Activity log creation FAILED', [
                'error' => $e->getMessage(),
                'action' => $action,
                'description' => $description,
                'todo_id' => $todoId,
                'metadata' => $metadata,
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('warning', '⚠️ Catatan dihapus, tapi riwayat tidak tersimpan.');
        }
    }
    
    public function changeView($view)
    {
        $this->currentView = $view;
        $this->currentFilter = 'all';

        if ($view === 'activity-log') {
            $this->loadActivityLogs();
        }
    }

    public function setFilter($filter)
    {
        $this->currentFilter = $filter;
    }

    public function getCategoryFromMetadata($todo)
    {
        return $todo->metadata['category'] ?? 'lainnya';
    }

    public function getCategoryColor($category)
    {
        $colors = [
            'proker' => 'bg-blue-100 text-blue-800',
            'event' => 'bg-purple-100 text-purple-800 border border-purple-200',
            'rapat' => 'bg-orange-100 text-orange-800',
            'dana' => 'bg-red-100 text-red-800',
            'lainnya' => 'bg-gray-100 text-gray-700'
        ];
        return $colors[$category] ?? 'bg-gray-100 text-gray-700';
    }

    public function getCategoryIcon($category)
    {
        $icons = [
            'proker' => 'ph-briefcase',
            'event' => 'ph-calendar-star',
            'rapat' => 'ph-users',
            'dana' => 'ph-currency-dollar',
            'lainnya' => 'ph-tag'
        ];
        return $icons[$category] ?? 'ph-tag';
    }

    public function checkUpcomingDeadlines()
    {
        if (!auth()->check()) {
            return;
        }

        $now = now();
        $todos = auth()->user()->todos()
            ->where('completed', false)
            ->whereBetween('reminder_at', [$now, $now->copy()->addMinutes(10)])
            ->get();

        foreach ($todos as $todo) {
            if (!$todo->reminder_at) {
                continue;
            }

            $diffMinutes = $now->diffInMinutes($todo->reminder_at, false);
            
            // Session keys untuk mencegah notifikasi berulang
            $notificationKey5min = 'notif_5min_' . $todo->id;
            $notificationKeyDeadline = 'notif_deadline_' . $todo->id;

            // ✅ Notifikasi 5 menit sebelum deadline (HARD-CODED)
            if ($diffMinutes == 5 && !session()->has($notificationKey5min)) {
                $this->dispatch('showNotification', [
                    'title' => '⏰ Pengingat 5 Menit',
                    'message' => "Deadline catatan '{$todo->title}' akan tiba dalam 5 menit!",
                    'type' => 'warning',
                    'icon' => '⏰',
                    'todoId' => $todo->id
                ]);
                session()->put($notificationKey5min, true);
                
                Log::info('5-minute reminder notification sent', [
                    'todo_id' => $todo->id,
                    'title' => $todo->title,
                    'diff_minutes' => $diffMinutes
                ]);
            }

            // ✅ Notifikasi saat deadline tiba
            if ($diffMinutes <= 0 && !session()->has($notificationKeyDeadline)) {
                $this->dispatch('showNotification', [
                    'title' => '🚨 Deadline Tiba!',
                    'message' => "Waktu deadline catatan '{$todo->title}' telah tiba!",
                    'type' => 'error',
                    'icon' => '🚨',
                    'todoId' => $todo->id
                ]);
                session()->put($notificationKeyDeadline, true);
                
                Log::info('Deadline notification sent', [
                    'todo_id' => $todo->id,
                    'title' => $todo->title
                ]);
            }
        }
    }

    public function getUpcomingTodos()
    {
        return Todo::whereNotNull('reminder_at')
            ->where('user_id', auth()->id())
            ->select('id', 'title', 'reminder_at')
            ->get()
            ->toArray();
    }

    public function getUncompletedTodosWithDeadline()
    {
        return auth()->user()->todos()
            ->whereNull('completed_at')
            ->whereNotNull('reminder_at')
            ->select('id', 'title', 'reminder_at')
            ->get();
    }

    public function getLogBorderColor($action)
    {
        $colors = [
            'created' => 'border-green-500',
            'updated' => 'border-blue-500',
            'completed' => 'border-purple-500',
            'deleted' => 'border-red-500',
            'archived' => 'border-yellow-500',
            'profile_updated' => 'border-indigo-500',
            'logged_out' => 'border-gray-500',
            'log_deleted' => 'border-pink-500',
            'logs_batch_deleted' => 'border-pink-500',
            'all_logs_deleted' => 'border-pink-500',
        ];
        return $colors[$action] ?? 'border-gray-300';
    }

    public function getLogBgColor($action)
    {
        $colors = [
            'created' => 'bg-green-500',
            'updated' => 'bg-blue-500',
            'completed' => 'bg-purple-500',
            'deleted' => 'bg-red-500',
            'archived' => 'bg-yellow-500',
            'profile_updated' => 'bg-indigo-500',
            'logged_out' => 'bg-gray-500',
            'log_deleted' => 'bg-pink-500',
            'logs_batch_deleted' => 'bg-pink-500',
            'all_logs_deleted' => 'bg-pink-500',
        ];
        return $colors[$action] ?? 'bg-gray-400';
    }

    public function getLogIcon($action)
    {
        $icons = [
            'created' => 'ph-plus-circle',
            'updated' => 'ph-pencil-circle',
            'completed' => 'ph-check-circle',
            'deleted' => 'ph-trash',
            'archived' => 'ph-archive',
            'profile_updated' => 'ph-user-circle',
            'logged_out' => 'ph-sign-out',
            'log_deleted' => 'ph-trash-simple',
            'logs_batch_deleted' => 'ph-trash',
            'all_logs_deleted' => 'ph-trash',
        ];
        return $icons[$action] ?? 'ph-clock-counter-clockwise';
    }

    public function getLogTitle($action)
    {
        $titles = [
            'created' => 'Catatan Dibuat',
            'updated' => 'Catatan Diedit',
            'completed' => 'Catatan Diselesaikan',
            'deleted' => 'Catatan Dihapus',
            'archived' => 'Catatan Diaktifkan Kembali',
            'profile_updated' => 'Profil Diperbarui',
            'logged_out' => 'Logout',
            'log_deleted' => 'Riwayat Dihapus',
            'logs_batch_deleted' => 'Riwayat Dihapus (Batch)',
            'all_logs_deleted' => 'Semua Riwayat Dihapus',
        ];
        return $titles[$action] ?? 'Aktivitas';
    }

    // ============================================
    // FITUR HAPUS RIWAYAT AKTIVITAS
    // ============================================

    /**
     * Konfirmasi hapus satu riwayat
     */
    public function confirmDeleteLog($logId)
    {
        $log = ActivityLog::where('id', $logId)
            ->where('user_id', auth()->id())
            ->first();
        
        if ($log) {
            $this->logToDeleteId = $logId;
            $this->logToDeleteTitle = $log->description;
            $this->confirmingLogDeletion = true;
        } else {
            session()->flash('error', '❌ Riwayat tidak ditemukan atau tidak memiliki akses.');
        }
    }

    /**
     * Hapus satu riwayat
     */
    public function deleteLog()
    {
        try {
            $log = ActivityLog::where('id', $this->logToDeleteId)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            
            $logDescription = $log->description;
            $logAction = $log->action;
            
            // Simpan info untuk logging
            $deletedLogInfo = [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'metadata' => $log->metadata,
                'created_at' => $log->created_at?->format('Y-m-d H:i:s') ?? null,
                'deleted_at' => now()->format('Y-m-d H:i:s'),
                'deleted_by' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name ?? 'User',
                ]
            ];
            
            $log->delete();
            
            // Log aktivitas penghapusan riwayat
            $this->logActivity('log_deleted', "Menghapus riwayat aktivitas: {$logDescription}", null, [
                'deleted_log_id' => $this->logToDeleteId,
                'deleted_log_action' => $logAction,
                'deleted_log_info' => $deletedLogInfo
            ]);
            
            $this->confirmingLogDeletion = false;
            $this->logToDeleteId = null;
            $this->loadActivityLogs();
            
            session()->flash('message', '🗑️ Riwayat aktivitas berhasil dihapus!');
            Log::info('Activity log deleted successfully', [
                'log_id' => $this->logToDeleteId,
                'user_id' => auth()->id(),
                'action' => $logAction
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', '❌ Riwayat tidak ditemukan atau tidak memiliki akses.');
            Log::warning('Activity log not found for deletion', [
                'log_id' => $this->logToDeleteId,
                'user_id' => auth()->id() ?? 'guest'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting activity log: ' . $e->getMessage(), [
                'log_id' => $this->logToDeleteId,
                'user_id' => auth()->id() ?? 'guest',
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '❌ Terjadi kesalahan saat menghapus riwayat.');
        }
    }

    /**
     * Batalkan konfirmasi hapus
     */
    public function cancelDeleteLog()
    {
        $this->confirmingLogDeletion = false;
        $this->logToDeleteId = null;
        $this->logToDeleteTitle = '';
    }

    /**
     * Toggle select/deselect semua riwayat
     */
    public function toggleSelectAllLogs()
    {
        if ($this->selectAllLogs) {
            $this->selectedLogs = $this->activityLogs->pluck('id')->toArray();
        } else {
            $this->selectedLogs = [];
        }
    }

    /**
     * Hapus riwayat yang dipilih (batch delete)
     */
    public function deleteSelectedLogs()
    {
        if (empty($this->selectedLogs)) {
            session()->flash('warning', '⚠️ Tidak ada riwayat yang dipilih.');
            return;
        }
        
        try {
            $count = count($this->selectedLogs);
            
            // Ambil info logs yang akan dihapus untuk logging
            $deletedLogsInfo = ActivityLog::whereIn('id', $this->selectedLogs)
                ->where('user_id', auth()->id())
                ->get()
                ->map(function($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        'description' => $log->description,
                        'created_at' => $log->created_at?->format('Y-m-d H:i:s') ?? null,
                    ];
                })
                ->toArray();
            
            // Hapus logs
            $deletedCount = ActivityLog::whereIn('id', $this->selectedLogs)
                ->where('user_id', auth()->id())
                ->delete();
            
            // Log aktivitas penghapusan batch
            $this->logActivity('logs_batch_deleted', "Menghapus {$count} riwayat aktivitas", null, [
                'deleted_count' => $deletedCount,
                'deleted_logs' => $deletedLogsInfo,
                'deleted_at' => now()->format('Y-m-d H:i:s')
            ]);
            
            $this->selectedLogs = [];
            $this->selectAllLogs = false;
            $this->loadActivityLogs();
            
            session()->flash('message', "🗑️ Berhasil menghapus {$deletedCount} riwayat aktivitas!");
            Log::info('Batch activity logs deleted', [
                'count' => $deletedCount,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error batch deleting activity logs: ' . $e->getMessage(), [
                'log_ids' => $this->selectedLogs,
                'user_id' => auth()->id() ?? 'guest',
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '❌ Terjadi kesalahan saat menghapus riwayat.');
        }
    }

    /**
     * Konfirmasi hapus semua riwayat
     */
    public function confirmDeleteAllLogs()
    {
        $this->dispatch('confirmDeleteAllLogs');
    }

    /**
     * Hapus semua riwayat
     */
    public function deleteAllLogs()
    {
        try {
            // Hitung jumlah logs sebelum dihapus
            $totalCount = ActivityLog::where('user_id', auth()->id())->count();
            
            if ($totalCount === 0) {
                session()->flash('warning', 'ℹ️ Tidak ada riwayat untuk dihapus.');
                return;
            }
            
            // Ambil info semua logs untuk logging
            $allLogsInfo = ActivityLog::where('user_id', auth()->id())
                ->get()
                ->map(function($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        'description' => $log->description,
                        'created_at' => $log->created_at?->format('Y-m-d H:i:s') ?? null,
                    ];
                })
                ->toArray();
            
            // Hapus semua logs
            ActivityLog::where('user_id', auth()->id())->delete();
            
            // Log aktivitas penghapusan semua riwayat
            $this->logActivity('all_logs_deleted', "Menghapus semua {$totalCount} riwayat aktivitas", null, [
                'deleted_count' => $totalCount,
                'deleted_logs' => $allLogsInfo,
                'deleted_at' => now()->format('Y-m-d H:i:s')
            ]);
            
            $this->loadActivityLogs();
            
            session()->flash('message', "🗑️ Berhasil menghapus semua {$totalCount} riwayat aktivitas!");
            Log::info('All activity logs deleted', [
                'count' => $totalCount,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting all activity logs: ' . $e->getMessage(), [
                'user_id' => auth()->id() ?? 'guest',
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '❌ Terjadi kesalahan saat menghapus semua riwayat.');
        }
    }

    /**
     * Helper untuk cek apakah log dipilih
     */
    public function isLogSelected($logId)
    {
        return in_array($logId, $this->selectedLogs);
    }
}