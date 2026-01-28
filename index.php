<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Todo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-check2-square me-2"></i>Simple Todo App
            </span>
        </div>
    </nav>

    <div class="container my-4">
        <div class="alert alert-danger d-none" id="errorAlert" role="alert"></div>
        <div class="alert alert-success d-none" id="successAlert" role="alert"></div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Todos</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#todoModal" onclick="openModal()">
                <i class="bi bi-plus-circle me-1"></i>Add New Todo
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Filter</label>
                        <select class="form-select" id="filterSelect" onchange="loadTodos()">
                            <option value="all">All Todos</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div id="todosContainer" class="row">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Todo Modal -->
    <div class="modal fade" id="todoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Todo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="todoForm">
                        <input type="hidden" id="todoId">
                        <div class="mb-3">
                            <label for="todoTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="todoTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="todoDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="todoDescription" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="todoPriority" class="form-label">Priority</label>
                                <select class="form-select" id="todoPriority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="todoDueDate" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="todoDueDate">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveTodo()">Save Todo</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light mt-5 py-4">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Simple Todo App. Built with PHP & Bootstrap.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
