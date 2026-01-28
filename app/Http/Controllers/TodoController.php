<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Todo::query();

        $filter = $request->get('filter', 'all');
        if ($filter === 'active') {
            $query->active();
        } elseif ($filter === 'completed') {
            $query->completed();
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if ($sort === 'priority') {
            $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
            if ($direction === 'asc') {
                $query->orderByRaw("FIELD(priority, 'low', 'medium', 'high')");
            }
        } else {
            $query->orderBy($sort, $direction);
        }

        $todos = $query->get();

        return view('todos.index', compact('todos', 'filter', 'sort', 'direction'));
    }

    public function store(TodoRequest $request): RedirectResponse
    {
        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    public function update(TodoRequest $request, Todo $todo): RedirectResponse
    {
        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? $todo->priority,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('todos.index')
            ->with('success', 'Todo updated successfully!');
    }

    public function destroy(Todo $todo): RedirectResponse
    {
        $todo->delete();

        return redirect()->route('todos.index')
            ->with('success', 'Todo deleted successfully!');
    }

    public function toggle(Todo $todo): RedirectResponse
    {
        $todo->update([
            'completed' => !$todo->completed,
        ]);

        $message = $todo->completed 
            ? 'Todo marked as completed!' 
            : 'Todo marked as active!';

        return redirect()->route('todos.index')
            ->with('success', $message);
    }
}
