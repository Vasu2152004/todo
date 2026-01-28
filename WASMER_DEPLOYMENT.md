# Wasmer Deployment Guide

## Important: Wasmer Now Supports PHP/Laravel!

Wasmer has added support for PHP applications through their Shipit build system. This guide will help you deploy your Laravel Todo app to Wasmer.

## Prerequisites

Before deploying to Wasmer, you need to:

1. **Generate `composer.lock` file** (REQUIRED)
   ```bash
   composer install
   ```
   This creates the `composer.lock` file that Wasmer needs for reproducible builds.

2. **Commit `composer.lock` to Git**
   ```bash
   git add composer.lock
   git commit -m "Add composer.lock for Wasmer deployment"
   git push
   ```

## Deployment Steps

### 1. Generate composer.lock (CRITICAL)

**You MUST run this locally before deploying:**

```bash
# Install dependencies to generate composer.lock
composer install

# Commit the lock file
git add composer.lock
git commit -m "Add composer.lock"
git push
```

### 2. Configure Wasmer

Wasmer uses the `Shipit` file for configuration. The file is already created with:

```json
{
  "package_manager": "pnpm",
  "build_command": "pnpm run build",
  "use_composer": true,
  "composer_build_script": "post-update-cmd"
}
```

### 3. Set Environment Variables in Wasmer

In your Wasmer dashboard, set these environment variables:

```env
APP_NAME="Laravel Todo App"
APP_ENV=production
APP_KEY=base64:... (generate with: php artisan key:generate --show)
APP_DEBUG=false
APP_URL=https://your-wasmer-app-url.wasmer.app

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

### 4. Database Setup

Wasmer doesn't provide a built-in database. You'll need to:

1. Use an external MySQL/MariaDB service (like PlanetScale, Railway DB, or AWS RDS)
2. Or use Wasmer's database addon if available
3. Set the database connection details in environment variables

### 5. Deploy

1. Connect your GitHub repository to Wasmer
2. Wasmer will automatically detect the `Shipit` file
3. The build process will:
   - Install PHP dependencies via Composer
   - Install Node.js dependencies via pnpm
   - Build assets with `pnpm run build`
   - Deploy your application

## Build Process

Wasmer's build process (from the logs):

1. **Setup Phase:**
   - Creates app and sets up secrets

2. **Deployment Phase:**
   - Clones Git repository
   - Runs Shipit build system
   - Installs Composer dependencies (requires `composer.lock`)
   - Installs pnpm dependencies
   - Runs `pnpm run build` to compile assets
   - Creates Docker image
   - Deploys application

## Troubleshooting

### Error: "/composer.lock": not found

**Solution:** You must generate `composer.lock` locally:

```bash
composer install
git add composer.lock
git commit -m "Add composer.lock"
git push
```

Then redeploy on Wasmer.

### Build Fails on Composer Install

- Ensure `composer.lock` exists and is committed
- Check that `composer.json` is valid
- Verify all required PHP extensions are available

### Assets Not Loading

- Ensure `npm run build` completes successfully
- Check that `public/build/` directory exists after build
- Verify Vite manifest is generated

### Database Connection Errors

- Verify database credentials in environment variables
- Ensure database is accessible from Wasmer's network
- Check firewall rules if using external database

### Application Key Missing

Generate and set `APP_KEY` in Wasmer environment variables:

```bash
# Locally
php artisan key:generate --show

# Copy the output and set in Wasmer dashboard as APP_KEY
```

## Post-Deployment

After successful deployment:

1. **Run Migrations:**
   ```bash
   # Via Wasmer CLI or dashboard
   php artisan migrate --force
   ```

2. **Seed Database (Optional):**
   ```bash
   php artisan db:seed
   ```

3. **Cache Configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## File Structure for Wasmer

Required files (already in project):
- ✅ `composer.json` - PHP dependencies
- ✅ `composer.lock` - **YOU MUST GENERATE THIS** (`composer install`)
- ✅ `package.json` - Node.js dependencies
- ✅ `vite.config.js` - Vite configuration
- ✅ `Shipit` - Wasmer build configuration
- ✅ `.env.example` - Environment template

## Quick Fix for Current Error

If you're seeing the `composer.lock` error right now:

```bash
# 1. Run locally to generate lock file
composer install

# 2. Commit and push
git add composer.lock
git commit -m "Add composer.lock for Wasmer"
git push origin main

# 3. Redeploy on Wasmer
```

## Additional Resources

- [Wasmer Documentation](https://docs.wasmer.io/)
- [Shipit Documentation](https://github.com/wasmerio/shipit)
- [Laravel Deployment](https://laravel.com/docs/deployment)
