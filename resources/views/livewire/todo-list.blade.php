<div class="min-h-screen bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 flex transition-colors duration-300"
     x-data="{ sidebarOpen: false }"
     x-on:resize.window="sidebarOpen = window.innerWidth >= 768"
     x-init="sidebarOpen = window.innerWidth >= 768">

    <aside 
        class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-gray-700 z-50 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-xl md:shadow-none"
        :class="{ 'translate-x-0': sidebarOpen }"
    >
        <div class="p-6 flex items-center gap-1">
            <h2 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">Mading OSIS</h2>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1">
            <a 
                href="#" 
                wire:click.prevent="changeView('notes')"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'notes' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}"
            >
                📝 Papan Catatan
            </a>
            <a 
                href="#" 
                wire:click.prevent="changeView('history')"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'history' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}"
            >
                🕒 Riwayat Aktivitas
            </a>
        </nav>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
            <button 
                @click="$store.darkMode.toggle()"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors"
            >
                <div class="flex items-center gap-3">
                    <span x-text="$store.darkMode.isDark ? '☀️' : '🌙'"></span>
                    <span x-text="$store.darkMode.isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
                </div>
                <div class="w-10 h-5 bg-gray-300 dark:bg-indigo-600 rounded-full relative">
                    <div 
                        class="absolute top-1 w-3 h-3 bg-white rounded-full transition-transform duration-300"
                        :class="{ 'left-1': !$store.darkMode.isDark, 'left-6': $store.darkMode.isDark }"
                    ></div>
                </div>
            </button>
            <div 
    wire:click="openProfileModal"
    class="flex items-center gap-3 px-4 py-3 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors group"
>
    <img 
        src="{{ auth()->user()?->profile_photo_url ?? 'https://placehold.co/100?text=' . urlencode(substr(auth()->user()?->name ?? 'OSIS', 0, 1)) }}" 
        alt="User" 
        class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-600 shadow-sm"
    >
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
            {{ auth()->user()?->name ?? 'Ketua OSIS' }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            Edit Profil
        </p>
    </div>
    <span class="text-gray-400 group-hover:rotate-90 transition-transform duration-500">⚙️</span>
</div>
</aside>
    <div 
        class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm" 
        x-show="sidebarOpen" 
        x-on:click="sidebarOpen = false"
        x-transition
    ></div>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button 
                    @click="sidebarOpen = true" 
                    class="md:hidden p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg"
                >
                    ☰
                </button>
                <h1 class="text-2xl font-bold">
                    @if($currentView === 'history') Riwayat Aktivitas @else Papan Catatan @endif
                </h1>
            </div>

            @if($currentView === 'notes')
                <button 
                    wire:click="openCreateModal"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md shadow-indigo-200 dark:shadow-none transition flex items-center gap-2 active:scale-95"
                >
                    ➕ Tempel Catatan
                </button>
            @endif
        </header>

        @if($currentView === 'notes')
        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-900/50 border-b border-gray-200 dark:border-gray-700 overflow-x-auto whitespace-nowrap no-scrollbar">
            <div class="flex gap-2">
                @php
                    $filters = [
                        'all' => 'Semua',
                        'proker' => 'Program Kerja',
                        'event' => 'Event Sekolah',
                        'rapat' => 'Rapat',
                        'dana' => 'Dana & Sponsor',
                        'completed' => 'Selesai (Arsip)',
                    ];
                @endphp
                @foreach($filters as $key => $label)
                    <button 
                        wire:click="setFilter('{{ $key }}')"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all shadow-sm
                        @if($currentFilter === $key)
                            @if($key === 'event')
                                bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-900/50 dark:text-purple-300 dark:border-purple-900
                            @else
                                bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-600 dark:border-indigo-600
                            @endif
                        @else
                            bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-indigo-300 hover:text-indigo-600 dark:hover:text-indigo-400
                        @endif"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex-1 overflow-y-auto p-6">
            @if($currentView === 'notes')
                @if($this->filteredTodos->isEmpty())
                    <div class="text-center py-20">
                        <div class="text-5xl mb-4">📝</div>
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Belum ada catatan</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Mulai dengan menempelkan catatan baru.</p>
                        <button 
                            wire:click="openCreateModal"
                            class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition"
                        >
                            ➕ Tempel Catatan
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($this->filteredTodos as $todo)
                            @php
                                $isOverdue = $todo->reminder_at && $todo->reminder_at->isPast();
                                $dateStr = $todo->reminder_at ? $todo->reminder_at->format('d M H:i') : '—';
                                $category = $todo->metadata['category'] ?? 'lainnya';
                                $tagClass = 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300';
                                if ($category === 'proker') { $tagClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'; }
                                if ($category === 'event') { $tagClass = 'bg-purple-100 text-purple-800 border border-purple-200 dark:bg-purple-900/40 dark:border-purple-700 dark:text-purple-300'; }
                                if ($category === 'rapat') { $tagClass = 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300'; }
                                if ($category === 'dana') { $tagClass = 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300'; }
                                $titleClass = $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-slate-800 dark:text-slate-100';
                                $dateClass = $isOverdue ? 'text-red-500 dark:text-red-400 font-bold' : 'text-gray-500 dark:text-gray-400';
                            @endphp
                            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                                @if($todo->media_path)
                                    <img 
                                        src="{{ asset('storage/' . $todo->media_path) }}" 
                                        class="w-full h-48 object-cover border-b border-gray-100 dark:border-gray-700"
                                        onerror="this.src='https://placehold.co/400x200/e2e8f0/64748b?text=Foto+Catatan'"
                                    >
                                @endif

                                <div class="p-5 flex-1 flex flex-col">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-md inline-flex items-center gap-1 {{ $tagClass }}">
                                            {{ ucfirst($category) }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-bold mb-2 leading-tight {{ $titleClass }}">{{ $todo->title }}</h3>
                                    @if($todo->description)
                                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-3 flex-1">{{ $todo->description }}</p>
                                    @endif
                                    <div class="pt-4 mt-auto border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 {{ $dateClass }} text-xs">
                                            ⏰ {{ $dateStr }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button 
                                                wire:click="toggleCompleted({{ $todo->id }})"
                                                class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition"
                                                title="Selesai"
                                            >
                                                ✅
                                            </button>
                                            <button 
                                                wire:click="openEditModal({{ $todo->id }})"
                                                class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition"
                                                title="Edit"
                                            >
                                                ✏️
                                            </button>
                                            <button 
                                                wire:click="deleteTodo({{ $todo->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
                                                title="Hapus"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                @if($activityLogs->count() > 0)
                    <div class="max-w-4xl mx-auto space-y-4">
                        @foreach($activityLogs as $log)
                            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 flex items-start gap-4 hover:shadow-sm transition">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center flex-shrink-0">
                                    @switch($log->action)
                                        @case('created')
                                            <i class="ph ph-plus-circle text-green-600 dark:text-green-400 text-xl"></i>
                                            @break
                                        @case('updated')
                                            <i class="ph ph-pencil-simple text-blue-600 dark:text-blue-400 text-xl"></i>
                                            @break
                                        @case('deleted')
                                            <i class="ph ph-trash text-red-600 dark:text-red-400 text-xl"></i>
                                            @break
                                        @case('completed')
                                            <i class="ph ph-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                                            @break
                                        @case('archived')
                                            <i class="ph ph-arrow-counter-clockwise text-yellow-600 dark:text-yellow-400 text-xl"></i>
                                            @break
                                        @default
                                            <i class="ph ph-clock-counter-clockwise text-gray-600 dark:text-gray-400 text-xl"></i>
                                    @endswitch
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                                    
                                    @if($log->metadata)
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @if(isset($log->metadata['category']))
                                                <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded">Kategori: {{ ucfirst($log->metadata['category']) }}</span>
                                            @endif
                                            @if(isset($log->metadata['has_media']) && $log->metadata['has_media'])
                                                <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">Foto: Ya</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center max-w-2xl mx-auto py-12">
                        <div class="bg-gray-100 dark:bg-slate-700 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ph ph-clock-counter-clockwise text-4xl text-gray-400 dark:text-gray-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">Belum ada riwayat aktivitas</h3>
                        <p class="text-gray-500 dark:text-gray-400">Aktivitas seperti menambah, mengedit, atau menghapus catatan akan muncul di sini.</p>
                    </div>
                @endif
            @endif
        </div>
    </main>
    
    @if($showModal)
        <div class="fixed inset-0 z-50" style="display: block;">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-2xl transform transition-all scale-95 opacity-0 animate-fade-in">
                    <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                            {{ $editingId ? 'Edit Catatan' : 'Tempel Catatan Baru' }}
                        </h3>
                        <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-full transition">✕</button>
                    </div>
                    <form wire:submit.prevent="saveModal" class="p-6 space-y-4">
                        <input type="hidden" wire:model="editingId">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Catatan</label>
                            <input type="text" wire:model="modalTitle" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Contoh: Persiapan Pensi" required />
                            @error('modalTitle') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Isi Catatan</label>
                            <textarea wire:model="modalDescription" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Detail kegiatan..." required></textarea>
                            @error('modalDescription') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                                <select wire:model="modalCategory" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                    <option value="proker">Program Kerja</option>
                                    <option value="event">Event Sekolah</option>
                                    <option value="rapat">Rapat</option>
                                    <option value="dana">Dana & Sponsor</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deadline</label>
                                <input type="datetime-local" wire:model="modalDeadline" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" required />
                                @error('modalDeadline') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Lampiran Gambar (Opsional)</label>
                            <div class="mt-1">
                                <label for="modalMedia" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition cursor-pointer">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="ph ph-image text-3xl text-gray-400"></i>
                                        <p class="text-sm text-gray-600 mt-2">Upload foto atau drag & drop</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                    </div>
                                    <input id="modalMedia" type="file" accept="image/*" 
                                           class="hidden"
                                           wire:model="modalMedia">
                                </label>
                                
                                @if($modalMedia)
                                    <div class="mt-3">
                                        <img src="{{ $modalMedia->temporaryUrl() }}" 
                                             class="h-32 w-full object-cover rounded-lg border border-gray-200">
                                    </div>
                                @endif
                                
                                @error('modalMedia') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                            <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-md shadow-indigo-200 dark:shadow-none transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@if($showProfileModal)
    <div class="fixed inset-0 z-50" style="display: block;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeProfileModal"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl transform transition-all scale-95 opacity-0 animate-fade-in">
                <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Edit Profil</h3>
                    <button wire:click="closeProfileModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-full transition">✕</button>
                </div>
                <form wire:submit.prevent="saveProfile" class="p-6 space-y-4">
                    <div class="flex flex-col items-center">
                        <div class="relative w-24 h-24 mb-4 group cursor-pointer" 
                             x-on:click="$refs.avatarInput.click">
                            <img src="{{ $profileAvatar }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">📷</div>
                        </div>
                        <input 
                            type="file" 
                            accept="image/*" 
                            class="hidden" 
                            x-ref="avatarInput"
                            wire:model="profileAvatarFile" 
                        />
                        <p class="text-xs text-gray-500 dark:text-gray-400">Klik gambar untuk ganti</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Pengguna</label>
                        <input type="text" wire:model="profileName" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        @error('profileName') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit" class="w-full bg-indigo-600 text-white font-medium py-2.5 rounded-xl hover:bg-indigo-700 transition">Simpan Perubahan</button>
                        <button type="button" wire:click="logout" class="w-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-medium py-2.5 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition flex items-center justify-center gap-2">🔚 Keluar Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

    @if(session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 z-50 flex items-center gap-3 p-4 rounded-xl shadow-lg border-l-4 border-green-500 bg-white dark:bg-slate-800 min-w-[300px] transform translate-x-full transition-all duration-300" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
            ✅ <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ session('message') }}</span>
        </div>
    @endif

    @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 z-50 flex items-center gap-3 p-4 rounded-xl shadow-lg border-l-4 border-red-500 bg-white dark:bg-slate-800 min-w-[300px] transform translate-x-full transition-all duration-300" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
            ❌ <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ session('error') }}</span>
        </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('darkMode', {
                isDark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                init() { document.documentElement.classList.toggle('dark', this.isDark); },
                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    document.documentElement.classList.toggle('dark', this.isDark);
                }
            });
            Alpine.store('darkMode').init();
        });
    </script>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s forwards; }
        @keyframes fadeIn { to { opacity: 1; transform: scale(1); } }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>