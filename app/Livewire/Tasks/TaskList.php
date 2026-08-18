<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Livewire\Attributes\Layout;
use Livewire\Component;

class TaskList extends Component
{
    public $filterStatus = 'all';

    public $newTaskTitle = '';

    public $newTaskDescription = '';

    public $newTaskPriority = 'normal';

    public $newTaskDueDate = '';

    public function createTask()
    {
        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
            'newTaskDescription' => 'nullable|string',
            'newTaskPriority' => 'required|in:normal,high,urgent',
            'newTaskDueDate' => 'nullable|date',
        ]);

        Task::create([
            'title' => $this->newTaskTitle,
            'description' => $this->newTaskDescription,
            'priority' => $this->newTaskPriority,
            'due_date' => $this->newTaskDueDate ?: null,
            'status' => 'pending',
            'assigned_to' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        $this->reset(['newTaskTitle', 'newTaskDescription', 'newTaskPriority', 'newTaskDueDate']);

        $this->js("\$flux.modal('new-task').close()");
    }

    public function markAsComplete(Task $task)
    {
        $task->update(['status' => 'completed']);
    }

    #[Layout('components.layouts.app.workspace')]
    public function render()
    {
        $query = Task::query();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        } else {
            $query->whereIn('status', ['pending', 'in_progress']); // Hide completed by default
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        return view('livewire.tasks.task-list', [
            'tasks' => $tasks,
        ]);
    }
}
