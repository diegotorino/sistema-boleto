<?php

namespace App\Http\Controllers;

use App\Models\TaskBoard;
use App\Models\Cliente;
use App\Models\Boleto;
use Illuminate\Http\Request;

class TaskBoardController extends Controller
{
    public function index()
    {
        $tasks = [
            'inicio' => TaskBoard::where('status', 'inicio')->orderBy('position')->get(),
            'andamento' => TaskBoard::where('status', 'andamento')->orderBy('position')->get(),
            'concluido' => TaskBoard::where('status', 'concluido')->orderBy('position')->get()
        ];

        $clientes = Cliente::all();
        $boletos = Boleto::all();

        return view('tasks.index', compact('tasks', 'clientes', 'boletos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $boletos = Boleto::all();
        return view('tasks.create', compact('clientes', 'boletos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cliente_id' => 'nullable|exists:clientes,id',
            'boleto_id' => 'nullable|exists:boletos,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high'
        ]);

        $position = TaskBoard::where('status', 'inicio')->count();

        TaskBoard::create([
            ...$validated,
            'status' => 'inicio',
            'position' => $position,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarefa criada com sucesso!');
    }

    public function update(Request $request, TaskBoard $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'cliente_id' => 'nullable|exists:clientes,id',
            'boleto_id' => 'nullable|exists:boletos,id',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|required|in:low,medium,high'
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'task' => $task->fresh()->load('cliente', 'boleto')
        ]);
    }

    public function updateStatus(Request $request, TaskBoard $task)
    {
        $request->validate([
            'status' => 'required|in:inicio,andamento,concluido',
            'position' => 'required|integer|min:0'
        ]);

        // Atualizar posições das outras tarefas
        if ($task->status !== $request->status) {
            TaskBoard::where('status', $task->status)
                ->where('position', '>', $task->position)
                ->decrement('position');

            TaskBoard::where('status', $request->status)
                ->where('position', '>=', $request->position)
                ->increment('position');
        }

        $task->update([
            'status' => $request->status,
            'position' => $request->position
        ]);

        return response()->json(['success' => true]);
    }

    public function show(TaskBoard $task)
    {
        return response()->json($task->load('user', 'cliente', 'boleto'));
    }

    public function destroy(TaskBoard $task)
    {
        $task->delete();

        return response()->json(['success' => true]);
    }
}
