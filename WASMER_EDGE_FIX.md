# Fix: Wasmer Edge Project Detection

## The Problem

Wasmer Edge is not detecting your Laravel project. This is because Wasmer Edge uses **`app.yaml`** for configuration, not `plan.json`.

## Solution: Create app.yaml

I've created the `app.yaml` file that Wasmer Edge expects. This file tells Wasmer Edge:
- Your project is a PHP/Laravel application
- How to build it (Composer + pnpm)
- How to start it (php artisan serve)
- Database configuration (MySQL 8.4)

## Files Created

1. ✅ **`app.yaml`** - Main Wasmer Edge configuration (REQUIRED)
2. ✅ **`wasmer.toml`** - Package manifest (optional but recommended)

## Next Steps

### 1. Commit the New Files

```bash
git add app.yaml wasmer.toml
git commit -m "Add Wasmer Edge configuration files"
git push
```

### 2. Ensure composer.lock Exists

If you haven't already:

```bash
composer install
git add composer.lock
git commit -m "Add composer.lock"
git push
```

### 3. Redeploy on Wasmer Edge

After committing both files:
1. Go to Wasmer Edge dashboard
2. Trigger a new deployment
3. Wasmer Edge should now detect your Laravel project!

## File Structure for Wasmer Edge

Required files:
- ✅ `app.yaml` - **Main configuration file** (NEW - REQUIRED)
- ✅ `composer.json` - PHP dependencies
- ✅ `composer.lock` - **YOU MUST GENERATE THIS** (`composer install`)
- ✅ `package.json` - Node.js dependencies
- ✅ `artisan` - Laravel CLI (for detection)
- ✅ `public/index.php` - Laravel entry point

Optional but recommended:
- ✅ `wasmer.toml` - Package manifest
- ✅ `.env.example` - Environment template

## app.yaml Configuration Explained

```yaml
name: todo                    # Your app name
runtime: php                  # PHP runtime
php_version: 8.3              # PHP version

build:
  commands:                   # Commands to run during build
    - composer install ...
    - pnpm install
    - pnpm run build

start:
  command: php artisan serve  # Command to start your app

database:
  type: mysql                 # Database type
  version: 8.4               # MySQL version

scaling:
  mode: single_concurrency    # PHP scaling mode
```

## Troubleshooting

### Still Not Detected?

1. **Check file names are exact:**
   - `app.yaml` (not `app.yml` or `App.yaml`)
   - File must be in repository root

2. **Verify files are committed:**
   ```bash
   git ls-files | grep app.yaml
   git ls-files | grep wasmer.toml
   ```

3. **Check repository structure:**
   - `artisan` file must exist (Laravel detection)
   - `composer.json` must exist (PHP detection)
   - `public/index.php` should exist

4. **Verify Git push:**
   ```bash
   git status
   git log --oneline -5
   ```

### Build Still Fails?

If build fails after detection:

1. **Ensure composer.lock exists:**
   ```bash
   composer install
   git add composer.lock
   git commit -m "Add composer.lock"
   git push
   ```

2. **Check environment variables** in Wasmer Edge dashboard:
   - `APP_KEY` must be set
   - Database variables will be auto-set by Wasmer Edge

## Difference: app.yaml vs plan.json

- **`app.yaml`** - Used by **Wasmer Edge** (the service you're using)
- **`plan.json`** - Used by a different Wasmer service (not Edge)
- **`Shipit`** - Used by regular Wasmer (not Edge)

Since you're using Wasmer Edge, you need `app.yaml`!

## After Fixing

Once you've:
1. ✅ Created `app.yaml`
2. ✅ Committed and pushed it
3. ✅ Generated and committed `composer.lock`
4. ✅ Redeployed on Wasmer Edge

Wasmer Edge should:
- ✅ Detect your Laravel project
- ✅ Start the build process
- ✅ Set up MySQL database automatically
- ✅ Deploy your application
