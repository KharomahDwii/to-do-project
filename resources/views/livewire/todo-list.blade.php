<div class="min-h-screen bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 flex transition-colors duration-300" x-data="{ sidebarOpen: false }" x-on:resize.window="sidebarOpen = window.innerWidth >= 768" x-init="sidebarOpen = window.innerWidth >= 768">
<aside class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-gray-700 z-50 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-xl md:shadow-none"
:class="{ 'translate-x-0': sidebarOpen }">
    <div class="p-6 flex items-center gap-1">
        <h2 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">To Do List</h2>
    </div>
    <nav class="flex-1 px-4 py-4 space-y-1">
        <a href="#" 
        wire:click.prevent="changeView('notes')"
        class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'notes' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}"
        >📝 Papan Catatan
    </a>
    <a
    href="#"
    wire:click.prevent="changeView('history')"
    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'history' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}"
    >
    ✅ Catatan Selesai
        </a>
        <a
        href="#"
        wire:click.prevent="changeView('activity-log')"
        class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'activity-log' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}"
        >📋 Activity Log</a>
    </nav>
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
        <button
        @click="$store.darkMode.toggle()"
        class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
        <div class="flex items-center gap-3">
            <span x-text="$store.darkMode.isDark ? '☀️' : '🌙'"></span>
            <span x-text="$store.darkMode.isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
        </div>
        <div class="w-10 h-5 bg-gray-300 dark:bg-indigo-600 rounded-full relative">
            <div
            class="absolute top-1 w-3 h-3 bg-white rounded-full transition-transform duration-300"
            :class="{ 'left-1': !$store.darkMode.isDark, 'left-6': $store.darkMode.isDark }">
        </div>
    </div>
</button>
    <div
    wire:click="openProfileModal"
    class="flex items-center gap-3 px-4 py-3 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors group">
    <img
    src="{{ $profileAvatar }}"
    alt="User"
    class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-600 shadow-sm"
    onerror="this.src='https://placehold.co/100?text=      {{ strtoupper(substr($profileName ?? 'U', 0, 1)) }}'">
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
            {{ $profileName }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            Edit Profil
        </p>
    </div>
    <span class="text-gray-400 group-hover:rotate-90 transition-transform duration-500">⚙️</span>
</div>
    </div>
    </aside>
    <div class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm" x-show="sidebarOpen" x-on:click="sidebarOpen = false" x-transition>
    </div>
    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button
                @click="sidebarOpen = true"
                class="md:hidden p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg">
                ☰
            </button>
            <h1 class="text-2xl font-bold">
                @if($currentView === 'notes') List Catatan
                @elseif($currentView === 'history') Catatan Selesai
                @elseif($currentView === 'activity-log') Activity Log
                @endif
            </h1>
        </div>
        @if($currentView === 'notes')
        <button
        wire:click="openCreateModal"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md shadow-indigo-200 dark:shadow-none transition flex items-center gap-2 active:scale-95">
        ➕ Tambahkan Catatan
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
                @endif">
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
                <button wire:click="openCreateModal" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition">
                    ➕ Tambahkan Catatan
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
                $titleClass = $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-slate-800 dark:text-slate-100';
                $dateClass = $isOverdue ? 'text-red-500 dark:text-red-400 font-bold' : 'text-gray-500 dark:text-gray-400';
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    @if($todo->media_path)
                    <img
                    src="{{ asset('storage/' . $todo->media_path) }}"
                    class="w-full h-48 object-cover border-b border-gray-100 dark:border-gray-700"
                    onerror="this.src='https://placehold.co/400x200/e2e8f0/64748b?text=Foto+Catatan      '"
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
                                <button wire:click="toggleCompleted({{ $todo->id }})"
                                    class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition" title="Selesai">
                                    ✅
                                </button>
                                <button
                                wire:click="openEditModal({{ $todo->id }})"
                                class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition"
                                title="Edit">
                                ✏️
                            </button>
                            <button
                            wire:click="deleteTodo({{ $todo->id }})"
                            class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus"
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

    @elseif($currentView === 'history')
    @if($this->filteredTodos->isEmpty())
    <div class="text-center py-20">
        <div class="text-5xl mb-4">✅</div>
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Belum ada catatan selesai</h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Catatan yang sudah ditandai selesai akan muncul di sini.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($this->filteredTodos as $todo)
        @php
        $category = $todo->metadata['category'] ?? 'lainnya';
        $tagClass = 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300';
        if ($category === 'proker') { $tagClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'; }
        if ($category === 'event') { $tagClass = 'bg-purple-100 text-purple-800 border border-purple-200 dark:bg-purple-900/40 dark:border-purple-700 dark:text-purple-300'; }
        if ($category === 'rapat') { $tagClass = 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300'; }
        @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            @if($todo->media_path)
            <img
            src="{{ asset('storage/' . $todo->media_path) }}"
            class="w-full h-48 object-cover border-b border-gray-100 dark:border-gray-700"
            onerror="this.src='https://placehold.co/400x200/e2e8f0/64748b?text=Foto+Catatan      '"
            >
            @endif
            <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-md inline-flex items-center gap-1 {{ $tagClass }}">
                        {{ ucfirst($category) }}
                    </span>
                    <span class="text-xs bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 px-2 py-1 rounded">
                        Selesai
                    </span>
                </div>
                <h3 class="text-lg font-bold mb-2 leading-tight text-gray-500 dark:text-gray-400 line-through">{{ $todo->title }}</h3>
                @if($todo->description)
                <p class="text-gray-400 dark:text-gray-500 text-sm mb-4 line-clamp-3 flex-1">{{ $todo->description }}</p>
                @endif
                <div class="pt-4 mt-auto border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        ⏰ {{ $todo->reminder_at ? $todo->reminder_at->format('d M H:i') : '—' }}
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                        wire:click="toggleCompleted({{ $todo->id }})"
                        class="p-2 text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition" title="Aktifkan Kembali"
                        >
                        ↩️
                    </button>
                    <button
                    wire:click="deleteTodo({{ $todo->id }})"
                    class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus Permanen"
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

@elseif($currentView === 'activity-log')
<!-- Activity Log View Sederhana dengan Hanya Tombol Hapus Semua -->
<div class="space-y-4">
    
    <!-- Header dengan tombol hapus semua -->
    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="ph-clock-counter-clockwise text-blue-500 text-xl"></i>
                Riwayat Aktivitas
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Melacak semua aktivitas Anda di aplikasi
            </p>
        </div>
        
        @if($activityLogs->count() > 0)
        <div class="flex items-center gap-2">
            <!-- Tombol Hapus Semua (dengan konfirmasi) -->
            <button 
                wire:click="confirmDeleteAllLogs"
                class="flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors shadow-sm hover:shadow"
                title="Hapus semua riwayat">
                <i class="ph-trash text-lg"></i>
                <span class="hidden sm:inline">Hapus Semua</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Tidak ada riwayat -->
    @if($activityLogs->count() === 0)
        <div class="bg-white dark:bg-slate-800 rounded-lg p-8 text-center border-2 border-dashed border-gray-300 dark:border-gray-600">
            <div class="mx-auto w-20 h-20 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4">
                <i class="ph-clock-counter-clockwise text-4xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Belum Ada Riwayat</h3>
            <p class="text-gray-500 dark:text-gray-400">Aktivitas Anda akan muncul di sini</p>
        </div>
    @else
        <!-- Activity Logs List (tanpa checkbox/tombol hapus per item) -->
        <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
            @foreach($activityLogs as $log)
            <div 
                class="bg-white dark:bg-slate-800 rounded-lg p-4 border-l-4 {{ $this->getLogBorderColor($log->action) }} 
                       hover:shadow-md transition-shadow">
                
                <!-- Konten Utama -->
                <div class="flex items-start gap-3">
                    <!-- Icon -->
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full flex-shrink-0 {{ $this->getLogBgColor($log->action) }}">
                        <i class="{{ $this->getLogIcon($log->action) }} text-white text-lg"></i>
                    </span>
                    
                    <!-- Deskripsi -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-semibold text-gray-800 dark:text-white">{{ $this->getLogTitle($log->action) }}</h4>
                            @if($log->todo)
                                <span class="text-sm text-gray-500 dark:text-gray-400">→ {{ $log->todo->title }}</span>
                            @elseif(isset($log->metadata['title']))
                                <span class="text-sm text-gray-500 dark:text-gray-400">→ {{ $log->metadata['title'] }} <span class="text-xs italic">(telah dihapus)</span></span>
                            @endif
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $log->description }}
                        </p>
                        
                        <!-- Metadata tambahan untuk log deleted -->
                        @if($log->action === 'deleted' && isset($log->metadata['description_preview']))
                        <div class="mt-2">
                            <div class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-slate-700 rounded px-2 py-1 inline-block">
                                <i class="ph-text-bolder mr-1"></i>
                                {{ $log->metadata['description_preview'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Timestamp -->
                <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span>
                        <i class="ph-clock mr-1"></i>
                        {{ $log->created_at->diffForHumans() }}
                        <span class="mx-1">•</span>
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </span>
                    @if(isset($log->metadata['category']))
                    <span class="bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded text-xs">
                        {{ ucfirst($log->metadata['category']) }}
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

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
                                <input id="modalMedia" type="file" accept="image/*" class="hidden" wire:model="modalMedia">
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
                    @error('profileAvatarFile')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Pengguna</label>
                    <input type="text" wire:model="profileName" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    @error('profileName')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
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

<!-- Modal Konfirmasi Hapus Semua Riwayat (SINGLE FEATURE) -->
<div 
    x-data="{ showDeleteAllModal: false }"
    x-init="$wire.on('confirmDeleteAllLogs', () => { showDeleteAllModal = true })"
    x-show="showDeleteAllModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    style="display: none;"
    x-style="display: block;">
    
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-md w-full p-6 animate-fade-in-up">
        <div class="flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full mx-auto mb-4">
            <i class="ph-warning-circle text-red-600 dark:text-red-400 text-4xl"></i>
        </div>
        
        <h3 class="text-xl font-bold text-center text-gray-800 dark:text-white mb-2">
            Hapus Semua Riwayat?
        </h3>
        
        <p class="text-center text-gray-600 dark:text-gray-400 mb-6 text-sm">
            Apakah Anda yakin ingin menghapus <strong>semua</strong> riwayat aktivitas? Tindakan ini tidak dapat dibatalkan dan akan menghapus {{ $activityLogs->count() }} riwayat.
        </p>
        
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 mb-4">
            <p class="text-xs text-yellow-800 dark:text-yellow-300 flex items-start gap-2">
                <i class="ph-info text-lg mt-0.5 flex-shrink-0"></i>
                <span>
                    Riwayat aktivitas penting untuk audit trail. Pastikan Anda benar-benar ingin menghapus semua data ini.
                </span>
            </p>
        </div>
        
        <div class="flex gap-3">
            <button 
                @click="showDeleteAllModal = false"
                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-800 dark:text-white rounded-lg font-medium transition-colors">
                Batal
            </button>
            <button 
                @click="$wire.deleteAllLogs(); showDeleteAllModal = false"
                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                <i class="ph-trash text-lg mr-1"></i>
                Hapus Semua
            </button>
        </div>
    </div>
</div>

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
document.addEventListener('DOMContentLoaded', () => {
    if ('Notification' in window && Notification.permission === 'default') {
        setTimeout(() => {
            Notification.requestPermission().then(permission => {
                console.log('Izin notifikasi:', permission);
            });
        }, 2000);
    }
});
function sendNotification(title, body) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: body,
            badge: 'https://placehold.co/32?text=📌'
        });
        return true;
    }
    return false;
}
    const NotificationTracker = {
        key: 'sentDeadlineNotifications',
        markSent(todoId, type) {
            const id = String(todoId);
            let tracker = this.getAll();
            
            if (!tracker[id]) tracker[id] = {};
            tracker[id][type] = Date.now();
            localStorage.setItem(this.key, JSON.stringify(tracker));
        },
        isSent(todoId, type) {
            const id = String(todoId);
            const tracker = this.getAll();
            return !!tracker[id]?.[type];
        },
        getAll() {
            try {
                return JSON.parse(localStorage.getItem(this.key)) || {};
            } catch {
                return {};
            }
        }
    };
    function checkDeadlines() {
        @this.call('getUpcomingTodos').then(todos => {
            if (!todos?.length) return;
            const now = new Date();
            const fiveMinutesMs = 300000;
            const deadlineWindowMs = 1800000;
                            todos.forEach(todo => {
                    if (!todo?.reminder_at) return;
                    
                    const deadline = new Date(todo.reminder_at);
                    if (isNaN(deadline.getTime())) return;
                    
                    const timeDiff = deadline.getTime() - now.getTime();
                    const todoId = String(todo.id);

                    
                    if (timeDiff <= 0 && timeDiff >= -deadlineWindowMs && !NotificationTracker.isSent(todoId, 'deadline')) {
                        sendNotification('🚨 Deadline Sekarang!', `Catatan: ${todo.title}\nDeadline: ${deadline.toLocaleString('id-ID')}`);
                        NotificationTracker.markSent(todoId, 'deadline');
                    }
                });
            }).catch(error => {
                console.error('Error fetching todos:', error);
            });
        }

        setInterval(checkDeadlines, 15000);

        document.addEventListener('livewire:init', () => {
            checkDeadlines();
        });
    </script>

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