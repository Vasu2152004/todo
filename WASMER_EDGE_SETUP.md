# Wasmer Edge Deployment Guide

## Plan File Configuration

Wasmer Edge requires a plan file with the naming convention: `{username}.{repo-name}.plan.json`

For your setup, the file is: `vasu2152004.family-erp.plan.json`

## Plan File Structure

The plan file (`vasu2152004.family-erp.plan.json`) contains:

```json
{
  "name": "family-erp",
  "description": "Laravel Todo List Application",
  "runtime": "php",
  "php_version": "8.3",
  "build": {
    "commands": [
      "composer install --optimize-autoloader --no-dev --no-interaction",
      "pnpm install",
      "pnpm run build"
    ]
  },
  "start": {
    "command": "php artisan serve --host=0.0.0.0 --port=8000"
  },
  "environment": {
    "APP_ENV": "production",
    "APP_DEBUG": "false",
    "LOG_LEVEL": "error"
  },
  "database": {
    "type": "mysql",
    "version": "8.4"
  }
}
```

## Deployment Steps

### 1. Generate composer.lock (Required)

```bash
composer install
git add composer.lock
git commit -m "Add composer.lock"
git push
```

### 2. Commit Plan File

```bash
git add vasu2152004.family-erp.plan.json
git commit -m "Add Wasmer Edge plan file"
git push
```

### 3. Configure Environment Variables

In Wasmer Edge dashboard, set these environment variables:

**Application:**
- `APP_NAME` = "Laravel Todo App"
- `APP_ENV` = "production"
- `APP_DEBUG` = "false"
- `APP_KEY` = (generate with: `php artisan key:generate --show`)
- `APP_URL` = (your Wasmer Edge app URL)

**Database (Auto-configured by Wasmer Edge):**
- `DB_HOST` = `${DB_HOST}` (automatically set)
- `DB_PORT` = `${DB_PORT}` (automatically set)
- `DB_DATABASE` = `${DB_DATABASE}` (automatically set)
- `DB_USERNAME` = `${DB_USERNAME}` (automatically set)
- `DB_PASSWORD` = `${DB_PASSWORD}` (automatically set)

**Other:**
- `SESSION_DRIVER` = "database"
- `CACHE_STORE` = "database"
- `LOG_LEVEL` = "error"

### 4. Deploy

1. Connect your GitHub repository to Wasmer Edge
2. Wasmer Edge will detect the plan file automatically
3. The build process will:
   - Install PHP dependencies via Composer
   - Install Node.js dependencies via pnpm
   - Build assets with `pnpm run build`
   - Set up MySQL 8.4 database automatically
   - Deploy your application

### 5. Post-Deployment

After successful deployment, run migrations:

```bash
# Via Wasmer Edge CLI or dashboard
php artisan migrate --force

# Optional: Seed sample data
php artisan db:seed
```

## Database Configuration

Wasmer Edge automatically sets up a MySQL 8.4 database and exposes connection details through environment variables:

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

These are automatically injected into your application - no manual configuration needed!

## Troubleshooting

### Error: "Expected plan file not found"

**Solution:** Ensure the plan file is named exactly: `vasu2152004.family-erp.plan.json` and is committed to your repository.

### Build Fails on Composer Install

- Ensure `composer.lock` exists and is committed
- Check that `composer.json` is valid
- Verify PHP 8.3 is available (as specified in plan file)

### Database Connection Errors

- Verify environment variables are set correctly
- Check that Wasmer Edge has created the database
- Ensure migrations have run: `php artisan migrate --force`

### Assets Not Loading

- Verify `pnpm run build` completes successfully
- Check that `public/build/` directory exists
- Ensure Vite manifest is generated

## Plan File Options

You can customize the plan file:

### Change PHP Version

```json
"php_version": "8.1"  // or "8.2", "8.3"
```

### Add Build Commands

```json
"build": {
  "commands": [
    "composer install --optimize-autoloader --no-dev --no-interaction",
    "pnpm install",
    "pnpm run build",
    "php artisan config:cache",
    "php artisan route:cache"
  ]
}
```

### Custom Start Command

```json
"start": {
  "command": "php artisan serve --host=0.0.0.0 --port=8000"
}
```

## Files Required for Wasmer Edge

- ✅ `vasu2152004.family-erp.plan.json` - Plan configuration
- ✅ `composer.json` - PHP dependencies
- ✅ `composer.lock` - **YOU MUST GENERATE THIS** (`composer install`)
- ✅ `package.json` - Node.js dependencies
- ✅ `vite.config.js` - Vite configuration
- ✅ `Shipit` - Build configuration (for regular Wasmer)
- ✅ `.env.example` - Environment template

## Notes

- The plan file name must match: `{username}.{repo-name}.plan.json`
- Wasmer Edge automatically provisions MySQL 8.4 database
- Database credentials are injected via environment variables
- The build process runs all commands in the `build.commands` array
- The application starts with the command in `start.command`
