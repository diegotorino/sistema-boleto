<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::where('user_id', auth()->id())->with('lists.tasks')->get();
        return view('boards.index', compact('boards'));
    }

    public function create()
    {
        return view('boards.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'background_color' => 'nullable|string|max:7'
        ]);

        $board = Board::create([
            ...$validated,
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

        return redirect()->route('boards.show', $board)
            ->with('success', 'Quadro criado com sucesso!');
    }

    public function show(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        $board->load('lists.tasks.user', 'lists.tasks.cliente');
        return view('boards.show', compact('board'));
    }

    public function edit(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        return view('boards.edit', compact('board'));
    }

    public function update(Request $request, Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'background_color' => 'nullable|string|max:7'
        ]);

        $board->update($validated);

        return redirect()->route('boards.show', $board)
            ->with('success', 'Quadro atualizado com sucesso!');
    }

    public function destroy(Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            abort(403);
        }

        $board->delete();

        return redirect()->route('boards.index')
            ->with('success', 'Quadro excluído com sucesso!');
    }
}
