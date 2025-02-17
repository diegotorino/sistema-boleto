<?php

namespace App\Livewire;

use App\Models\Board;
use Livewire\Component;

class ShowBoard extends Component
{
    public $showModal = false;
    public $board = null;

    protected $listeners = ['openBoard'];

    public function openBoard($boardId)
    {
        $this->board = Board::with(['lists.tasks.user', 'lists.tasks.cliente'])->find($boardId);
        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.show-board');
    }
}
