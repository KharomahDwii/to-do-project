<div class="container mx-auto p-6 bg-beige min-h-screen">
    <div class="container mx-auto p-6 min-h-screen" style="background-color: #f8f4f0;">
    <h1 class="text-4xl font-bold text-brown mb-8">TO DO LIST</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Kolom Kiri: Daftar To-Do -->
        <div class="bg-brown rounded-3xl {
                            border-radius: 10px
                            } border-none focus:outline-none focus:ring-2 focus:ring-beige"
                            style="background-color: #eecfb9; color: #48281c; font-weight: bold; border-radius: 20px; padding: 12px 0; p-6 text-beige shadow-lg; ">
            
            <ul class="space-y-4">
                @forelse($todos as $todo)
                    <li class="flex items-center justify-between p-4 bg-beige/20 rounded-xl">
                        
                        <div class="flex items-center space-x-3 flex-grow">
                           <input
    type="checkbox"
    class="me-2 form-check-input"
    wire:change="toggleCompleted({{ $todo->id }})"
    {{ $todo->completed ? 'checked' : '' }}
>
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
                📅 {{ $todo->created_at->format('d/m/Y H:i') }}
                    @if($todo->reminder_at)
                        | ⏰ {{ $todo->reminder_at->format('d/m/Y H:i') }}
                                @if(now()->gt($todo->reminder_at) && !$todo->completed)
                                        <span class="badge bg-warning text-dark ms-1">Waktu lewat!</span>
                                                @endif
                                                    @endif
                                             </small>
                                        @if($todo->completed)
                                        <small class="d-block mt-0.09 opacity-75" style="font-size: 0.8rem; font-style: italic;">
                                        ✅ Telah Dikerjakan
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button
                                wire:click="startEdit({{ $todo->id }})">
                                ✏️
                            </button>
                            <button
                            wire:click="deleteTodo({{ $todo->id }})"
                            wire:confirm="Hapus tugas ini?"
                            class="btn btn-sm"
                            style="background-color: #dc3545; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0;"
                            >
                                🗑️
                            </button>
                        </div>
                    </li>
                @empty
                    <li class="text-center py-8 text-beige/70">Belum ada tugas.</li>
                @endforelse
            </ul>
        </div>
        <div class="space-y-6">
            <button
                wire:click="$toggle('showAddForm')"
                class="w-full flex items-center justify-center gap-2 bg-beige text-brown font-medium py-3 px-4 rounded-full hover:bg-beige/80 transition"
                style="background-color: #933920; color: #ffffff; font-weight: bold; border-radius: 50px; padding: 12px 0;"
                >
                Tambahkan List
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.404-1.404a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.596-8.596z" />
                </svg>
            </button>

            <!-- Form Tambah list -->
            @if($showAddForm)
                <div class="p-6 rounded-3xl shadow-lg" style="background-color: #933920; color: #ffffff;">
        <h3 class="text-xl font-semibold mb-4">Tambah list</h3>
                    <div class="mb-4">
                        <input
                            type="text"
                            wire:model="title"
                            placeholder="Masukan Text"
                            class="w-full p-3 bg-beige/30 text-beige rounded-full {
                            border-radius: 10px
                            } border-none focus:outline-none focus:ring-2 focus:ring-beige"
                            style="background-color: #ffdbdb; color: #933920; font-weight: bold; border-radius: 10px; padding: 12px 0;"
                        >
                        @error('title') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                    <textarea
                    wire:model="description"
                    placeholder="deskripsi (maks. 50 karakter)"
                    class="w-full p-3 bg-white/10 text-white rounded-xl border-none focus:outline-none focus:ring-2 focus:ring-white resize-none"
                    rows="3"
                    ></textarea>
                        @error('description') 
                        <small class="text-red-400 mt-1 d-block">{{ $message }}</small> 
                        @enderror
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-beige" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <input
                            type="datetime-local"
                            wire:model="reminder_at"
                            class="flex-grow p-3 bg-beige/30 text-beige rounded-full {
                            border-radius: 10px
                            } border-none focus:outline-none focus:ring-2 focus:ring-beige"
                            style="background-color: #ffdbdb; color: #933920; font-weight: bold; border-radius: 10px; padding: 12px 0;" 
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

            <!-- Form Editnya -->
            @if($editingTodoId)
            <div class="p-6 rounded-3xl shadow-lg" style="background-color: #933920; color: #ffffff;">
                <div class="bg-brown rounded-3xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold mb-4 text-beige">Edit List</h3>

                    <div class="mb-4">
                        <input
                            type="text"
                            class="form-control"
                            wire:model="editTitle"
                            placeholder="Masukan text"
                            class="w-full p-3 bg-beige/30 text-beige rounded-full border-none focus:outline-none focus:ring-2 focus:ring-beige"
                        >
                        @error('editTitle') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <textarea
                            wire:model="editDescription"
                            placeholder="Deskripsi (maks. 200 karakter)"
                            class="w-full p-3 bg-white/10 text-white rounded-xl border-none focus:outline-none focus:ring-2 focus:ring-white resize-none"
                            rows="3"
                            style="backdrop-filter: blur(10px);"
                            ></textarea>
                                @error('editDescription') 
                            <small class="text-red-400 mt-1 d-block">{{ $message }}</small> 
                        @enderror
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-beige" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <input
                            type="datetime-local"
                            wire:model="editReminderAt"
                            class="flex-grow p-3 bg-beige/30 text-beige rounded-full border-none focus:outline-none focus:ring-2 focus:ring-beige"
                        >
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

{{-- untuk reminder --}}
@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    const checkReminders = () => {
        document.querySelectorAll('[data-reminder]').forEach(el => {
            const reminderTime = el.getAttribute('data-reminder');
            const isCompleted = el.getAttribute('data-completed') === '1';
            const title = el.getAttribute('data-title');
            const todoId = el.getAttribute('data-todo-id');

            if (!reminderTime || isCompleted || !todoId) return;

            const now = new Date();
            const remindAt = new Date(reminderTime);

            if (remindAt <= now) {
                const notifiedKey = 'notified_' + todoId;
                if (localStorage.getItem(notifiedKey)) return;

                localStorage.setItem(notifiedKey, '1');
                showCustomNotification(todoId, title);
            }
        });

        setTimeout(checkReminders, 30000);
    };

    function showCustomNotification(todoId, title) {
        if (document.getElementById('custom-notif-' + todoId)) return;

        const notif = document.createElement('div');
        notif.id = 'custom-notif-' + todoId;
        notif.className = 'alert alert-info position-fixed bottom-0 end-0 m-3 shadow-lg';
        notif.style.cssText = `
            background: white;
            border-left: 4px solid #198754;
            border-radius: 8px;
            padding: 1rem;
            max-width: 320px;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
        `;
        notif.innerHTML = `
            <strong>⏰ Pengingat!</strong><br>
            <em>${title}</em>
            <div class="mt-2 d-flex gap-2">
                <button class="btn btn-sm btn-success" onclick="markDone(${todoId})">
                    Sudah Dikerjakan
                </button>
                <button class="btn btn-sm btn-warning" onclick="snooze(${todoId})">
                    Snooze (5 menit)
                </button>
            </div>
        `;
        document.body.appendChild(notif);

        setTimeout(() => {
            if (notif.parentNode) notif.remove();
        }, 15000);
    }

    // === PANGGIL LIVWIRE SECARA LANGSUNG ===
window.markDone = function(todoId) {
    Livewire.dispatch('markAsCompletedFromNotification', [todoId]);

    const li = document.querySelector(`[data-todo-id="${todoId}"]`);
    if (!li) return;

    // 1. Centang checkbox
    const checkbox = li.querySelector('input[type="checkbox"]');
    if (checkbox) {
        checkbox.checked = true;
    }

    // 2. Coret teks
    const textSpan = li.querySelector('span');
    if (textSpan) {
        textSpan.style.textDecoration = 'line-through';
        textSpan.style.color = '#999';
    }

    // 3. Ganti "Waktu lewat!" → "Telah Dikerjakan", atau tambahkan jika perlu
    const warningBadge = li.querySelector('span.badge.bg-warning');
    if (warningBadge) {
        warningBadge.textContent = 'Telah Dikerjakan';
        warningBadge.className = 'badge bg-success ms-1';
    } else {
        // Cari elemen pengingat (yang ada ⏰)
        const reminderEl = Array.from(li.querySelectorAll('small')).find(el =>
            el.textContent.trim().startsWith('⏰')
        );

        if (reminderEl) {
            const doneLabel = document.createElement('span');
            doneLabel.className = 'badge bg-success ms-1';
            doneLabel.textContent = 'Telah Dikerjakan';
            reminderEl.appendChild(doneLabel);
        } else {
            // Jika tidak ada pengingat, tampilkan di bawah tanggal
            const dateEl = li.querySelector('small.d-block.text-muted');
            if (dateEl) {
                const doneDiv = document.createElement('small');
                doneDiv.className = 'd-block mt-1 text-success';
                doneDiv.innerHTML = '<strong>Telah Dikerjakan</strong>';
                dateEl.parentNode.insertBefore(doneDiv, dateEl.nextSibling);
            }
        }
    }

    // Simpan status ke DOM
    li.setAttribute('data-completed', '1');

    // Hapus notifikasi
    const notif = document.getElementById('custom-notif-' + todoId);
    if (notif) notif.remove();
};

    window.snooze = function(todoId) {
        const newTime = new Date(Date.now() + 5 * 60 * 1000);
        localStorage.setItem('snoozed_until_' + todoId, newTime.getTime());

        const li = document.querySelector(`[data-todo-id="${todoId}"]`);
        if (li) {
            li.setAttribute('data-reminder', newTime.toISOString());
        }

    const notif = document.getElementById('custom-notif-' + todoId);
    if (notif) notif.remove();
    };

    // Jalankan pengecekan
    checkReminders();
});
</script>
@endpush