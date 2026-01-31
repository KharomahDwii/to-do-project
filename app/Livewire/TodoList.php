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

    protected $listeners = [
        'refresh-todos' => 'loadData'
    ];

    protected $rules = [
        'modalTitle' => 'required|string|max:50',
        'modalDescription' => 'nullable|string|max:1000',
        'modalDeadline' => 'required|date_format:Y-m-d\TH:i',
        'modalCategory' => 'required|in:proker,event,rapat,dana,lainnya',
        'modalMedia' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        'profileAvatarFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'profileName' => 'required|string|max:255', 
    ];

    protected $validationAttributes = [
        'modalTitle' => 'Judul catatan',
        'modalDescription' => 'Isi catatan',
        'modalDeadline' => 'Deadline',
        'modalCategory' => 'Kategori',
        'modalMedia' => 'Lampiran gambar',
        'profileName' => 'Nama pengguna', 
    ];

    public function mount()
    {
        $this->loadData();
        $tomorrow = now()->addDay();
        $this->modalDeadline = $tomorrow->format('Y-m-d\TH:i');

        $user = Auth::user();
        if ($user) {
            $this->profileName = $user->name;
            $this->profileAvatar = $this->getProfilePhotoUrl($user);
        } else {
            $this->profileName = 'Ketua OSIS';
            $this->profileAvatar = $this->getDefaultAvatar('K');
        }
    }

    public function render()
    {
        return view('livewire.todo-list');
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
        return "https://placehold.co/100?text={$firstLetter}";
    }
    
    public function getFilteredTodosProperty()
    {
        if (!$this->todos) {
            return collect();
        }

        if ($this->currentView === 'history') {
            return collect();
        }

        $filtered = $this->todos->filter(function($todo) {
            if ($this->currentFilter === 'completed') {
                return $todo->completed;
            } else {
                if ($this->currentFilter !== 'all') {
                    $category = $this->getCategoryFromMetadata($todo);
                    return !$todo->completed && $category === $this->currentFilter;
                }
                return !$todo->completed;
            }
        });

        if ($this->search) {
            $search = strtolower($this->search);
            $filtered = $filtered->filter(function($todo) use ($search) {
                return str_contains(strtolower($todo->title ?? ''), $search) ||
                       str_contains(strtolower($todo->description ?? ''), $search);
            });
        }

        return $filtered;
    }

    public function loadData()
    {
        if (auth()->check()) {
            $this->todos = auth()->user()->todos()->orderBy('created_at', 'desc')->get();
            $this->activityLogs = auth()->user()->activityLogs()->with('todo')->orderBy('created_at', 'desc')->take(50)->get();
        } else {
            $this->todos = collect();
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
        $this->validate();
        
        try {
            if ($this->editingId) {
                $todo = Todo::where('id', $this->editingId)->where('user_id', auth()->id())->first();
                if ($todo) {
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

                    $this->dispatch('swal', [
                        'title' => 'Berhasil!',
                        'text' => '✅ Catatan berhasil diperbarui!',
                        'icon' => 'success',
                        'timer' => 3000
                    ]);
                } else {
                    $this->dispatch('swal', [
                        'title' => 'Gagal!',
                        'text' => '❌ Catatan tidak ditemukan atau tidak memiliki akses.',
                        'icon' => 'error',
                        'timer' => 3000
                    ]);
                    return;
                }
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
                    'reminder_at' => $this->modalDeadline ? Carbon::parse($this->modalDeadline) : null,
                    'metadata' => $metadata,
                    'media_path' => $mediaPath
                ]);

                $this->logActivity('created', "Menambahkan catatan baru '{$this->modalTitle}'", $todo->id, [
                    'category' => $this->modalCategory,
                    'has_media' => !!$mediaPath
                ]);

                $this->dispatch('swal', [
                    'title' => 'Berhasil!',
                    'text' => '✅ Catatan baru berhasil ditambahkan!',
                    'icon' => 'success',
                    'timer' => 3000
                ]);
            }
            
            $this->closeModal();
            $this->loadData();
            
        } catch (\Exception $e) {
            Log::error('Error saving todo: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => '❌ Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 5000
            ]);
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
                $this->dispatch('swal', [
                    'title' => 'Berhasil!',
                    'text' => '✅ Catatan ditandai sebagai selesai!',
                    'icon' => 'success',
                    'timer' => 2000
                ]);
            } else {
                $this->logActivity('archived', "Mengaktifkan kembali catatan '{$todo->title}'", $todo->id);
                $this->dispatch('swal', [
                    'title' => 'Berhasil!',
                    'text' => '✅ Catatan diaktifkan kembali!',
                    'icon' => 'success',
                    'timer' => 2000
                ]);
            }
            
            $this->loadData();
        } else {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text' => '❌ Catatan tidak ditemukan atau tidak memiliki akses.',
                'icon' => 'error',
                'timer' => 3000
            ]);
        }
    }

    public function deleteTodo($id)
    {
        try {
            $todo = Todo::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
            $todoTitle = $todo->title;
            
            if ($todo->media_path && Storage::disk('public')->exists($todo->media_path)) {
                Storage::disk('public')->delete($todo->media_path);
            }
            
            $todo->delete();
            
            $this->logActivity('deleted', "Menghapus catatan '{$todoTitle}'", $id);
            
            $this->loadData();
            
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text' => '🗑️ Catatan berhasil dihapus!',
                'icon' => 'success',
                'timer' => 2000
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text' => '❌ Catatan tidak ditemukan atau tidak memiliki akses.',
                'icon' => 'error',
                'timer' => 3000
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting todo: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => '❌ Terjadi kesalahan saat menghapus catatan.',
                'icon' => 'error',
                'timer' => 5000
            ]);
        }
    }

    public function openProfileModal()
    {
        $this->showProfileModal = true;
        $user = Auth::user();
        if ($user) {
            $this->profileName = $user->name;
            $this->profileAvatar = $this->getProfilePhotoUrl($user);
            $this->profileAvatarFile = null;
            $this->resetErrorBag(); 
        }
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
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => '❌ Anda harus login untuk mengubah profil.',
                'icon' => 'error',
                'timer' => 3000
            ]);
            return;
        }

        $this->validate();

        try {
            $oldName = $user->name;
            $user->name = $this->profileName;

            if ($this->profileAvatarFile) {

                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $this->profileAvatarFile->store('avatars', 'public');
                $user->profile_photo_path = $path;
            }

            $user->save();

            $this->profileAvatar = $this->getProfilePhotoUrl($user);

            $changes = [];
            if ($oldName !== $this->profileName) {
                $changes[] = "nama dari '{$oldName}' menjadi '{$this->profileName}'";
            }
            if ($this->profileAvatarFile) {
                $changes[] = "foto profil diperbarui";
            }
            
            $changeDesc = !empty($changes) ? implode(', ', $changes) : 'profil diperbarui';
            $this->logActivity('profile_updated', "Memperbarui profil: {$changeDesc}", null, [
                'changes' => $changes,
                'has_avatar' => !!$this->profileAvatarFile
            ]);
            
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text' => '✅ Profil berhasil diperbarui!',
                'icon' => 'success',
                'timer' => 3000
            ]);
            
            $this->closeProfileModal();
            
            $this->dispatch('profile-updated');
            
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => '❌ Terjadi kesalahan saat memperbarui profil.',
                'icon' => 'error',
                'timer' => 5000
            ]);
        }
    }

    public function logout()
    {
        $this->logActivity('logged_out', "Pengguna keluar dari akun", null, [
            'logout_time' => now()->format('Y-m-d H:i:s')
        ]);
        
        Auth::logout();
        session()->flash('message', 'Anda telah keluar dari akun.');
        return redirect('/');
    }
    
    private function logActivity(string $action, string $description, ?int $todoId = null, array $metadata = [])
    {
        try {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'todo_id' => $todoId,
                'action' => $action,
                'description' => $description,
                'metadata' => $metadata
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating activity log: ' . $e->getMessage());
        }
    }
    
    public function changeView($view)
    {
        $this->currentView = $view;
        $this->currentFilter = 'all';
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
}