<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Carbon\Carbon;

class TodoList extends Component
{
    public $title = '';
    public $todos;
    public $reminder_at = '';

    public $showAddForm = false;

    public $editingTodoId = null;
    public $editTitle = '';
    public $editReminderAt = '';
    public $description = '';
    public $editDescription = '';

    protected $rules = [
        'title' => 'required|string|max:50',
        'description' => 'nullable|string|max:1000',
        'reminder_at' => 'nullable|date',
    ];

    protected $editRules = [
        'editTitle' => 'required|string|max:50',
        'editDescription' => 'nullable|string|max:1000',
        'editReminderAt' => 'nullable|date',
    ];

    public function mount()
    {
        $this->loadTodos();
    }

public function addTodo()
{
    $this->validate();

    auth()->user()->todos()->create([
        'title' => $this->title,
        'description' => $this->description,
        'completed' => false,
        'reminder_at' => $this->reminder_at ? Carbon::parse($this->reminder_at) : null,
    ]);

    $this->reset(['title', 'description', 'reminder_at']);
    $this->showAddForm = false;
    $this->editingTodoId = null;

    $this->loadTodos();
}

    public function updatedShowAddForm()
{
    if ($this->showAddForm) {
        $this->editingTodoId = null;
    }
}

    public function toggleCompleted($id)
{
    $todo = Todo::find($id);
    if ($todo) {
        $todo->completed = !$todo->completed;
        $todo->save();
        $this->loadTodos();
    }
}

    public function deleteTodo($id)
    {
        Todo::destroy($id);
        $this->loadTodos();
    }

public function startEdit($id)
{
    if ($this->editingTodoId === $id) {
        $this->cancelEdit();
    } else {
        $todo = Todo::find($id);
        if ($todo) {
            $this->editingTodoId = $id;
            $this->editTitle = $todo->title;
            $this->editDescription = $todo->description;
            $this->editReminderAt = $todo->reminder_at ? $todo->reminder_at->format('Y-m-d\TH:i') : '';
            $this->showAddForm = false;
        }
    }
}
    public function updateTodo()
    {
        $this->validate($this->editRules);

        $todo = Todo::find($this->editingTodoId);
        if ($todo) {
            $todo->update([
                'title' => $this->editTitle,
                'description' => $this->editDescription,
                'reminder_at' => $this->editReminderAt ? Carbon::parse($this->editReminderAt) : null,
            ]);
            $this->cancelEdit();
            $this->loadTodos();
            $this->showAddForm = false;
        }
    }
public function refreshTodos()
{
    $this->todos = \App\Models\Todo::orderBy('created_at', 'desc')->get();
}
    public function cancelEdit()
    {
        $this->editingTodoId = null;
        $this->editTitle = '';
        $this->editDescription = '';
        $this->editReminderAt = '';
    }

    public function cancelAddForm()
    {
        $this->showAddForm = false;
        $this->reset(['title', 'description', 'reminder_at']);
    }

public function markAsCompletedFromNotification($id)
{
    $todo = Todo::find($id);
    if ($todo) {
        $todo->completed = true;
        $todo->save();
        $this->refreshTodos();
    }
}

protected function loadTodos()
{
    if (auth()->check()) {
        $this->todos = auth()->user()->todos()->orderBy('created_at', 'desc')->get();
    } else {
        $this->todos = collect();
    }
}

protected $listeners = [
    'refresh-todos' => 'refreshTodos'
];
    public function render()
    {
        return view('livewire.todo-list');
    }
}