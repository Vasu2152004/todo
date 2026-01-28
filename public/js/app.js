// Todo App JavaScript

(function() {
    'use strict';

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-dismissible')) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Clear form on modal close
    const createModal = document.getElementById('createTodoModal');
    if (createModal) {
        createModal.addEventListener('hidden.bs.modal', function() {
            const form = createModal.querySelector('form');
            if (form) {
                form.reset();
                form.classList.remove('was-validated');
            }
        });
    }

    // Set minimum date to today for due date inputs
    const dueDateInputs = document.querySelectorAll('input[type="date"][name="due_date"], input[type="date"][id="due_date"], input[type="date"][id="edit_due_date"]');
    const today = new Date().toISOString().split('T')[0];
    dueDateInputs.forEach(function(input) {
        input.setAttribute('min', today);
    });

    // Confirm delete with better UX
    window.deleteTodo = function(id) {
        if (confirm('Are you sure you want to delete this todo? This action cannot be undone.')) {
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
    };

    // Toggle todo with loading state
    window.toggleTodo = function(id) {
        const checkbox = document.getElementById(`todo-${id}`);
        if (checkbox) {
            checkbox.disabled = true;
        }

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
    };

    // Edit todo function
    window.editTodo = function(id) {
        const todos = window.todos || {};
        const todo = todos[id];
        if (!todo) return;

        const form = document.getElementById('editTodoForm');
        if (form) {
            form.action = `/todos/${id}`;
        }

        const titleInput = document.getElementById('edit_title');
        const descInput = document.getElementById('edit_description');
        const prioritySelect = document.getElementById('edit_priority');
        const dueDateInput = document.getElementById('edit_due_date');

        if (titleInput) titleInput.value = todo.title || '';
        if (descInput) descInput.value = todo.description || '';
        if (prioritySelect) prioritySelect.value = todo.priority || 'medium';
        if (dueDateInput) {
            dueDateInput.value = todo.due_date ? todo.due_date.split('T')[0] : '';
        }
    };

    console.log('Todo App JavaScript loaded successfully!');
})();
