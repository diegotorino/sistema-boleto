<?php

namespace App\Livewire;

use App\Models\Board;
use Livewire\Component;

class CreateBoard extends Component
{
    public $showModal = false;
    public $name = '';
    public $description = '';
    public $background_color = '#f3f4f6';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'background_color' => 'required|string|max:7'
    ];

    public function createBoard()
    {
        $this->validate();

        $board = Board::create([
            'name' => $this->name,
            'description' => $this->description,
            'background_color' => $this->background_color,
            'user_id' => auth()->id()
        ]);

        // Criar listas padrão
        $defaultLists = [
            ['name' => 'A Fazer', 'position' => 0],
            ['name' => 'Em Progresso', 'position' => 1],
            ['name' => 'Concluído', 'position' => 2]
        ];

        foreach ($defaultLists as $list) {
            $board->lists()->create($list);
        }

        $this->reset(['showModal', 'name', 'description', 'background_color']);
        $this->dispatch('board-created');
        $this->dispatch('notify', ['message' => 'Quadro criado com sucesso!', 'type' => 'success']);

        return redirect()->route('boards.show', $board);
    }

    public function render()
    {
        return view('livewire.create-board');
    }
}
