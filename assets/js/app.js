const API_BASE = 'api/todos.php';

let currentFilter = 'all';
let editingTodoId = null;

// Load todos on page load
document.addEventListener('DOMContentLoaded', () => {
    loadTodos();
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('todoDueDate').setAttribute('min', today);
});

function showAlert(message, type = 'success') {
    const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
    const alert = document.getElementById(alertId);
    alert.textContent = message;
    alert.classList.remove('d-none');
    
    setTimeout(() => {
        alert.classList.add('d-none');
    }, 5000);
}

async function loadTodos() {
    currentFilter = document.getElementById('filterSelect').value;
    const container = document.getElementById('todosContainer');
    
    container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    try {
        const response = await fetch(`${API_BASE}?action=list&filter=${currentFilter}`);
        const result = await response.json();
        
        if (result.success) {
            displayTodos(result.data);
        } else {
            throw new Error(result.message || 'Failed to load todos');
        }
    } catch (error) {
        container.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error: ${error.message}</div></div>`;
    }
}

function displayTodos(todos) {
    const container = document.getElementById('todosContainer');
    
    if (todos.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3 class="mt-3">No todos found</h3>
                    <p class="text-muted">Get started by creating your first todo!</p>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#todoModal" onclick="openModal()">
                        <i class="bi bi-plus-circle me-1"></i>Create Todo
                    </button>
                </div>
            </div>
        `;
        return;
    }
    
    container.innerHTML = todos.map(todo => createTodoCard(todo)).join('');
}

function createTodoCard(todo) {
    const isOverdue = todo.due_date && new Date(todo.due_date) < new Date() && !todo.completed;
    const borderClass = todo.completed ? 'border-success' : (isOverdue ? 'border-danger' : '');
    const completedClass = todo.completed ? 'todo-completed' : '';
    
    const priorityColors = {
        high: 'danger',
        medium: 'warning',
        low: 'info'
    };
    
    const dueDate = todo.due_date ? new Date(todo.due_date).toLocaleDateString() : '';
    
    return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card todo-card ${borderClass}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="check-${todo.id}" 
                                   ${todo.completed ? 'checked' : ''}
                                   onchange="toggleTodo(${todo.id})">
                            <label class="form-check-label ${completedClass}" for="check-${todo.id}">
                                <strong>${escapeHtml(todo.title)}</strong>
                            </label>
                        </div>
                        <span class="badge bg-${priorityColors[todo.priority]}">${todo.priority}</span>
                    </div>
                    
                    ${todo.description ? `<p class="card-text text-muted small mb-2 ${completedClass}">${escapeHtml(todo.description)}</p>` : ''}
                    
                    ${dueDate ? `
                        <p class="mb-2">
                            <i class="bi bi-calendar3 me-1"></i>
                            <small class="${isOverdue ? 'text-danger fw-bold' : 'text-muted'}">
                                Due: ${dueDate}
                                ${isOverdue ? '<span class="badge bg-danger ms-1">Overdue</span>' : ''}
                            </small>
                        </p>
                    ` : ''}
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            ${formatDate(todo.created_at)}
                        </small>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="editTodo(${todo.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteTodo(${todo.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return 'Today';
    if (diffDays === 2) return 'Yesterday';
    if (diffDays < 7) return `${diffDays - 1} days ago`;
    
    return date.toLocaleDateString();
}

function openModal(todoId = null) {
    editingTodoId = todoId;
    document.getElementById('modalTitle').textContent = todoId ? 'Edit Todo' : 'Add New Todo';
    document.getElementById('todoForm').reset();
    document.getElementById('todoId').value = '';
    
    if (todoId) {
        loadTodoForEdit(todoId);
    }
}

async function loadTodoForEdit(id) {
    try {
        const response = await fetch(`${API_BASE}?action=get&id=${id}`);
        const result = await response.json();
        
        if (result.success) {
            const todo = result.data;
            document.getElementById('todoId').value = todo.id;
            document.getElementById('todoTitle').value = todo.title;
            document.getElementById('todoDescription').value = todo.description || '';
            document.getElementById('todoPriority').value = todo.priority;
            document.getElementById('todoDueDate').value = todo.due_date || '';
        }
    } catch (error) {
        showAlert('Failed to load todo', 'error');
    }
}

async function saveTodo() {
    const form = document.getElementById('todoForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const todoId = document.getElementById('todoId').value;
    const data = {
        title: document.getElementById('todoTitle').value,
        description: document.getElementById('todoDescription').value,
        priority: document.getElementById('todoPriority').value,
        due_date: document.getElementById('todoDueDate').value || null
    };
    
    try {
        const action = todoId ? 'update' : 'create';
        if (todoId) data.id = parseInt(todoId);
        
        const response = await fetch(`${API_BASE}?action=${action}`, {
            method: todoId ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(todoId ? 'Todo updated successfully!' : 'Todo created successfully!');
            bootstrap.Modal.getInstance(document.getElementById('todoModal')).hide();
            loadTodos();
        } else {
            throw new Error(result.message || 'Failed to save todo');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

async function toggleTodo(id) {
    try {
        const response = await fetch(`${API_BASE}?action=toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadTodos();
        } else {
            throw new Error(result.message || 'Failed to toggle todo');
        }
    } catch (error) {
        showAlert(error.message, 'error');
        loadTodos(); // Reload to reset checkbox
    }
}

async function editTodo(id) {
    openModal(id);
    bootstrap.Modal.getInstance(document.getElementById('todoModal')).show();
}

async function deleteTodo(id) {
    if (!confirm('Are you sure you want to delete this todo?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}?action=delete&id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Todo deleted successfully!');
            loadTodos();
        } else {
            throw new Error(result.message || 'Failed to delete todo');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}
