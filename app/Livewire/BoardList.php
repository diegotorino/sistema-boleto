<?php

namespace App\Livewire;

use App\Models\Board;
use Livewire\Component;

class BoardList extends Component
{
    public function openBoard($boardId)
    {
        $board = Board::with(['lists.tasks.user', 'lists.tasks.cliente'])->find($boardId);
        
        return [
            'id' => $board->id,
            'name' => $board->name,
            'description' => $board->description,
            'background_color' => $board->background_color,
            'lists' => $board->lists->map(function($list) {
                return [
                    'id' => $list->id,
                    'name' => $list->name,
                    'tasks' => $list->tasks->map(function($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'description' => $task->description,
                            'priority' => $task->priority,
                            'due_date' => $task->due_date ? $task->due_date->format('d/m/Y') : null,
                            'user' => $task->user ? [
                                'name' => $task->user->name,
                                'avatar' => $task->user->profile_photo_url
                            ] : null,
                            'cliente' => $task->cliente ? [
                                'name' => $task->cliente->name
                            ] : null
                        ];
                    })
                ];
            })
        ];
    }

    public function render()
    {
        return view('livewire.board-list', [
            'boards' => Board::where('user_id', auth()->id())->get()
        ]);
    }
}
