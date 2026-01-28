# Deployment Guide

## Important Note About Wasmer

**Wasmer is a WebAssembly runtime platform** and is not designed to run PHP/Laravel applications. Laravel requires:
- PHP runtime (PHP 8.1+)
- Composer for dependency management
- MySQL/MariaDB database
- Web server (Apache/Nginx)

## Recommended Hosting Platforms for Laravel

For Laravel applications, consider these platforms:

1. **Traditional VPS/Cloud Servers**
   - DigitalOcean, Linode, AWS EC2, Azure VM
   - Full control, requires server setup

2. **Platform-as-a-Service (PaaS)**
   - **Laravel Forge** (recommended for Laravel)
   - **Heroku** (with PHP buildpack)
   - **Railway**
   - **Render**
   - **Fly.io** (supports PHP/Laravel)

3. **Shared Hosting**
   - cPanel hosting with PHP 8.1+
   - Requires manual file upload

## Pre-Deployment Checklist

### Commands to Run BEFORE Deployment

You have two options:

#### Option 1: Run Locally (Recommended)
Run these commands on your local machine before uploading:

```bash
# 1. Install PHP dependencies
composer install --optimize-autoloader --no-dev

# 2. Install Node.js dependencies
npm install

# 3. Build assets for production
npm run build

# 4. Generate application key (if not already done)
php artisan key:generate

# 5. Copy .env.example to .env and configure
cp .env.example .env
# Edit .env with production settings

# 6. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Then upload:**
- All files EXCEPT `vendor/` (if platform installs via Composer)
- Or upload everything INCLUDING `vendor/` (if platform doesn't support Composer)

#### Option 2: Run on Server
If your hosting platform supports SSH/command line:

```bash
# After uploading files, SSH into server and run:
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate
```

## Deployment Steps by Platform

### Laravel Forge

1. Connect your Git repository
2. Configure server (PHP 8.1+, MySQL, Node.js)
3. Set environment variables in Forge dashboard
4. Add build script in Forge:
   - `npm install && npm run build`
5. Forge automatically runs:
   - `composer install --no-dev`
   - `npm install && npm run build` (if configured)
   - `php artisan migrate --force`
   - `php artisan config:cache`
   - `php artisan route:cache`

**Note:** You may need to configure Node.js build step in Forge deployment script.

### Heroku

1. Install Heroku CLI
2. Create `Procfile`:
   ```
   web: vendor/bin/heroku-php-apache2 public/
   ```
3. Add Node.js buildpack:
   ```bash
   heroku buildpacks:add heroku/nodejs
   heroku buildpacks:add heroku/php
   ```
4. Push to Heroku:
   ```bash
   git push heroku main
   ```
   Heroku will automatically run `npm install` and `npm run build`
5. Set environment variables:
   ```bash
   heroku config:set APP_KEY=$(php artisan key:generate --show)
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   ```
6. Run migrations:
   ```bash
   heroku run php artisan migrate
   ```

### Railway

1. Connect GitHub repository
2. Set environment variables in Railway dashboard
3. Configure build command:
   - `npm install && npm run build && composer install`
4. Configure start command:
   - `php artisan serve --host=0.0.0.0 --port=$PORT`
5. Railway automatically runs:
   - `npm install && npm run build`
   - `composer install`
   - `php artisan migrate`
6. Set `APP_KEY` in environment variables

### Render

1. Connect Git repository
2. Select "Web Service"
3. Build command: `npm install && npm run build && composer install --optimize-autoloader --no-dev`
4. Start command: `php artisan serve`
5. Set environment variables
6. Run migrations via Render shell

### Traditional VPS (Ubuntu/Debian)

1. **On your local machine:**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan key:generate
   ```

2. **Upload files** (via FTP/SFTP or Git):
   ```bash
   git clone your-repo.git
   cd your-project
   ```

3. **On server (SSH):**
   ```bash
   # Install PHP dependencies
   composer install --optimize-autoloader --no-dev
   
   # Install Node.js dependencies
   npm install
   
   # Build assets
   npm run build
   
   # Set permissions
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   
   # Configure environment
   cp .env.example .env
   nano .env  # Edit with production values
   
   # Generate key (if not done locally)
   php artisan key:generate
   
   # Cache configuration
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Run migrations
   php artisan migrate --force
   ```

### Shared Hosting (cPanel)

1. **On your local machine:**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan key:generate
   ```

2. **Upload files** via FTP/cPanel File Manager:
   - Upload all files to `public_html/` or your domain folder
   - Move `public/` contents to root, update paths

3. **Via cPanel Terminal or SSH:**
   ```bash
   cd ~/public_html
   composer install --optimize-autoloader --no-dev
   php artisan migrate --force
   ```

## Environment Variables for Production

Ensure these are set in your production environment:

```env
APP_NAME="Laravel Todo App"
APP_ENV=production
APP_KEY=base64:... (generated key)
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_STORE=database
LOG_LEVEL=error
```

## Files to Exclude from Deployment

Add to `.gitignore` (already included):
- `.env` (use `.env.example` as template)
- `vendor/` (if platform installs via Composer)
- `node_modules/`
- `storage/logs/*` (keep directory, ignore files)

## Post-Deployment Checklist

- [ ] Environment variables configured
- [ ] Application key generated
- [ ] Database migrations run
- [ ] Storage and cache directories writable
- [ ] Config cached (`php artisan config:cache`)
- [ ] Routes cached (`php artisan route:cache`)
- [ ] Views cached (`php artisan view:cache`)
- [ ] Web server configured (Apache/Nginx)
- [ ] SSL certificate installed (HTTPS)
- [ ] Domain configured
- [ ] Application accessible via browser

## Troubleshooting Deployment

### 500 Error After Deployment
- Check `storage/logs/laravel.log`
- Verify file permissions: `chmod -R 775 storage bootstrap/cache`
- Ensure `.env` file exists and is configured
- Clear config cache: `php artisan config:clear`

### Database Connection Error
- Verify database credentials in `.env`
- Check database server is accessible
- Ensure database exists and user has permissions

### Styles/JavaScript Not Loading
- Clear browser cache
- Verify `public/css/app.css` and `public/js/app.js` exist
- Check web server configuration for static files
- Ensure `APP_URL` in `.env` matches your domain

## Quick Answer: Do You Need to Run Commands Locally?

**It depends on your hosting platform:**

- **Laravel Forge, Railway, Render**: No - they handle it automatically
- **Heroku**: Minimal - just set environment variables
- **Traditional VPS**: Yes - run `composer install` and `php artisan key:generate` locally OR on server
- **Shared Hosting**: Yes - run locally, then upload files

**Best Practice:** Always run these commands before deployment:
- `composer install --optimize-autoloader --no-dev` (PHP dependencies)
- `npm install && npm run build` (Build assets)

Unless your platform handles these automatically (like Laravel Forge, Railway, or Render).
