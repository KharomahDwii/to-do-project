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
use App\Notifications\DeadlineNotification;
use App\Notifications\TodoCreatedNotification;
use App\Notifications\ActivityLogNotification;
use Illuminate\Support\Facades\DB;

class TodoList extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;
    public $showProfileModal = false;
    public $showCategoryModal = false;
    public $showPjModal = false; 

    public $modalTitle = '';
    public $modalDescription = '';
    public $modalCategory = 'event';
    public $modalPjId = '';
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
    public $currentCategoryFilter = 'all';
    public $search = '';

    public $editingCategoryId = null;
    public $categoryName = '';
    public $categoryColor = '#667eea';
    public $categories = [];

    public $editingPjId = null;
    public $pjName = '';
    public $pjRole = '';
    public $pjs = []; 

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

        'confirmDeleteCategory' => 'confirmDeleteCategory', 
        'deleteCategoryConfirmed' => 'deleteCategoryConfirmed',

        'confirmDeletePj' => 'confirmDeletePj',
        'deletePjConfirmed' => 'deletePjConfirmed',

        'closeDeleteConfirmation' => 'closeDeleteConfirmation',
    ];

    protected $rules = [
        'modalTitle' => 'required|string|max:50',
        'modalDescription' => 'nullable|string|max:1000',
        'modalDeadline' => 'required|date',
        'modalCategory' => 'required|string',
        'modalMedia' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'profileAvatarFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'profileName' => 'required|string|min:3|max:255',
        
        'categoryName' => 'required|string|max:50',
        'categoryColor' => 'required|string|regex:/^#([a-fA-F0-9]{6})$/',
        
        'pjName' => 'required|string|max:100',
        'pjRole' => 'nullable|string|max:100',
    ];

    protected $validationAttributes = [
        'modalTitle' => 'Judul catatan',
        'modalDescription' => 'Isi catatan',
        'modalDeadline' => 'Deadline',
        'modalCategory' => 'Kategori',
        'modalMedia' => 'Lampiran gambar',
        'profileName' => 'Nama pengguna',
        'categoryName' => 'Nama kategori',
        'categoryColor' => 'Warna kategori',
        'pjName' => 'Nama Penanggung Jawab',
        'pjRole' => 'Jabatan/Peran',
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
        'categoryName.required' => 'Nama kategori wajib diisi.',
        'categoryName.max' => 'Nama kategori maksimal 50 karakter.',
        'categoryColor.required' => 'Warna kategori wajib dipilih.',
        'categoryColor.regex' => 'Format warna harus hex (contoh: #667eea)',
        'pjName.required' => 'Nama PJ wajib diisi.',
        'pjName.max' => 'Nama PJ maksimal 100 karakter.',
    ];

    public function mount()
    {
        $this->loadData();
        $this->modalDeadline = now()->addDay()->format('Y-m-d\TH:i');
        $this->loadProfileData();
        $this->loadCategories();
        $this->loadPjs();
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

    private function loadCategories()
    {
        try {
            $defaultCategories = [
                ['id' => 'proker', 'name' => 'Program Kerja', 'color' => '#667eea', 'is_default' => true],
                ['id' => 'event', 'name' => 'Event Sekolah', 'color' => '#764ba2', 'is_default' => true],
                ['id' => 'rapat', 'name' => 'Rapat', 'color' => '#f093fb', 'is_default' => true],
                ['id' => 'dana', 'name' => 'Pengelolaan Dana', 'color' => '#fa709a', 'is_default' => true],
                ['id' => 'lainnya', 'name' => 'Lainnya', 'color' => '#4facfe', 'is_default' => true]
            ];
            $customCategories = session('custom_categories', []);
            if (!is_array($customCategories)) {
                $customCategories = [];
                session(['custom_categories' => []]);
            }
            $this->categories = array_merge($defaultCategories, array_values($customCategories));
        } catch (\Exception $e) {
            Log::error('Error loading categories', ['error' => $e->getMessage()]);
            $this->categories = $defaultCategories;
        }
    }

    private function saveCategories()
    {
        try {
            $customCategories = array_filter($this->categories, function($cat) {
                return !isset($cat['is_default']) || !$cat['is_default'];
            });
            $validatedCategories = [];
            foreach ($customCategories as $cat) {
                if (isset($cat['id'], $cat['name'], $cat['color'])) {
                    $validatedCategories[] = [
                        'id' => $cat['id'],
                        'name' => $cat['name'],
                        'color' => $cat['color']
                    ];
                }
            }
            session(['custom_categories' => array_values($validatedCategories)]);
        } catch (\Exception $e) {
            Log::error('Error saving categories', ['error' => $e->getMessage()]);
        }
    }

    public function openCategoryModal($categoryId = null)
    {
        $this->resetErrorBag();
        if ($categoryId) {
            $category = collect($this->categories)->firstWhere('id', $categoryId);
            if ($category) {
                $this->editingCategoryId = $categoryId;
                $this->categoryName = $category['name'];
                $this->categoryColor = $category['color'];
                $this->showCategoryModal = true;
            } else {
                session()->flash('error', '❌ Kategori tidak ditemukan.');
            }
        } else {
            $this->resetCategoryModal();
            $this->showCategoryModal = true;
        }
    }

    private function resetCategoryModal()
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categoryColor = '#667eea';
        $this->resetErrorBag(['categoryName', 'categoryColor']);
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->resetCategoryModal();
    }

    public function saveCategory()
    {
        try {
            $this->validate([
                'categoryName' => 'required|string|max:50',
                'categoryColor' => 'required|string|regex:/^#([a-fA-F0-9]{6})$/'
            ]);

            $this->categoryName = trim(strip_tags($this->categoryName));

            if ($this->editingCategoryId) {
                $index = null;
                foreach ($this->categories as $key => $cat) {
                    if ($cat['id'] === $this->editingCategoryId) {
                        $index = $key;
                        break;
                    }
                }
                if ($index === null) throw new \Exception('Kategori tidak ditemukan.');

                $oldName = $this->categories[$index]['name'];
                $isDefault = isset($this->categories[$index]['is_default']) && $this->categories[$index]['is_default'];

                if ($isDefault) {
                    $this->categories[$index]['color'] = $this->categoryColor;
                    $message = "✅ Warna kategori '{$oldName}' berhasil diubah!";
                } else {
                    $this->categories[$index]['name'] = $this->categoryName;
                    $this->categories[$index]['color'] = $this->categoryColor;
                    $this->logActivity('category_updated', "Mengubah kategori '{$oldName}' menjadi '{$this->categoryName}'", null, [
                        'category_id' => $this->editingCategoryId,
                        'old_name' => $oldName,
                        'new_name' => $this->categoryName,
                        'new_color' => $this->categoryColor
                    ]);
                    $message = "✅ Kategori berhasil diperbarui!";
                }
                session()->flash('message', $message);
            } else {
                $newId = 'custom_' . Str::slug(Str::lower($this->categoryName), '_') . '_' . Str::random(6);
                $existingIds = collect($this->categories)->pluck('id')->toArray();
                $counter = 1;
                $originalId = $newId;
                while (in_array($newId, $existingIds)) {
                    $newId = $originalId . '_' . $counter;
                    $counter++;
                }
                $newCategory = ['id' => $newId, 'name' => $this->categoryName, 'color' => $this->categoryColor];
                $this->categories[] = $newCategory;
                $this->logActivity('category_created', "Menambahkan kategori baru '{$this->categoryName}'", null, [
                    'category_id' => $newId,
                    'category_name' => $this->categoryName,
                    'category_color' => $this->categoryColor
                ]);
                session()->flash('message', '✅ Kategori baru berhasil ditambahkan!');
            }
            $this->saveCategories();
            $this->closeCategoryModal();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving category', ['error' => $e->getMessage()]);
            session()->flash('error', '❌ ' . $e->getMessage());
        }
    }

    public function confirmDeleteCategory($categoryId)
    {
        $category = collect($this->categories)->firstWhere('id', $categoryId);
        $name = $category ? $category['name'] : 'ini';
        
        $this->dispatch('openDeleteConfirmation', [
            'type' => 'category',
            'id' => $categoryId,
            'name' => $name
        ]);
    }

    public function deleteCategoryConfirmed($categoryId)
    {
        try {
            $category = collect($this->categories)->firstWhere('id', $categoryId);
            if (!$category) throw new \Exception('Kategori tidak ditemukan.');
            if (isset($category['is_default']) && $category['is_default']) throw new \Exception('Kategori default tidak bisa dihapus.');

            $categoryName = $category['name'];
            $this->categories = array_values(array_filter($this->categories, function($cat) use ($categoryId) {
                return $cat['id'] !== $categoryId;
            }));
            $this->saveCategories();

            if ($this->currentCategoryFilter === $categoryId) $this->currentCategoryFilter = 'all';

            $affected = Todo::where('user_id', auth()->id())
                ->whereJsonContains('metadata->category', $categoryId)
                ->update(['metadata' => DB::raw("JSON_SET(metadata, '$.category', '\"lainnya\"')")]);

            $this->logActivity('category_deleted', "Menghapus kategori '{$categoryName}'" . ($affected > 0 ? " dan memindahkan {$affected} catatan ke 'Lainnya'" : ""), null, [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'moved_todos_count' => $affected
            ]);
            session()->flash('message', "🗑️ Kategori '{$categoryName}' berhasil dihapus!");
            $this->loadData();
        } catch (\Exception $e) {
            Log::error('Error deleting category', ['error' => $e->getMessage()]);
            session()->flash('error', '❌ ' . $e->getMessage());
        }
    }

    public function setCategoryFilter($categoryId)
    {
        if ($categoryId !== 'all') {
            $exists = collect($this->categories)->contains('id', $categoryId);
            if (!$exists) {
                session()->flash('warning', '⚠️ Kategori tidak ditemukan.');
                $categoryId = 'all';
            }
        }
        $this->currentCategoryFilter = $categoryId;
        $this->currentFilter = 'all';
    }

    private function loadPjs()
    {
        try {
            $customPjs = session('custom_pjs', []);
            if (!is_array($customPjs)) {
                $customPjs = [];
                session(['custom_pjs' => []]);
            }
            $this->pjs = array_values($customPjs);
        } catch (\Exception $e) {
            Log::error('Error loading PJs', ['error' => $e->getMessage()]);
            $this->pjs = [];
        }
    }

    private function savePjs()
    {
        try {
            $validatedPjs = [];
            foreach ($this->pjs as $pj) {
                if (isset($pj['id'], $pj['name'])) {
                    $validatedPjs[] = [
                        'id' => $pj['id'],
                        'name' => $pj['name'],
                        'role' => $pj['role'] ?? ''
                    ];
                }
            }
            session(['custom_pjs' => $validatedPjs]);
        } catch (\Exception $e) {
            Log::error('Error saving PJs', ['error' => $e->getMessage()]);
        }
    }

    public function openPjModal($pjId = null)
    {
        $this->resetErrorBag();
        if ($pjId) {
            $pj = collect($this->pjs)->firstWhere('id', $pjId);
            if ($pj) {
                $this->editingPjId = $pjId;
                $this->pjName = $pj['name'];
                $this->pjRole = $pj['role'] ?? '';
                $this->showPjModal = true;
            } else {
                session()->flash('error', '❌ Data PJ tidak ditemukan.');
            }
        } else {
            $this->resetPjModal();
            $this->showPjModal = true;
        }
    }

    private function resetPjModal()
    {
        $this->editingPjId = null;
        $this->pjName = '';
        $this->pjRole = '';
        $this->resetErrorBag(['pjName', 'pjRole']);
    }

    public function closePjModal()
    {
        $this->showPjModal = false;
        $this->resetPjModal();
    }

    public function savePj()
    {
        try {
            $this->validate([
                'pjName' => 'required|string|max:100',
                'pjRole' => 'nullable|string|max:100',
            ]);

            $this->pjName = trim(strip_tags($this->pjName));
            $this->pjRole = trim(strip_tags($this->pjRole));

            if ($this->editingPjId) {
                $index = null;
                foreach ($this->pjs as $key => $item) {
                    if ($item['id'] === $this->editingPjId) {
                        $index = $key;
                        break;
                    }
                }
                if ($index === null) throw new \Exception('Data PJ tidak ditemukan.');

                $oldName = $this->pjs[$index]['name'];
                $this->pjs[$index]['name'] = $this->pjName;
                $this->pjs[$index]['role'] = $this->pjRole;
                
                $this->logActivity('pj_updated', "Mengubah PJ '{$oldName}' menjadi '{$this->pjName}'", null, [
                    'pj_id' => $this->editingPjId,
                    'old_name' => $oldName,
                    'new_name' => $this->pjName,
                    'new_role' => $this->pjRole
                ]);
                session()->flash('message', "✅ Data PJ berhasil diperbarui!");
            } else {
                $newId = 'pj_' . Str::slug(Str::lower($this->pjName), '_') . '_' . Str::random(6);
                $existingIds = collect($this->pjs)->pluck('id')->toArray();
                $counter = 1;
                $originalId = $newId;
                while (in_array($newId, $existingIds)) {
                    $newId = $originalId . '_' . $counter;
                    $counter++;
                }
                
                $newPj = ['id' => $newId, 'name' => $this->pjName, 'role' => $this->pjRole];
                $this->pjs[] = $newPj;
                
                $this->logActivity('pj_created', "Menambahkan PJ baru '{$this->pjName}'", null, [
                    'pj_id' => $newId,
                    'pj_name' => $this->pjName,
                    'pj_role' => $this->pjRole
                ]);
                session()->flash('message', '✅ PJ baru berhasil ditambahkan!');
            }
            
            $this->savePjs();
            $this->closePjModal();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving PJ', ['error' => $e->getMessage()]);
            session()->flash('error', '❌ ' . $e->getMessage());
        }
    }

    public function confirmDeletePj($pjId)
    {
        $pj = collect($this->pjs)->firstWhere('id', $pjId);
        $name = $pj ? $pj['name'] : 'ini';

        $this->dispatch('openDeleteConfirmation', [
            'type' => 'pj',
            'id' => $pjId,
            'name' => $name
        ]);
    }

    public function deletePjConfirmed($pjId)
    {
        try {
            $pj = collect($this->pjs)->firstWhere('id', $pjId);
            if (!$pj) throw new \Exception('Data PJ tidak ditemukan.');

            $pjName = $pj['name'];
            $this->pjs = array_values(array_filter($this->pjs, function($item) use ($pjId) {
                return $item['id'] !== $pjId;
            }));
            $this->savePjs();

            $affected = Todo::where('user_id', auth()->id())
                ->whereJsonContains('metadata->pj_id', $pjId)
                ->update(['metadata' => DB::raw("JSON_REMOVE(metadata, '$.pj_id')")]);

            $this->logActivity('pj_deleted', "Menghapus PJ '{$pjName}'" . ($affected > 0 ? " dari {$affected} catatan" : ""), null, [
                'pj_id' => $pjId,
                'pj_name' => $pjName,
                'affected_todos' => $affected
            ]);
            
            session()->flash('message', "🗑️ PJ '{$pjName}' berhasil dihapus!");
            $this->loadData();
        } catch (\Exception $e) {
            Log::error('Error deleting PJ', ['error' => $e->getMessage()]);
            session()->flash('error', '❌ ' . $e->getMessage());
        }
    }

    public function getPjName($pjId)
    {
        if (!$pjId) return null;
        $pj = collect($this->pjs)->firstWhere('id', $pjId);
        return $pj ? ($pj['name'] . ($pj['role'] ? ' (' . $pj['role'] . ')' : '')) : 'Unknown';
    }

    public function performDelete($type, $id)
    {
        if ($type === 'category') {
            $this->deleteCategoryConfirmed($id);
        } elseif ($type === 'pj') {
            $this->deletePjConfirmed($id);
        }

        $this->dispatch('closeDeleteConfirmation');
    }
    
    public function closeDeleteConfirmation()
    {

    }

    public function checkUpcomingDeadlines()
    {
        if (!auth()->check()) return;
        try {
            $now = now();
            $user = auth()->user();

            $todos30min = $user->todos()
                ->where('completed', false)
                ->whereNotNull('reminder_at')
                ->whereBetween('reminder_at', [$now->copy()->addMinutes(29), $now->copy()->addMinutes(31)])
                ->get();
            foreach ($todos30min as $todo) {
                $key = 'email_30min_' . $todo->id;
                if (!session()->has($key)) {
                    try {
                        $user->notify(new DeadlineNotification($todo, 30));
                        session()->put($key, true);
                    } catch (\Exception $e) {
                        Log::error('Failed to send 30min email', ['error' => $e->getMessage()]);
                    }
                }
            }

            $todos5min = $user->todos()
                ->where('completed', false)
                ->whereNotNull('reminder_at')
                ->whereBetween('reminder_at', [$now->copy()->addMinutes(4), $now->copy()->addMinutes(6)])
                ->get();
            foreach ($todos5min as $todo) {
                $key = 'notif_5min_' . $todo->id;
                if (!session()->has($key)) {
                    $this->dispatch('showNotification', [
                        'title' => '⏰ Pengingat 5 Menit',
                        'message' => "Deadline catatan '{$todo->title}' akan tiba dalam 5 menit!",
                        'type' => 'warning',
                        'icon' => '⏰',
                        'todoId' => $todo->id
                    ]);
                    session()->put($key, true);
                }
            }

            $overdueTodos = $user->todos()
                ->where('completed', false)
                ->whereNotNull('reminder_at')
                ->where('reminder_at', '<=', $now)
                ->where('reminder_at', '>=', $now->subMinutes(5))
                ->get();
            foreach ($overdueTodos as $todo) {
                $emailKey = 'email_overdue_' . $todo->id;
                if (!session()->has($emailKey)) {
                    try {
                        $user->notify(new DeadlineNotification($todo, 0));
                        session()->put($emailKey, true);
                    } catch (\Exception $e) {
                        Log::error('Failed to send overdue email', ['error' => $e->getMessage()]);
                    }
                }
                $notifKey = 'notif_overdue_' . $todo->id;
                if (!session()->has($notifKey)) {
                    $this->dispatch('showNotification', [
                        'title' => '🚨 Deadline Tiba!',
                        'message' => "Waktu deadline catatan '{$todo->title}' telah tiba!",
                        'type' => 'error',
                        'icon' => '🚨',
                        'todoId' => $todo->id
                    ]);
                    session()->put($notifKey, true);
                }
            }
        } catch (\Exception $e) {
            Log::error('Deadline check error', ['error' => $e->getMessage()]);
        }
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
        return "https://placehold.co/100?text={$firstLetter}";
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
            session()->flash('error', '❌ Anda harus login.');
            return redirect()->route('login');
        }
        $this->showProfileModal = true;
        $user = $user->fresh();
        $this->profileName = $user->name;
        $this->profileAvatar = $this->getProfilePhotoUrl($user);
        $this->profileAvatarFile = null;
        $this->resetErrorBag();
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
            session()->flash('error', '❌ Anda harus login.');
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
                $this->logActivity('profile_updated', "Memperbarui profil: {$changeDesc}", null, ['changes' => $changes, 'new_name' => $user->name]);
                $user->notify(new ActivityLogNotification($changeDesc, 'profile_updated'));
                $this->closeProfileModal();
                $this->loadData();
                session()->flash('message', '✅ Profil berhasil diperbarui!');
            } else {
                $this->closeProfileModal();
                session()->flash('message', 'ℹ️ Tidak ada perubahan.');
            }
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            session()->flash('error', '❌ ' . $e->getMessage());
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            $this->logActivity('logged_out', "Pengguna keluar", null, ['logout_time' => now()]);
        }
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        session()->flash('message', '✅ Anda telah keluar.');
        return redirect('/');
    }

    public function getFilteredTodosProperty()
    {
        if (!$this->todos) return collect();

        $filtered = $this->currentView === 'history'
            ? $this->todos->where('completed', true)
            : $this->todos->where('completed', false);

        if ($this->currentCategoryFilter !== 'all') {
            $filtered = $filtered->filter(function($todo) {
                $cat = $todo->metadata['category'] ?? 'lainnya';
                return collect($this->categories)->contains('id', $cat);
            });
        } elseif ($this->currentView !== 'history' && $this->currentFilter !== 'all') {
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
        } else {
            $this->activityLogs = collect();
        }
    }

    public function openCreateModal()
    {
        $this->resetModal();
        $this->editingId = null;
        $this->showModal = true;
        $this->modalDeadline = now()->addDay()->format('Y-m-d\TH:i');
    }

    public function openEditModal($id)
    {
        $todo = Todo::where('id', $id)->where('user_id', auth()->id())->first();
        if ($todo) {
            $this->editingId = $id;
            $this->modalTitle = $todo->title;
            $this->modalDescription = $todo->description;
            $this->modalCategory = $this->getCategoryFromMetadata($todo);
            $this->modalPjId = $todo->metadata['pj_id'] ?? '';
            $this->modalDeadline = $todo->reminder_at ? $todo->reminder_at->format('Y-m-d\TH:i') : now()->addDay()->format('Y-m-d\TH:i');
            $this->modalMedia = null;
            $this->showModal = true;
        } else {
            session()->flash('error', '❌ Catatan tidak ditemukan.');
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
            $availableCategories = collect($this->categories)->pluck('id')->toArray();
            $rules = [
                'modalTitle' => 'required|string|max:50',
                'modalDescription' => 'nullable|string|max:1000',
                'modalDeadline' => 'required|date',
                'modalCategory' => ['required', Rule::in($availableCategories)],
                'modalMedia' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
            $messages = ['modalCategory.in' => 'Kategori tidak valid.'];
            $this->validate($rules, $messages);

            if ($this->editingId) {
                $todo = Todo::where('id', $this->editingId)->where('user_id', auth()->id())->first();
                if (!$todo) {
                    session()->flash('error', '❌ Catatan tidak ditemukan.');
                    return;
                }

                $oldTitle = $todo->title;
                $metadata = $todo->metadata ?? [];
                $metadata['category'] = $this->modalCategory;
                
                if ($this->modalPjId) {
                    $metadata['pj_id'] = $this->modalPjId;
                } else {
                    unset($metadata['pj_id']);
                }

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
                    'reminder_at' => Carbon::parse($this->modalDeadline),
                    'metadata' => $metadata,
                    'media_path' => $mediaPath
                ]);

                $this->logActivity('updated', "Mengedit catatan '{$oldTitle}'", $todo->id, [
                    'new_category' => $this->modalCategory,
                    'pj_id' => $this->modalPjId
                ]);
                session()->flash('message', '✅ Catatan berhasil diperbarui!');
            } else {
                $metadata = ['category' => $this->modalCategory];
                if ($this->modalPjId) {
                    $metadata['pj_id'] = $this->modalPjId;
                }

                $mediaPath = $this->modalMedia ? $this->modalMedia->store('todos', 'public') : null;

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
                    'pj_id' => $this->modalPjId
                ]);
                
                auth()->user()->notify(new TodoCreatedNotification($todo));
                session()->flash('message', '✅ Catatan baru berhasil ditambahkan!');
            }
            $this->closeModal();
            $this->loadData();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving todo: ' . $e->getMessage());
            session()->flash('error', '❌ Gagal menyimpan catatan.');
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
        $this->modalPjId = '';
        $this->modalDeadline = '';
        $this->modalMedia = null;
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function toggleCompleted($id)
    {
        $todo = Todo::where('id', $id)->where('user_id', auth()->id())->first();
        if ($todo) {
            $todo->completed = !$todo->completed;
            $todo->save();
            if ($todo->completed) {
                $this->logActivity('completed', "Menandai selesai: '{$todo->title}'", $todo->id);
                session()->flash('message', '✅ Selesai!');
            } else {
                $this->logActivity('archived', "Aktifkan kembali: '{$todo->title}'", $todo->id);
                session()->flash('message', '✅ Diaktifkan kembali!');
            }
            $this->loadData();
        }
    }

    public function deleteTodo($id)
    {
        try {
            $todo = Todo::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
            $todoTitle = $todo->title;
            
            $deletedInfo = [
                'id' => $todo->id,
                'title' => $todo->title,
                'category' => $this->getCategoryFromMetadata($todo),
                'pj_id' => $todo->metadata['pj_id'] ?? null,
                'deleted_at' => now()
            ];

            if ($todo->media_path && Storage::disk('public')->exists($todo->media_path)) {
                Storage::disk('public')->delete($todo->media_path);
            }

            $this->logActivity('deleted', "Menghapus catatan '{$todoTitle}'", $id, $deletedInfo);
            $todo->delete();
            $this->loadData();
            session()->flash('message', '🗑️ Catatan dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting todo: ' . $e->getMessage());
            session()->flash('error', '❌ Gagal menghapus.');
        }
    }

    private function logActivity(string $action, string $description, ?int $todoId = null, array $metadata = [])
    {
        if (!auth()->check()) return null;
        try {
            $log = ActivityLog::create([
                'user_id' => auth()->id(),
                'todo_id' => $todoId,
                'action' => $action,
                'description' => $description,
                'metadata' => $metadata
            ]);
            if ($log) {
                $this->dispatch('activityLogCreated', ['log_id' => $log->id, 'action' => $action]);
            }
            return $log;
        } catch (\Exception $e) {
            Log::error('Activity log failed: ' . $e->getMessage());
            return null;
        }
    }

    public function changeView($view)
    {
        $this->currentView = $view;
        $this->currentFilter = 'all';
        $this->currentCategoryFilter = 'all';
        if ($view === 'activity-log') $this->loadActivityLogs();
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

    public function getCategoryHexColor($categoryId)
    {
        foreach ($this->categories as $category) {
            if ($category['id'] === $categoryId) return $category['color'];
        }
        $fallbacks = ['proker' => '#667eea', 'event' => '#764ba2', 'rapat' => '#f093fb', 'dana' => '#fa709a', 'lainnya' => '#4facfe'];
        return $fallbacks[$categoryId] ?? '#95a5a6';
    }

    public function getLogBorderColor($action) { 
        $colors = ['created' => 'border-green-500', 'updated' => 'border-blue-500', 'completed' => 'border-purple-500', 'deleted' => 'border-red-500', 'archived' => 'border-yellow-500', 'profile_updated' => 'border-indigo-500', 'logged_out' => 'border-gray-500', 'log_deleted' => 'border-pink-500', 'logs_batch_deleted' => 'border-pink-500', 'all_logs_deleted' => 'border-pink-500', 'category_created' => 'border-blue-400', 'category_updated' => 'border-blue-400', 'category_deleted' => 'border-red-400', 'pj_created' => 'border-green-400', 'pj_updated' => 'border-green-400', 'pj_deleted' => 'border-red-400'];
        return $colors[$action] ?? 'border-gray-300';
    }
    public function getLogBgColor($action) { 
        $colors = ['created' => 'bg-green-500', 'updated' => 'bg-blue-500', 'completed' => 'bg-purple-500', 'deleted' => 'bg-red-500', 'archived' => 'bg-yellow-500', 'profile_updated' => 'bg-indigo-500', 'logged_out' => 'bg-gray-500', 'log_deleted' => 'bg-pink-500', 'logs_batch_deleted' => 'bg-pink-500', 'all_logs_deleted' => 'bg-pink-500', 'category_created' => 'bg-blue-400', 'category_updated' => 'bg-blue-400', 'category_deleted' => 'bg-red-400', 'pj_created' => 'bg-green-400', 'pj_updated' => 'bg-green-400', 'pj_deleted' => 'bg-red-400'];
        return $colors[$action] ?? 'bg-gray-400';
    }
    public function getLogIcon($action) { 
        $icons = ['created' => 'ph-plus-circle', 'updated' => 'ph-pencil-circle', 'completed' => 'ph-check-circle', 'deleted' => 'ph-trash', 'archived' => 'ph-archive', 'profile_updated' => 'ph-user-circle', 'logged_out' => 'ph-sign-out', 'log_deleted' => 'ph-trash-simple', 'logs_batch_deleted' => 'ph-trash', 'all_logs_deleted' => 'ph-trash', 'category_created' => 'ph-tag', 'category_updated' => 'ph-tag', 'category_deleted' => 'ph-tag', 'pj_created' => 'ph-user', 'pj_updated' => 'ph-user', 'pj_deleted' => 'ph-user'];
        return $icons[$action] ?? 'ph-clock-counter-clockwise';
    }
    public function getLogTitle($action) { 
        $titles = ['created' => 'Catatan Dibuat', 'updated' => 'Catatan Diedit', 'completed' => 'Catatan Diselesaikan', 'deleted' => 'Catatan Dihapus', 'archived' => 'Catatan Diaktifkan', 'profile_updated' => 'Profil Diperbarui', 'logged_out' => 'Logout', 'log_deleted' => 'Riwayat Dihapus', 'logs_batch_deleted' => 'Riwayat Dihapus', 'all_logs_deleted' => 'Semua Riwayat Dihapus', 'category_created' => 'Kategori Dibuat', 'category_updated' => 'Kategori Diubah', 'category_deleted' => 'Kategori Dihapus', 'pj_created' => 'PJ Ditambahkan', 'pj_updated' => 'PJ Diubah', 'pj_deleted' => 'PJ Dihapus'];
        return $titles[$action] ?? 'Aktivitas';
    }

    // Log Deletion Methods
    public function confirmDeleteLog($logId) { 
        $log = ActivityLog::where('id', $logId)->where('user_id', auth()->id())->first();
        if ($log) {
            $this->logToDeleteId = $logId;
            $this->logToDeleteTitle = $log->description;
            $this->confirmingLogDeletion = true;
        }
    }
    public function deleteLog() { 
        try {
            $log = ActivityLog::where('id', $this->logToDeleteId)->where('user_id', auth()->id())->firstOrFail();
            $log->delete();
            $this->logActivity('log_deleted', "Menghapus riwayat: {$log->description}", null, ['deleted_log_id' => $this->logToDeleteId]);
            $this->confirmingLogDeletion = false;
            $this->logToDeleteId = null;
            $this->loadActivityLogs();
            session()->flash('message', '🗑️ Riwayat dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', '❌ Gagal menghapus riwayat.');
        }
    }
    public function cancelDeleteLog() { $this->confirmingLogDeletion = false; $this->logToDeleteId = null; }
    
    public function toggleSelectAllLogs() {
        $this->selectedLogs = $this->selectAllLogs ? $this->activityLogs->pluck('id')->toArray() : [];
    }
    public function deleteSelectedLogs() {
        if (empty($this->selectedLogs)) return;
        $count = count($this->selectedLogs);
        ActivityLog::whereIn('id', $this->selectedLogs)->where('user_id', auth()->id())->delete();
        $this->logActivity('logs_batch_deleted', "Menghapus {$count} riwayat", null, ['deleted_count' => $count]);
        $this->selectedLogs = [];
        $this->selectAllLogs = false;
        $this->loadActivityLogs();
        session()->flash('message', "🗑️ {$count} riwayat dihapus!");
    }
    public function confirmDeleteAllLogs() { $this->dispatch('confirmDeleteAllLogs'); }
    public function deleteAllLogs() {
        $totalCount = ActivityLog::where('user_id', auth()->id())->count();
        if ($totalCount === 0) return;
        ActivityLog::where('user_id', auth()->id())->delete();
        $this->logActivity('all_logs_deleted', "Menghapus semua {$totalCount} riwayat", null, ['deleted_count' => $totalCount]);
        $this->loadActivityLogs();
        session()->flash('message', "🗑️ Semua riwayat dihapus!");
    }
    public function isLogSelected($logId) { return in_array($logId, $this->selectedLogs); }

    public function getUpcomingTodos()
    {
        return Todo::whereNotNull('reminder_at')
            ->where('user_id', auth()->id())
            ->select('id', 'title', 'reminder_at')
            ->get()
            ->toArray();
    }
}