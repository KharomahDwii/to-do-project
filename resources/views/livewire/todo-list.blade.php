<div class="min-h-screen bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 flex transition-colors duration-300" 
     x-data="{ sidebarOpen: false }" 
     x-on:resize.window="sidebarOpen = window.innerWidth >= 768" 
     x-init="sidebarOpen = window.innerWidth >= 768">

    <!-- SIDEBAR -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-gray-700 z-50 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-xl md:shadow-none"
           :class="{ 'translate-x-0': sidebarOpen }">
        
        <div class="p-6 flex items-center gap-1">
            <h2 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">To Do List</h2>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <a href="#" wire:click.prevent="changeView('notes')"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'notes' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}">
                📝 Catatan
            </a>
            <a href="#" wire:click.prevent="changeView('history')"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'history' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}">
                ✅ Catatan Selesai
            </a>
            <a href="#" wire:click.prevent="changeView('activity-log')"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $currentView === 'activity-log' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white' }}">
                📋 Riwayat Aktivitas
            </a>

            <!-- Menu Manajemen Baru -->
            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Manajemen</p>
                <button wire:click="openCategoryModal()" 
                    class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors text-left">
                    <span>🏷️</span> Kelola Kategori
                </button>
                <button wire:click="openPjModal()" 
                    class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors text-left">
                    <span>👤</span> Kelola PJ
                </button>
            </div>
        </nav>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
            <button @click="$store.darkMode.toggle()"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <span x-text="$store.darkMode.isDark ? '☀️' : '🌙'"></span>
                    <span x-text="$store.darkMode.isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
                </div>
                <div class="w-10 h-5 bg-gray-300 dark:bg-indigo-600 rounded-full relative">
                    <div class="absolute top-1 w-3 h-3 bg-white rounded-full transition-transform duration-300"
                        :class="{ 'left-1': !$store.darkMode.isDark, 'left-6': $store.darkMode.isDark }"></div>
                </div>
            </button>

            <div wire:click="openProfileModal"
                class="flex items-center gap-3 px-4 py-3 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors group">
                <img src="{{ $profileAvatar }}" alt="User"
                    class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-600 shadow-sm"
                    onerror="this.src='https://placehold.co/100?text={{ strtoupper(substr($profileName ?? 'U', 0, 1)) }}'">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $profileName }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Edit Profil</p>
                </div>
                <span class="text-gray-400 group-hover:rotate-90 transition-transform duration-500">⚙️</span>
            </div>
        </div>
    </aside>

    <!-- Overlay Mobile -->
    <div class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm" x-show="sidebarOpen" x-on:click="sidebarOpen = false" x-transition></div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true"
                    class="md:hidden p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg">
                    ☰
                </button>
                <h1 class="text-2xl font-bold">
                    @if($currentView === 'notes') List Catatan
                    @elseif($currentView === 'history') Catatan Selesai
                    @elseif($currentView === 'activity-log') Riwayat Aktivitas
                    @endif
                </h1>
            </div>
            @if($currentView === 'notes')
            <button wire:click="openCreateModal"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md shadow-indigo-200 dark:shadow-none transition flex items-center gap-2 active:scale-95">
                ➕ Tambahkan Catatan
            </button>
            @endif
        </header>

        <!-- Filter Bar -->
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
                <button wire:click="setFilter('{{ $key }}')"
                    class="px-4 py-2 rounded-full text-sm font-medium border transition-all shadow-sm
                    @if($currentFilter === $key)
                        @if($key === 'event') bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-900/50 dark:text-purple-300 dark:border-purple-900
                        @else bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-600 dark:border-indigo-600
                        @endif
                    @else
                        bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-indigo-300 hover:text-indigo-600 dark:hover:text-indigo-400
                    @endif">
                    {{ $label }}
                </button>
                @endforeach
                
                <!-- Filter Kategori Custom -->
                @foreach($categories as $cat)
                    @if(!isset($cat['is_default']) || !$cat['is_default'])
                    <button wire:click="setCategoryFilter('{{ $cat['id'] }}')"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all shadow-sm
                        @if($currentCategoryFilter === $cat['id'])
                            text-white border-transparent
                        @else
                            bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-indigo-300
                        @endif"
                        style="@if($currentCategoryFilter === $cat['id']) background-color: {{ $cat['color'] }}; @endif">
                        {{ $cat['name'] }}
                    </button>
                    @endif
                @endforeach
                @if($currentCategoryFilter !== 'all')
                <button wire:click="setCategoryFilter('all')" class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600 border border-red-200 hover:bg-red-200">✕ Reset</button>
                @endif
            </div>
        </div>
        @endif

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6">
            @if($currentView === 'notes')
                @if($this->filteredTodos->isEmpty())
                <div class="text-center py-20">
                    <div class="text-5xl mb-4">📝</div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Belum ada catatan</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Mulai dengan menambahkan catatan baru.</p>
                    <button wire:click="openCreateModal" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition">➕ Tambahkan Catatan</button>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($this->filteredTodos as $todo)
                    @php
                        $isOverdue = $todo->reminder_at && $todo->reminder_at->isPast();
                        $dateStr = $todo->reminder_at ? $todo->reminder_at->format('d M H:i') : '—';
                        $category = $todo->metadata['category'] ?? 'lainnya';
                        $pjId = $todo->metadata['pj_id'] ?? null;
                        $pjName = $this->getPjName($pjId);
                        
                        $tagClass = 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300';
                        if ($category === 'proker') { $tagClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'; }
                        elseif ($category === 'event') { $tagClass = 'bg-purple-100 text-purple-800 border border-purple-200 dark:bg-purple-900/40 dark:border-purple-700 dark:text-purple-300'; }
                        elseif ($category === 'rapat') { $tagClass = 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300'; }
                        elseif ($category === 'dana') { $tagClass = 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300'; }
                        
                        $customCat = collect($categories)->firstWhere('id', $category);
                        if($customCat && !isset($customCat['is_default'])) {
                             $tagClass = 'text-white'; 
                        }

                        $titleClass = $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-slate-800 dark:text-slate-100';
                        $dateClass = $isOverdue ? 'text-red-500 dark:text-red-400 font-bold' : 'text-gray-500 dark:text-gray-400';
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                        @if($todo->media_path)
                        <img src="{{ asset('storage/' . $todo->media_path) }}"
                            class="w-full h-48 object-cover border-b border-gray-100 dark:border-gray-700"
                            onerror="this.src='https://placehold.co/400x200/e2e8f0/64748b?text=Foto+Catatan'">
                        @endif
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-md inline-flex items-center gap-1 {{ $tagClass }}"
                                      @if($customCat && !isset($customCat['is_default'])) style="background-color: {{ $customCat['color'] }};" @endif>
                                    {{ $customCat ? $customCat['name'] : ucfirst($category) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold mb-2 leading-tight {{ $titleClass }}">{{ $todo->title }}</h3>
                            @if($todo->description)
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-3 flex-1">{{ $todo->description }}</p>
                            @endif
                            
                            @if($pjName)
                            <div class="mb-3 flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 px-2 py-1 rounded w-fit">
                                <span>👤</span> <span class="font-medium">{{ $pjName }}</span>
                            </div>
                            @endif

                            <div class="pt-4 mt-auto border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div class="flex items-center gap-1.5 {{ $dateClass }} text-xs">
                                    ⏰ {{ $dateStr }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="toggleCompleted({{ $todo->id }})"
                                        class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition" title="Selesai">✅</button>
                                    <button wire:click="openEditModal({{ $todo->id }})"
                                        class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition" title="Edit">✏️</button>
                                    <button wire:click="deleteTodo({{ $todo->id }})"
                                        class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">🗑️</button>
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
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col opacity-75 hover:opacity-100 transition-opacity">
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-md {{ $tagClass }}">{{ ucfirst($category) }}</span>
                                <span class="text-xs bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 px-2 py-1 rounded">Selesai</span>
                            </div>
                            <h3 class="text-lg font-bold mb-2 leading-tight text-gray-500 dark:text-gray-400 line-through">{{ $todo->title }}</h3>
                            <div class="pt-4 mt-auto border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <button wire:click="toggleCompleted({{ $todo->id }})"
                                    class="text-xs text-yellow-600 hover:text-yellow-700 font-medium">↩️ Aktifkan Kembali</button>
                                <button wire:click="deleteTodo({{ $todo->id }})" class="text-red-500 hover:text-red-700">🗑️</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

            @elseif($currentView === 'activity-log')
            <div class="space-y-4">
                <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="ph-clock-counter-clockwise text-blue-500 text-xl"></i> Riwayat Aktivitas
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Melacak semua aktivitas Anda</p>
                    </div>
                    @if($activityLogs->count() > 0)
                    <button wire:click="confirmDeleteAllLogs" class="flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors shadow-sm">
                        <i class="ph-trash text-lg"></i> <span class="hidden sm:inline">Hapus Semua</span>
                    </button>
                    @endif
                </div>

                @if($activityLogs->count() === 0)
                <div class="bg-white dark:bg-slate-800 rounded-lg p-8 text-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <div class="text-4xl mb-2">🕒</div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Belum Ada Riwayat</h3>
                </div>
                @else
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($activityLogs as $log)
                    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border-l-4 {{ $this->getLogBorderColor($log->action) }} hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full flex-shrink-0 {{ $this->getLogBgColor($log->action) }}">
                                <i class="{{ $this->getLogIcon($log->action) }} text-white text-lg"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-gray-800 dark:text-white">{{ $this->getLogTitle($log->action) }}</h4>
                                    @if($log->todo)
                                        <span class="text-sm text-gray-500 dark:text-gray-400">→ {{ $log->todo->title }}</span>
                                    @elseif(isset($log->metadata['title']))
                                        <span class="text-sm text-gray-500 dark:text-gray-400">→ {{ $log->metadata['title'] }} <span class="text-xs italic">(telah dihapus)</span></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $log->description }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <span><i class="ph-clock mr-1"></i> {{ $log->created_at->diffForHumans() }}</span>
                                    @if(isset($log->metadata['category']))
                                    <span class="bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded text-xs">{{ ucfirst($log->metadata['category']) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>
    </main>

    <!-- MODAL CREATE/EDIT TODO -->
    @if($showModal)
    <div class="fixed inset-0 z-50" style="display: block;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-2xl transform transition-all scale-95 opacity-0 animate-fade-in">
                <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $editingId ? 'Edit Catatan' : 'Tambah Catatan Baru' }}</h3>
                    <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-full transition">✕</button>
                </div>
                <form wire:submit.prevent="saveModal" class="p-6 space-y-4">
                    <input type="hidden" wire:model="editingId">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Catatan</label>
                        <input type="text" wire:model="modalTitle" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Judul kegiatan..." required />
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
                            <div class="flex gap-2">
                                <select wire:model="modalCategory" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="openCategoryModal()" class="px-3 bg-gray-100 dark:bg-slate-700 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 transition" title="Kelola Kategori">➕</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Penanggung Jawab (PJ)</label>
                            <div class="flex gap-2">
                                <select wire:model="modalPjId" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                                    <option value="">-- Pilih PJ --</option>
                                    @foreach($pjs as $pj)
                                        <option value="{{ $pj['id'] }}">{{ $pj['name'] }} {{ $pj['role'] ? '('.$pj['role'].')' : '' }}</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="openPjModal()" class="px-3 bg-gray-100 dark:bg-slate-700 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 transition" title="Kelola PJ">➕</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deadline</label>
                        <input type="datetime-local" wire:model="modalDeadline" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" required />
                        @error('modalDeadline') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Lampiran Gambar (Opsional)</label>
                        <label for="modalMedia" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition cursor-pointer">
                            <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                <i class="ph ph-image text-2xl text-gray-400"></i>
                                <p class="text-xs text-gray-500 mt-1">Upload foto</p>
                            </div>
                            <input id="modalMedia" type="file" accept="image/*" class="hidden" wire:model="modalMedia">
                        </label>
                        @if($modalMedia)
                        <div class="mt-2 relative">
                            <img src="{{ $modalMedia->temporaryUrl() }}" class="h-24 w-full object-cover rounded-lg border border-gray-200">
                            <button type="button" wire:click="clearMedia" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">✕</button>
                        </div>
                        @endif
                        @error('modalMedia') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
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

    <!-- MODAL PROFILE -->
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
                        <div class="relative w-24 h-24 mb-4 group cursor-pointer" x-on:click="$refs.avatarInput.click">
                            <img src="{{ $profileAvatar }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">📷</div>
                        </div>
                        <input type="file" accept="image/*" class="hidden" x-ref="avatarInput" wire:model="profileAvatarFile" />
                        @error('profileAvatarFile') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Pengguna</label>
                        <input type="text" wire:model="profileName" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        @error('profileName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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

    <!-- MODAL MANAJEMEN KATEGORI (CENTERED FIX) -->
    @if($showCategoryModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" 
         style="display: flex !important; position: fixed !important; top: 0; left: 0; right: 0; bottom: 0;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeCategoryModal"></div>
        <div class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl transform transition-all scale-100 opacity-100 animate-fade-in max-h-[90vh] overflow-y-auto custom-scrollbar z-10" style="position: relative; margin: auto;">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
                    <button type="button" wire:click="closeCategoryModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form wire:submit.prevent="saveCategory" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kategori</label>
                        <input type="text" wire:model="categoryName" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Contoh: Ujian, Proyek" required>
                        @error('categoryName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Warna Label</label>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <input type="color" wire:model="categoryColor" class="h-10 w-14 rounded cursor-pointer border-0 bg-transparent">
                            <span class="text-sm text-gray-600 dark:text-gray-300 font-mono">{{ $categoryColor }}</span>
                        </div>
                        @error('categoryColor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if(!$editingCategoryId && count($categories) > 0)
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">Daftar Kategori:</p>
                        <div class="max-h-48 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                            @foreach($categories as $cat)
                            <div class="flex items-center justify-between p-2.5 bg-gray-50 dark:bg-slate-700/50 rounded-lg group hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-4 h-4 rounded-full shadow-sm border border-gray-200 dark:border-gray-600" style="background-color: {{ $cat['color'] }}"></span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $cat['name'] }}</span>
                                    @if(isset($cat['is_default']) && $cat['is_default'])
                                        <span class="text-[10px] bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded font-medium">Default</span>
                                    @endif
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" wire:click="openCategoryModal('{{ $cat['id'] }}')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-semibold px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/30">Edit</button>
                                    @if(!isset($cat['is_default']) || !$cat['is_default'])
                                    <button type="button" wire:click="confirmDeleteCategory('{{ $cat['id'] }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-semibold px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30">Hapus</button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeCategoryModal" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition shadow-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-md shadow-indigo-200 dark:shadow-none transition transform active:scale-95">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL MANAJEMEN PJ (CENTERED FIX) -->
    @if($showPjModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" 
         style="display: flex !important; position: fixed !important; top: 0; left: 0; right: 0; bottom: 0;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closePjModal"></div>
        <div class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl transform transition-all scale-100 opacity-100 animate-fade-in max-h-[90vh] overflow-y-auto custom-scrollbar z-10" style="position: relative; margin: auto;">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $editingPjId ? 'Edit Penanggung Jawab' : 'Tambah PJ Baru' }}</h3>
                    <button type="button" wire:click="closePjModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form wire:submit.prevent="savePj" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="pjName" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Contoh: Budi Santoso" required>
                        @error('pjName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan / Peran (Opsional)</label>
                        <input type="text" wire:model="pjRole" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Contoh: Ketua Seksi">
                        @error('pjRole') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if(!$editingPjId && count($pjs) > 0)
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">Daftar PJ:</p>
                        <div class="max-h-48 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                            @foreach($pjs as $pj)
                            <div class="flex items-center justify-between p-2.5 bg-gray-50 dark:bg-slate-700/50 rounded-lg group hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $pj['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $pj['role'] ?? '-' }}</p>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" wire:click="openPjModal('{{ $pj['id'] }}')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-semibold px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/30">Edit</button>
                                    <button type="button" wire:click="confirmDeletePj('{{ $pj['id'] }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-semibold px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30">Hapus</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closePjModal" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition shadow-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-md shadow-indigo-200 dark:shadow-none transition transform active:scale-95">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS (CUSTOM POPUP) -->
    <div x-data="{ 
            isOpen: false, 
            type: '', 
            itemId: null, 
            itemName: '' 
        }"
        x-init="
            $wire.on('openDeleteConfirmation', (event) => {
                type = event.type;
                itemId = event.id;
                itemName = event.name;
                isOpen = true;
            });
            $wire.on('closeDeleteConfirmation', () => {
                isOpen = false;
            });
        "
        x-show="isOpen"
        x-cloak
        class="fixed inset-0 z-[10000] flex items-center justify-center p-4"
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="isOpen = false"></div>

        <!-- Modal Content -->
        <div class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl p-6 transform transition-all scale-100"
             @click.away="isOpen = false">
            
            <div class="flex flex-col items-center text-center">
                <!-- Icon Warning -->
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
                    Hapus <span x-text="type === 'category' ? 'Kategori' : 'PJ'"></span>?
                </h3>
                
                <p class="text-gray-600 dark:text-gray-300 mb-6 text-sm leading-relaxed">
                    Apakah Anda yakin ingin menghapus 
                    <strong class="text-indigo-600 dark:text-indigo-400" x-text="itemName"></strong>?
                    <br>
                    <span x-show="type === 'category'" class="text-xs text-orange-500 mt-1 block">
                        Catatan yang menggunakan kategori ini akan dipindahkan ke 'Lainnya'.
                    </span>
                    <span x-show="type === 'pj'" class="text-xs text-orange-500 mt-1 block">
                        Data PJ akan dihapus dari catatan yang terkait.
                    </span>
                </p>

                <div class="flex gap-3 w-full">
                    <button @click="isOpen = false" 
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </button>
                    <button @click="
                        $wire.performDelete(type, itemId);
                        isOpen = false;
                    " 
                        class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-medium shadow-lg shadow-red-200 dark:shadow-none transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ showDeleteAllModal: false }"
         x-init="$wire.on('confirmDeleteAllLogs', () => { showDeleteAllModal = true })"
         x-show="showDeleteAllModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         style="display: none;" x-style="display: block;">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-md w-full p-6 animate-fade-in-up">
            <h3 class="text-xl font-bold text-center text-gray-800 dark:text-white mb-2">Hapus Semua Riwayat?</h3>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-6 text-sm">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="showDeleteAllModal = false" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-800 dark:text-white rounded-lg font-medium">Batal</button>
                <button @click="$wire.deleteAllLogs(); showDeleteAllModal = false" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">Hapus Semua</button>
            </div>
        </div>
    </div>

    <!-- NOTIFICATIONS -->
    @if(session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
         class="fixed bottom-5 right-5 z-50 flex items-center gap-3 p-4 rounded-xl shadow-lg border-l-4 border-green-500 bg-white dark:bg-slate-800 min-w-[300px] transform translate-x-full transition-all duration-300" 
         x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
        ✅ <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ session('message') }}</span>
    </div>
    @endif
    
    @if(session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         class="fixed bottom-5 right-5 z-50 flex items-center gap-3 p-4 rounded-xl shadow-lg border-l-4 border-red-500 bg-white dark:bg-slate-800 min-w-[300px] transform translate-x-full transition-all duration-300" 
         x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
        ❌ <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ session('error') }}</span>
    </div>
    @endif

    <!-- SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if ('Notification' in window && Notification.permission === 'default') {
                setTimeout(() => Notification.requestPermission(), 2000);
            }
        });

        function sendNotification(title, body) {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(title, { body: body, badge: 'https://placehold.co/32?text=📌' });
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
                try { return JSON.parse(localStorage.getItem(this.key)) || {}; } catch { return {}; }
            }
        };

        function checkDeadlines() {
            @this.call('getUpcomingTodos').then(todos => {
                if (!todos?.length) return;
                const now = new Date();
                todos.forEach(todo => {
                    if (!todo?.reminder_at) return;
                    const deadline = new Date(todo.reminder_at);
                    if (isNaN(deadline.getTime())) return;
                    const timeDiff = deadline.getTime() - now.getTime();
                    const todoId = String(todo.id);
                    if (timeDiff <= 0 && timeDiff >= -300000 && !NotificationTracker.isSent(todoId, 'deadline')) {
                        sendNotification('🚨 Deadline Sekarang!', `Catatan: ${todo.title}`);
                        NotificationTracker.markSent(todoId, 'deadline');
                    }
                });
            }).catch(error => console.error('Error fetching todos:', error));
        }
        setInterval(checkDeadlines, 15000);
        document.addEventListener('livewire:init', () => { checkDeadlines(); });

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
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #475569; }
        [x-cloak] { display: none !important; }
    </style>
</div>