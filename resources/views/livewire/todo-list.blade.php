    <div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl md:text-5xl font-bold text-center mb-6" style="color: #ffffff;">
            TO DO LIST
        </h1>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="shadow-lg bg-brown rounded-3xl { border-radius: 10px } border-none focus:outline-none focus:ring-2 focus:ring-beige"
     style="background-color: #ffffff; color: #48281c; font-weight: bold; border-radius: 20px; padding: 12px 0; p-6 text-beige;">
            <ul class="space-y-4">
                @forelse($todos as $todo)
                    <li class="flex items-center justify-between p-4 bg-beige/20 rounded-xl"
                        data-todo-id="{{ $todo->id }}"
                        data-reminder="{{ $todo->reminder_at ? $todo->reminder_at->format('Y-m-d\TH:i:s') : '' }}"
                        data-title="{{ $todo->title }}">
                        <div class="flex items-center space-x-3 flex-grow">
                           <input
                            type="checkbox"
                            class="me-2 form-check-input"
                            wire:change="toggleCompleted({{ $todo->id }})"
                            {{ $todo->completed ? 'checked' : '' }}>
                            <div>
                                <div class="font-medium" style="{{ $todo->completed ? 'text-decoration: line-through; color: #000000;' : '' }}">
                        {{ $todo->title }}
                        </div>
                                <div class="text-xs text-beige/70">
                            @if($todo->description)
                    <small class="d-block mt-0.09 opacity-75" style="font-size: 0.85rem; color: #000000; word-wrap: break-word; font-style: italic; max-width: 100%; display: block;" >
            <div class="font-medium" style="{{ $todo->completed ? 'text-decoration: line-through; color: #000000;' : '' }}">
    {{ $todo->description }}
</div>
        </small>
    @endif
        <small class="d-block mt-0.09 opacity-75" style="font-size: 0.8rem; font-style: italic;">
                📅 {{ $todo->created_at->format('d/m/Y H:i | ') }}
                    @if($todo->reminder_at)
                        ⏰ {{ $todo->reminder_at->format('d/m/Y H:i | ') }}
                                @if(now()->gt($todo->reminder_at) && !$todo->completed)
                                        <span class="badge bg-warning text-dark ms-1"> Waktu terlewat!</span>
                                                @endif
                                                    @endif
                                             </small>
                                        @if($todo->completed)
                                        <small class="d-block mt-0.09 opacity-75" style="font-size: 0.8rem; font-style: italic;">
                                         Telah Dikerjakan
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button
                                wire:click="startEdit({{ $todo->id }})"
                                class="btn btn-sm" style="background-color: #4194d4; color: white; width: 28px; height: 28px; border-radius: 20%; display: flex; align-items: center; justify-content: center; padding: 0; box-shadow: 0 1px 6px rgb(0, 0, 0);"
                                >
                                ✏️
                            </button>
                            <button
                            wire:click="deleteTodo({{ $todo->id }})"
                            wire:confirm="Hapus tugas ini?"
                            class="btn btn-sm" style="background-color: #ff92b4; color: white; width: 28px; height: 28px; border-radius: 20%; display: flex; align-items: center; justify-content: center; padding: 0; box-shadow: 0 1px 6px rgb(0, 0, 0);"
                            >
                                🗑️
                            </button>
                        </div>
                    </li>
                @empty
                    <li class="text-center py-8 text-beige/70">Belum ada kegiatan.</li>
                @endforelse
            </ul>
        </div>
        <div class="space-y-6">
            <button
                wire:click="$toggle('showAddForm')"
                class="w-full flex items-center justify-center gap-2 bg-beige text-brown font-medium py-3 px-4 rounded-full hover:bg-beige/80 transition"
                style="background-color: #ffffff; color: #724e6c; font-weight: bold; border-radius: 50px; padding: 12px 0;"
                >
                Tambahkan kegiatan
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.404-1.404a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.596-8.596z" />
                </svg>
            </button>

            @if($showAddForm)
                <div class="p-6 rounded-3xl shadow-lg" style="background-color: #ffffff; color: #303756;">
                    <div class="bg-brown rounded-3xl p-6 shadow-lg">
                        <h3 class="text-xl font-semibold mb-4">Tambah kegiatan</h3>
                    <div class="mb-4">
                        <input
                            type="text"
                            wire:model="title"
                            placeholder="Masukan Text"
                            class="w-full p-3 bg-beige/30 text-beige rounded-full {
                            border-radius: 10px
                            } border-none focus:outline-none focus:ring-2 focus:ring-beige"
                            style="background-color: #b8c9ee; color: #3a425e; font-weight: bold; border-radius: 10px; padding: 12px 16px;">
                        @error('title') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                    <textarea
                    wire:model="description"
                    placeholder="Deskripsi (Opsional)"
                        class="w-full py-3 px-0 rounded-xl border-none focus:outline-none focus:ring-2 focus:ring-[#3a425e] resize-none font-bold"
                        style="background-color: #b8c9ee; color: #3a425e; padding: 12px 16px;"
                    rows="3">
                </textarea>
                        @error('description') 
                        <small class="text-red-400 mt-1 d-block" style="padding: 12px 16px;">{{ $message }}</small> 
                        @enderror
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <input
                            type="datetime-local"
                            wire:model="reminder_at"
                            class="flex-grow p-3 bg-beige/30 text-beige rounded-full {
                            border-radius: 10px
                            } border-none focus:outline-none focus:ring-2 focus:ring-beige"
                            style="background-color: #b8c9ee; color: #3a425e; font-weight: bold; border-radius: 10px; padding: 12px 16px;" 
                        >
                    </div>
                    <div class="flex justify-between">
                        <button
                            wire:click="cancelAddForm"
                            class="px-6 py-2 bg-beige/30 text-beige rounded-full hover:bg-beige/50 transition"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="addTodo"
                            class="px-6 py-2 bg-beige text-brown rounded-full hover:bg-beige/80 transition font-medium"
                        >
                            Create
                        </button>
                    </div>
                </div>
            @endif

            @if($editingTodoId)
            <div class="p-6 rounded-3xl shadow-lg" style="background-color: #ffffff; color: #cb829c;">
                <div class="bg-brown rounded-3xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold mb-4 text-beige">Edit List</h3>
                    <div class="mb-4">
                        <input
                            type="text"
                            wire:model="editTitle"
                            placeholder="  Masukan text"
                            class="w-full p-3 bg-beige/30 text-beige rounded-full {
                            border-radius: 10px
                            } border-none focus:outline-none focus:ring-2 focus:ring-beige"
                            style="background-color: #eeb8c9; color: #754e5a; font-weight: bold; border-radius: 10px; padding: 12px 16px;">
                        @error('editTitle') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <textarea
                            wire:model="editDescription"
                            placeholder="  Deskripsi"
                            class="w-full py-3 px-0 rounded-xl border-none focus:outline-none focus:ring-2 focus:ring-[#3a425e] resize-none font-bold"
                            style="background-color: #eeb8c9; color: #754e5a; padding: 12px 16px; backdrop-filter: blur(10px);"
                            rows="3"
                            ></textarea>
                                @error('editDescription') 
                            <small class="text-red-400 mt-1 d-block">{{ $message }}</small> 
                        @enderror
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <input
                            type="datetime-local"
                            wire:model="editReminderAt"
                            class="flex-grow p-3 bg-beige/30 text-beige rounded-full {
                            border-radius: 10px
                            } border-none focus:outline-none focus:ring-2 focus:ring-beige"
                            style="background-color: #eeb8c9; color: #754e5a; font-weight: bold; border-radius: 10px; padding: 12px 16px;">
                    </div>
                    <div class="flex justify-between">
                        <button
                            wire:click="cancelEdit"
                            class="px-6 py-2 bg-beige/30 text-beige rounded-full hover:bg-beige/50 transition"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="updateTodo"
                            class="px-6 py-2 bg-beige text-brown rounded-full hover:bg-beige/80 transition font-medium"
                        >
                            Update
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --color-brown: #6B4F4F;
        --color-beige: #D9C7B8;
    }
    .bg-brown { background-color: var(--color-brown); }
    .text-brown { color: var(--color-brown); }
    .bg-beige { background-color: var(--color-beige); }
    .text-beige { color: var(--color-beige); }
    
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    if (Notification.permission === 'default') {
        Notification.requestPermission();
    }

    const checkReminders = () => {
        document.querySelectorAll('li[data-todo-id]').forEach(el => {
            const reminderStr = el.getAttribute('data-reminder');
            const todoId = el.getAttribute('data-todo-id');
            const title = el.getAttribute('data-title');
            
            const checkbox = el.querySelector('input[type="checkbox"]');
            const isCompleted = checkbox ? checkbox.checked : false;

            if (!reminderStr || isCompleted) return;

            const now = new Date().getTime();
            const remindAt = new Date(reminderStr).getTime();
            const diff = remindAt - now;

            const fiveMinutesMs = 300 * 1000;
            const oneMinuteMs = 60 * 1000;

            if (diff > 0 && diff <= fiveMinutesMs) {
                const key = `notified_5min_${todoId}`;
                if (!localStorage.getItem(key)) {
                    sendBrowserNotification(title, "⏰ Sisa waktu 5 menit lagi!", todoId);
                    localStorage.setItem(key, 'true');
                }
            }

                if (diff > 0 && diff <= oneMinuteMs) {
            const key = `notified_1min_${todoId}`;
            if (!localStorage.getItem(key)) {
                sendBrowserNotification(title, "⏰ Sisa waktu 1 menit lagi!", todoId);
                localStorage.setItem(key, 'true');
            }
        }

            if (diff <= 0) {
                const key = `notified_exact_${todoId}`;
                if (!localStorage.getItem(key)) {
                    sendBrowserNotification(title, "Waktu tiba!",todoId);
                    localStorage.setItem(key, 'true');
                    
                    Livewire.dispatch('refresh-todos');
                }
            }
        });

        setTimeout(checkReminders, 5000); 
    };

    function sendBrowserNotification(title, body) {
        if (Notification.permission === "granted") {
            const notification = new Notification(title, {
                body: body,
                icon: '/favicon.ico' 
            });

            notification.onclick = function() {
            window.focus();
            notification.close();
        };
        }
    }
    checkReminders();

    Livewire.on('refresh-todos', () => {
    });
});
</script>
@endpush