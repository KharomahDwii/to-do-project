<div style="background: linear-gradient(to bottom, #adc7ff, #111f4d); color: #ffffff; border-radius: 20px; min-height: 100vh; font-family: Arial, sans-serif; padding: 0; margin: 0;">
    <style>
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.85) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes popOut {
            from { opacity: 1; transform: scale(1) translateY(0); }
            to { opacity: 0; transform: scale(0.85) translateY(10px); }
        }
        #logoutModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        #logoutModal.active {
            opacity: 1;
            pointer-events: all;
        }
        #logoutModal .modal-content {
            background-color: #3e4a6d;
            color: white;
            padding: 2rem;
            border-radius: 16px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            opacity: 0;
            transform: scale(0.85) translateY(10px);
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        #logoutModal.active .modal-content {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        .delete-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .delete-modal.active {
            opacity: 1;
            pointer-events: all;
        }
        .delete-modal .modal-content {
            background-color: #3e4a6d;
            color: white;
            padding: 2rem;
            border-radius: 16px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            opacity: 0;
            transform: scale(0.85) translateY(10px);
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .delete-modal.active .modal-content {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 3000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        @media (max-width: 768px) {
            div[style*="display: grid; grid-template-columns: 1fr 1fr;"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="max-width: 1280px; margin: 0 auto; padding: 2rem;"] {
                padding: 1rem !important;
            }
            h1[style*="font-size: 2.5rem"] {
                font-size: 1.8rem !important;
            }
            button[style*="width: 28px; height: 28px;"] {
                width: 24px !important;
                height: 24px !important;
            }
            .modal-content {
                padding: 1.5rem !important;
            }
        }
        
        @media (max-width: 480px) {
            div[style*="max-width: 1280px;"] {
                padding: 0.75rem !important;
            }
            h1[style*="font-size: 2.5rem"] {
                font-size: 1.5rem !important;
            }
            h2[style*="font-size: 1.25rem"] {
                font-size: 1.1rem !important;
            }
            li[style*="padding: 1rem;"] {
                padding: 0.75rem !important;
            }
            div[style*="font-size: 0.8rem;"] {
                font-size: 0.75rem !important;
            }
            div[style*="display: flex; gap: 0.5rem;"] {
                gap: 0.3rem !important;
            }
        }
    </style>
    <div style="position: absolute; top: 20px; left: 12px; z-index: 50;">
        <button onclick="document.getElementById('logoutModal').classList.add('active');"
                style="background: linear-gradient(to right, #363d71, #495092); color: #ffffff; font-weight: bold; border-radius: 50px; padding: 8px 18px; font-size: 0.75rem; border: none; cursor: pointer; box-shadow: 0 1px 4px rgba(0,0,0,0.2);">
            Logout
        </button>
    </div>

    <div style="max-width: 1280px; margin: 0 auto; padding: 2rem;">
        <h1 style="text-align: center; font-size: 2.5rem; font-weight: bold; margin-bottom: 1.5rem; color: #ffffff;">
            KEGIATAN SEHARI HARI
        </h1>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div style="background-color: #2d395e; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem; text-align: center;">Daftar List</h2>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">

                    @forelse($todos as $todo)
                        <li wire:key="todo-{{ $todo->id }}"
                            style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background-color: rgba(218, 223, 255, 0.1); border-radius: 12px; transition: all 0.2s;"
                            data-todo-id="{{ $todo->id }}"
                            data-reminder="{{ $todo->reminder_at ? $todo->reminder_at->format('Y-m-d\TH:i:s') : '' }}"
                            data-title="{{ $todo->title }}">

                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-grow: 1;">
                                <input type="checkbox"
                                       wire:change="toggleCompleted({{ $todo->id }})"
                                       {{ $todo->completed ? 'checked' : '' }}
                                       style="width: 18px; height: 18px; margin: 0; accent-color: #ffffff;">

                                <div>
                                    <div style="font-weight: 600; {{ $todo->completed ? 'text-decoration: line-through; color: #000000;' : '' }}">
                                        {{ $todo->title }}
                                    </div>
                                    @if($todo->description)
                                        <div style="font-size: 0.8rem; font-style: italic; opacity: 0.75; color: #dadfff; margin-top: 0.25rem; word-wrap: break-word; overflow-wrap: break-word; max-width: 50ch;">
                                            {{ $todo->description }}
                                        </div>
                                    @endif
                                    <div style="font-size: 0.8rem; font-style: italic; opacity: 0.75; color: #dadfff; margin-top: 0.25rem;">
                                        📅 {{ $todo->created_at->format('d/m/Y H:i') }}
                                        @if($todo->reminder_at)
                                            ⏰ {{ $todo->reminder_at->format('d/m/Y H:i') }}
                                            @if(now()->gt($todo->reminder_at) && !$todo->completed)
                                                <span style="background-color: #fbbf24; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 0.25rem;">
                                                    Waktu terlewat!
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    @if($todo->completed)
                                        <div style="font-size: 0.8rem; font-style: italic; opacity: 0.75; color: #dadfff; margin-top: 0.25rem;">
                                            Telah Dikerjakan
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div style="display: flex; gap: 0.5rem;">
                                <button wire:click="startEdit({{ $todo->id }})"
                                        style="background-color: #ffffff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0; box-shadow: 0 1px 6px rgb(0, 0, 0); border: none; cursor: pointer; font-size: 0.8rem;">
                                    ✏️
                                </button>
                                <button onclick="openDeleteModal({{ $todo->id }}, '{{ addslashes($todo->title) }}')"
                                        style="background-color: #ffffff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0; box-shadow: 0 1px 6px rgb(0, 0, 0); border: none; cursor: pointer; font-size: 0.8rem;">
                                    🗑️
                                </button>
                            </div>
                        </li>
                    @empty
                        <li style="text-align: center; padding: 2rem; color: rgba(218, 223, 255, 0.7); font-style: italic;">
                            Belum ada List.
                        </li>
                    @endforelse
                </ul>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                <button wire:click="$toggle('showAddForm')"
                        style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: #2d395e; color: #ffffff; font-weight: bold; border-radius: 50px; padding: 12px 0; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    Tambahkan List
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.404-1.404a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.596-8.596z" />
                    </svg>
                </button>

                @if($showAddForm)
                    <div style="background-color: #2d395e; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem; text-align: center;">Tambah List</h3>

                        <div style="margin-bottom: 1rem;">
                            <input type="text"
                                   wire:model="title"
                                   placeholder="Masukkan title"
                                   style="width: 100%; padding: 12px 16px; background-color: #ffffff; color: #171f3b; font-weight: bold; border-radius: 10px; border: none; box-sizing: border-box;">

                            @error('title')
                                <div style="color: #f87171; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <textarea wire:model="description"
                                      placeholder="Deskripsi (opsional)"
                                      rows="3"
                                      style="width: 100%; padding: 12px 16px; background-color: #ffffff; color: #171f3c; font-weight: bold; border-radius: 10px; border: none; box-sizing: border-box; resize: vertical; min-height: 80px;"></textarea>
                            @error('description')
                                <div style="color: #f87171; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <input type="datetime-local"
                                   wire:model="reminder_at"
                                   style="width: 100%; padding: 12px 16px; background-color: #ffffff; color: #17203f; font-weight: bold; border-radius: 10px; border: none; box-sizing: border-box;">
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                            <button wire:click="cancelAddForm"
                                    style="padding: 0.5rem 1.5rem; border-radius: 50px; border: none; cursor: pointer; font-weight: 600; background-color: rgba(218, 223, 255, 0.3); color: #ffffff;">
                                Cancel
                            </button>
                            <button wire:click="addTodo"
                                    style="padding: 0.5rem 1.5rem; border-radius: 50px; border: none; cursor: pointer; font-weight: 600; background-color: #566795; color: #ffffff;">
                                Create
                            </button>
                        </div>
                    </div>
                @endif

                @if($editingTodoId)
                    <div style="background-color: #2d395e; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem; text-align: center;">Edit List</h3>

                        <div style="margin-bottom: 1rem;">
                            <input type="text"
                                   wire:model="editTitle"
                                   placeholder="Title"
                                   style="width: 100%; padding: 12px 16px; background-color: #ffffff; color: #13163f; font-weight: bold; border-radius: 10px; border: none; box-sizing: border-box;">

                            @error('editTitle')
                                <div style="color: #f87171; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <textarea wire:model="editDescription"
                                      placeholder="Deskripsi"
                                      rows="3"
                                      style="width: 100%; padding: 12px 16px; background-color: #ffffff; color: #121438; font-weight: bold; border-radius: 10px; border: none; box-sizing: border-box; resize: vertical; min-height: 80px;"></textarea>
                            @error('editDescription')
                                <div style="color: #f87171; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <input type="datetime-local"
                                   wire:model="editReminderAt"
                                   style="width: 100%; padding: 12px 16px; background-color: #ffffff; color: #141b49; font-weight: bold; border-radius: 10px; border: none; box-sizing: border-box;">
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                            <button wire:click="cancelEdit"
                                    style="padding: 0.5rem 1.5rem; border-radius: 50px; border: none; cursor: pointer; font-weight: 600; background-color: rgba(218, 223, 255, 0.3); color: #ffffff;">
                                Cancel
                            </button>
                            <button wire:click="updateTodo"
                                    style="padding: 0.5rem 1.5rem; border-radius: 50px; border: none; cursor: pointer; font-weight: 600; background-color: #566795; color: #ffffff;">
                                Update
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="logoutModal">
        <div class="modal-content">
            <h3 style="margin-top: 0; font-size: 1.25rem;">Konfirmasi Logout</h3>
            <p>Yakin mau logout?</p>
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                <button onclick="document.getElementById('logoutModal').classList.remove('active');"
                        style="padding: 0.5rem 1.5rem; border-radius: 50px; background-color: #4b5563; color: white; border: none; cursor: pointer;">
                    Cancel
                </button>
                <!-- ✅ Form POST @csrf -->
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit"
                            style="padding: 0.5rem 1.5rem; border-radius: 50px; background-color: #ef4444; color: white; border: none; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="delete-modal">
        <div class="modal-content">
            <h3 style="margin-top: 0; font-size: 1.25rem;">Konfirmasi Hapus</h3>
            <p>Yakin mau hapus list ini?</p>
            <p style="font-weight: bold; margin: 0.5rem 0; color: #ff6b6b;" id="deleteModalTitle"></p>
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                <button onclick="closeDeleteModal()"
                        style="padding: 0.5rem 1.5rem; border-radius: 50px; background-color: #4b5563; color: white; border: none; cursor: pointer;">
                    Cancel
                </button>
                <button onclick="confirmDeleteTodo()"
                        style="padding: 0.5rem 1.5rem; border-radius: 50px; background-color: #b93232; color: white; border: none; cursor: pointer;">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast">Berhasil dihapus!</div>

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

                const oneMinuteMs = 60 * 1000;


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
                        sendBrowserNotification(title, "Waktu tiba!", todoId);
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

        Livewire.on('refresh-todos', () => {});

        let currentDeleteId = null;

        window.openDeleteModal = function(todoId, title) {
            currentDeleteId = todoId;
            document.getElementById('deleteModalTitle').textContent = title;
            document.getElementById('deleteModal').classList.add('active');
        };

        window.closeDeleteModal = function() {
            document.getElementById('deleteModal').classList.remove('active');
            currentDeleteId = null;
        };

        window.confirmDeleteTodo = function() {
            if (currentDeleteId !== null) {
                const component = Livewire.find('{{ $this->getId() }}');
                if (component) {
                    component.call('deleteTodo', currentDeleteId).then(() => {
                        const toast = document.getElementById('toast');
                        toast.textContent = 'Well well well!';
                        toast.style.backgroundColor = '#10b981';
                        toast.classList.add('show');
                        setTimeout(() => toast.classList.remove('show'), 3000);
                        closeDeleteModal();
                    }).catch(err => {
                        const toast = document.getElementById('toast');
                        toast.textContent = 'Gagal menghapus kegiatan.';
                        toast.style.backgroundColor = '#ef4444';
                        toast.classList.add('show');
                        setTimeout(() => {
                            toast.classList.remove('show');
                            toast.textContent = 'Kegiatan berhasil dihapus!';
                            toast.style.backgroundColor = '#10b981';
                        }, 3000);
                    });
                }
            }
        };
    });
    </script>
    @endpush
</div>