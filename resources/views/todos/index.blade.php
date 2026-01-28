@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-list-check me-2"></i>My Todo List
            </h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTodoModal">
                <i class="bi bi-plus-circle me-1"></i>Add New Todo
            </button>
        </div>

        <!-- Filters and Sort -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('todos.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="filter" class="form-label">Filter</label>
                        <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Todos</option>
                            <option value="active" {{ $filter === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $filter === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sort" class="form-label">Sort By</label>
                        <select name="sort" id="sort" class="form-select" onchange="this.form.submit()">
                            <option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>Date Created</option>
                            <option value="due_date" {{ $sort === 'due_date' ? 'selected' : '' }}>Due Date</option>
                            <option value="priority" {{ $sort === 'priority' ? 'selected' : '' }}>Priority</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="direction" class="form-label">Direction</label>
                        <select name="direction" id="direction" class="form-select" onchange="this.form.submit()">
                            <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Todos List -->
        @if($todos->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h3 class="mt-3">No todos found</h3>
                    <p class="text-muted">Get started by creating your first todo item!</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTodoModal">
                        <i class="bi bi-plus-circle me-1"></i>Create Todo
                    </button>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($todos as $todo)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 {{ $todo->completed ? 'border-success' : ($todo->isOverdue() ? 'border-danger' : '') }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="todo-{{ $todo->id }}" 
                                               {{ $todo->completed ? 'checked' : '' }}
                                               onchange="toggleTodo({{ $todo->id }})">
                                        <label class="form-check-label" for="todo-{{ $todo->id }}">
                                            <strong class="{{ $todo->completed ? 'text-decoration-line-through text-muted' : '' }}">
                                                {{ $todo->title }}
                                            </strong>
                                        </label>
                                    </div>
                                    <span class="badge bg-{{ $todo->priority_color }}">
                                        {{ ucfirst($todo->priority) }}
                                    </span>
                                </div>
                                
                                @if($todo->description)
                                    <p class="card-text text-muted small mb-2 {{ $todo->completed ? 'text-decoration-line-through' : '' }}">
                                        {{ \Illuminate\Support\Str::limit($todo->description, 100) }}
                                    </p>
                                @endif

                                @if($todo->due_date)
                                    <p class="mb-2">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <small class="{{ $todo->isOverdue() && !$todo->completed ? 'text-danger fw-bold' : 'text-muted' }}">
                                            Due: {{ $todo->due_date->format('M d, Y') }}
                                            @if($todo->isOverdue() && !$todo->completed)
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            @endif
                                        </small>
                                    </p>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $todo->created_at->diffForHumans() }}
                                    </small>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editTodo({{ $todo->id }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editTodoModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteTodo({{ $todo->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Create Todo Modal -->
<div class="modal fade" id="createTodoModal" tabindex="-1" aria-labelledby="createTodoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('todos.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createTodoModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Create New Todo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority">
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Create Todo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Todo Modal -->
<div class="modal fade" id="editTodoModal" tabindex="-1" aria-labelledby="editTodoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTodoForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editTodoModalLabel">
                        <i class="bi bi-pencil me-2"></i>Edit Todo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_priority" class="form-label">Priority</label>
                            <select class="form-select" id="edit_priority" name="priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="edit_due_date" name="due_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Update Todo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Store todo data for editing
    const todos = @json($todos->keyBy('id'));

    function editTodo(id) {
        const todo = todos[id];
        if (!todo) return;

        document.getElementById('editTodoForm').action = `/todos/${id}`;
        document.getElementById('edit_title').value = todo.title;
        document.getElementById('edit_description').value = todo.description || '';
        document.getElementById('edit_priority').value = todo.priority;
        document.getElementById('edit_due_date').value = todo.due_date || '';
    }

    function toggleTodo(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/todos/${id}/toggle`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    function deleteTodo(id) {
        if (confirm('Are you sure you want to delete this todo?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/todos/${id}`;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
@endsection
