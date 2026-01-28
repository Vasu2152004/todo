# Simple PHP Todo Application

A clean, simple todo list application built with pure PHP (no framework), MySQL, and Bootstrap 5.

## Features

- ✅ Create, Read, Update, Delete todos
- ✅ Mark todos as completed/incomplete
- ✅ Priority levels (Low, Medium, High)
- ✅ Due date tracking with overdue indicators
- ✅ Filter todos (All, Active, Completed)
- ✅ Responsive Bootstrap 5 UI
- ✅ RESTful API
- ✅ Production-ready

## Requirements

- PHP >= 8.1
- MySQL >= 5.7 or MariaDB >= 10.3
- Web server (Apache/Nginx) or PHP built-in server

## Installation

### 1. Clone or Download

```bash
git clone <your-repo-url>
cd todo-app
```

### 2. Database Setup

Create the database and tables:

```bash
mysql -u root -p < database/schema.sql
```

Or manually:

```sql
CREATE DATABASE todo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE todo_app;
-- Then run the SQL from database/schema.sql
```

### 3. Configure Environment

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Start Development Server

```bash
php -S localhost:8000
```

Visit `http://localhost:8000` in your browser.

## Project Structure

```
todo-app/
├── api/
│   └── todos.php          # REST API endpoints
├── assets/
│   ├── css/
│   │   └── style.css      # Custom styles
│   └── js/
│       └── app.js          # Frontend JavaScript
├── config/
│   └── database.php       # Database connection class
├── database/
│   └── schema.sql         # Database schema
├── index.php              # Main entry point
├── .htaccess              # Apache rewrite rules
├── .env                   # Environment variables
├── .env.example           # Environment template
├── app.yaml               # Wasmer Edge config
└── wasmer.toml            # Wasmer package config
```

## API Endpoints

### Get All Todos
```
GET /api/todos.php?action=list&filter=all
```

Filters: `all`, `active`, `completed`

### Get Single Todo
```
GET /api/todos.php?action=get&id=1
```

### Create Todo
```
POST /api/todos.php?action=create
Content-Type: application/json

{
  "title": "My Todo",
  "description": "Description here",
  "priority": "medium",
  "due_date": "2024-12-31"
}
```

### Update Todo
```
PUT /api/todos.php?action=update
Content-Type: application/json

{
  "id": 1,
  "title": "Updated Todo",
  "description": "Updated description",
  "priority": "high",
  "due_date": "2024-12-31"
}
```

### Toggle Todo Completion
```
POST /api/todos.php?action=toggle
Content-Type: application/json

{
  "id": 1
}
```

### Delete Todo
```
DELETE /api/todos.php?action=delete&id=1
```

## Deployment

### Wasmer Edge

1. Ensure `app.yaml` is in repository root
2. Set environment variables in Wasmer Edge dashboard:
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - (Or use Wasmer Edge's auto-provisioned database)
3. Deploy from GitHub repository
4. Run database migration:
   ```bash
   mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE < database/schema.sql
   ```

### Traditional Server

1. Upload files to web server
2. Configure database in `.env`
3. Run `database/schema.sql` to create tables
4. Set proper file permissions
5. Configure web server (Apache/Nginx)

## Database Schema

```sql
CREATE TABLE todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    completed TINYINT(1) DEFAULT 0,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Security Notes

- Uses PDO prepared statements to prevent SQL injection
- Input validation and sanitization
- CSRF protection recommended for production
- Environment variables for sensitive data
- `.htaccess` protects sensitive files

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## License

MIT License

## Support

For issues, please check:
- PHP error logs
- Browser console for JavaScript errors
- Database connection settings in `.env`
