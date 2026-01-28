# Laravel Todo List Application

A production-ready Laravel todo list application with Bootstrap UI, featuring full CRUD operations, filtering, sorting, and a modern, responsive design.

## Features

- ✅ Create, Read, Update, Delete todos
- ✅ Mark todos as completed/incomplete
- ✅ Priority levels (Low, Medium, High)
- ✅ Due date tracking with overdue indicators
- ✅ Filter todos (All, Active, Completed)
- ✅ Sort todos by date, priority, or due date
- ✅ Responsive Bootstrap 5 UI
- ✅ Form validation and error handling
- ✅ Flash messages for user feedback
- ✅ Production-ready configuration

## Requirements

- PHP >= 8.1
- Composer
- Node.js >= 18.x and npm
- MySQL/MariaDB
- Web server (Apache/Nginx) or PHP built-in server

## Installation

### 1. Clone or Download the Project

```bash
cd /path/to/your/project
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Edit the `.env` file and configure your database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create Database

Create a MySQL database:

```sql
CREATE DATABASE todo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. (Optional) Seed Sample Data

```bash
php artisan db:seed
```

This will create 5 sample todos for testing.

### 8. Set Permissions

Ensure storage and cache directories are writable:

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache

# Windows (if needed)
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### 9. Build Assets

For development (with hot reload):
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 10. Start Development Server

In one terminal, start Vite dev server:
```bash
npm run dev
```

In another terminal, start Laravel server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Production Deployment

> **Note:** For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md)

### Quick Answer: Pre-Deployment Commands

**Yes, you typically need to run these commands BEFORE deployment:**

```bash
# 1. Install production dependencies (optimized)
composer install --optimize-autoloader --no-dev

# 2. Generate application key (if not done)
php artisan key:generate

# 3. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**However**, some platforms (Laravel Forge, Railway, Render) handle this automatically - check [DEPLOYMENT.md](DEPLOYMENT.md) for platform-specific instructions.

### 1. Environment Configuration

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
LOG_LEVEL=error
```

### 2. Build Assets for Production

```bash
# Build optimized assets
npm run build
```

### 3. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 3. Web Server Configuration

#### Apache (.htaccess)

The `.htaccess` file in the `public` directory should handle URL rewriting. Ensure `mod_rewrite` is enabled.

#### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Project Structure

```
temp-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── TodoController.php
│   │   └── Requests/
│   │       └── TodoRequest.php
│   ├── Models/
│   │   └── Todo.php
│   └── Providers/
│       └── AppServiceProvider.php
├── config/
│   ├── app.php
│   ├── database.php
│   └── ...
├── database/
│   ├── migrations/
│   │   ├── 2024_01_28_000000_create_cache_table.php
│   │   ├── 2024_01_28_000001_create_todos_table.php
│   │   └── 2024_01_28_000002_create_sessions_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── TodoSeeder.php
├── public/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── index.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── components/
│       │   └── flash-messages.blade.php
│       └── todos/
│           └── index.blade.php
├── routes/
│   └── web.php
├── .env
├── .env.example
├── composer.json
└── README.md
```

## Usage

### Creating a Todo

1. Click the "Add New Todo" button
2. Fill in the title (required)
3. Optionally add description, priority, and due date
4. Click "Create Todo"

### Editing a Todo

1. Click the edit (pencil) icon on any todo card
2. Modify the fields
3. Click "Update Todo"

### Completing a Todo

1. Check the checkbox next to the todo title
2. The todo will be marked as completed automatically

### Deleting a Todo

1. Click the delete (trash) icon on any todo card
2. Confirm the deletion

### Filtering Todos

Use the filter dropdown to view:
- All Todos
- Active (incomplete) todos
- Completed todos

### Sorting Todos

Use the sort dropdown to organize todos by:
- Date Created
- Due Date
- Priority

## Database Schema

### todos table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | string(255) | Todo title (required) |
| description | text | Todo description (optional) |
| completed | boolean | Completion status (default: false) |
| priority | enum | Priority level: low, medium, high (default: medium) |
| due_date | date | Due date (optional) |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

## Security Considerations

- CSRF protection enabled on all forms
- Input validation and sanitization
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating
- Environment variables for sensitive data
- Production error handling (no stack traces)

## Troubleshooting

### Database Connection Error

- Verify database credentials in `.env`
- Ensure MySQL service is running
- Check database exists and user has permissions

### 500 Error

- Check `storage/logs/laravel.log` for errors
- Verify file permissions on `storage/` and `bootstrap/cache/`
- Ensure `.env` file exists and `APP_KEY` is set

### Styles/JavaScript Not Loading

- Clear browser cache
- Verify `public/css/app.css` and `public/js/app.js` exist
- Check web server configuration for static file serving

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please check the Laravel documentation at [https://laravel.com/docs](https://laravel.com/docs).
