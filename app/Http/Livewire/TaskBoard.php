<?php

namespace App\Http\Livewire;

use App\Models\Board;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Cliente;
use Livewire\Component;

class TaskBoard extends Component
{
    public Board $board;
    public $lists;
    public $editingTask = null;
    public $showTaskModal = false;
    public $taskForm = [
        'title' => '',
        'description' => '',
        'task_list_id' => '',
        'user_id' => '',
        'cliente_id' => '',
        'priority' => 'medium',
        'due_date' => null
    ];

    protected $rules = [
        'taskForm.title' => 'required|string|max:255',
        'taskForm.description' => 'nullable|string',
        'taskForm.task_list_id' => 'required|exists:task_lists,id',
        'taskForm.user_id' => 'nullable|exists:users,id',
        'taskForm.cliente_id' => 'nullable|exists:clientes,id',
        'taskForm.priority' => 'required|in:low,medium,high',
        'taskForm.due_date' => 'nullable|date'
    ];

    protected $listeners = [
        'taskMoved' => 'handleTaskMoved',
        'listMoved' => 'handleListMoved'
    ];

    public function mount(Board $board)
    {
        $this->board = $board;
        $this->refreshBoard();
    }

    public function refreshBoard()
    {
        $this->board->refresh();
        $this->lists = $this->board->lists()->with(['tasks' => function ($query) {
            $query->orderBy('position');
        }])->orderBy('position')->get();
    }

    public function createTask()
    {
        $this->validate();

        Task::create([
            ...$this->taskForm,
            'position' => Task::where('task_list_id', $this->taskForm['task_list_id'])->count()
        ]);

        $this->resetTaskForm();
        $this->showTaskModal = false;
        $this->refreshBoard();
    }

    public function editTask(Task $task)
    {
        $this->editingTask = $task;
        $this->taskForm = [
            'title' => $task->title,
            'description' => $task->description,
            'task_list_id' => $task->task_list_id,
            'user_id' => $task->user_id,
            'cliente_id' => $task->cliente_id,
            'priority' => $task->priority,
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null
        ];
        $this->showTaskModal = true;
    }

    public function updateTask()
    {
        $this->validate();

        $this->editingTask->update($this->taskForm);

        $this->resetTaskForm();
        $this->showTaskModal = false;
        $this->refreshBoard();
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
        $this->refreshBoard();
    }

    public function handleTaskMoved($taskId, $newListId, $newPosition)
    {
        $task = Task::find($taskId);
        $oldListId = $task->task_list_id;

        // Atualizar a posição da tarefa movida
        $task->update([
            'task_list_id' => $newListId,
            'position' => $newPosition
        ]);

        // Reordenar tarefas na lista de origem
        if ($oldListId != $newListId) {
            $this->reorderTasks(TaskList::find($oldListId));
        }

        // Reordenar tarefas na lista de destino
        $this->reorderTasks(TaskList::find($newListId));

        $this->refreshBoard();
    }

    public function handleListMoved($listId, $newPosition)
    {
        $list = TaskList::find($listId);
        $list->update(['position' => $newPosition]);

        // Reordenar todas as listas
        $lists = $this->board->lists()->orderBy('position')->get();
        foreach ($lists as $index => $list) {
            if ($list->position != $index) {
                $list->update(['position' => $index]);
            }
        }

        $this->refreshBoard();
    }

    private function reorderTasks(TaskList $list)
    {
        $tasks = $list->tasks()->orderBy('position')->get();
        foreach ($tasks as $index => $task) {
            if ($task->position != $index) {
                $task->update(['position' => $index]);
            }
        }
    }

    private function resetTaskForm()
    {
        $this->taskForm = [
            'title' => '',
            'description' => '',
            'task_list_id' => '',
            'user_id' => '',
            'cliente_id' => '',
            'priority' => 'medium',
            'due_date' => null
        ];
        $this->editingTask = null;
    }

    public function render()
    {
        return view('livewire.task-board', [
            'users' => User::all(),
            'clientes' => Cliente::all()
        ]);
    }
}
