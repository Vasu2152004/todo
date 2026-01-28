# Vite Setup Guide

This Laravel application uses **Vite** for modern asset bundling and compilation.

## What is Vite?

Vite is a next-generation frontend build tool that provides:
- ⚡ Lightning-fast HMR (Hot Module Replacement) during development
- 🚀 Optimized production builds
- 📦 Automatic code splitting and tree-shaking
- 🎯 Modern ES modules support

## Project Structure

```
resources/
├── css/
│   └── app.css          # Main stylesheet (includes Bootstrap)
├── js/
│   ├── app.js           # Main JavaScript file
│   └── bootstrap.js     # Axios configuration
```

## Development Setup

### 1. Install Dependencies

```bash
npm install
```

### 2. Start Development Server

Run Vite dev server (with hot reload):
```bash
npm run dev
```

In another terminal, start Laravel:
```bash
php artisan serve
```

Visit `http://localhost:8000` - changes to CSS/JS will hot-reload automatically!

## Production Build

### Build Assets

```bash
npm run build
```

This will:
- Compile and minify CSS
- Bundle and optimize JavaScript
- Generate assets in `public/build/` directory
- Create manifest file for Laravel to reference

### What Gets Built?

- `resources/css/app.css` → Compiled with Bootstrap included
- `resources/js/app.js` → Bundled with all dependencies
- Output: `public/build/assets/` (hashed filenames for cache busting)

## How It Works

### In Blade Templates

The `@vite()` directive in `resources/views/layouts/app.blade.php`:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

**Development:** Loads assets from Vite dev server (localhost:5173)  
**Production:** Loads optimized assets from `public/build/`

### Asset Imports

In `resources/js/app.js`:
```javascript
import 'bootstrap';                    // Bootstrap JS
import 'bootstrap/dist/css/bootstrap.min.css';  // Bootstrap CSS
import '../css/app.css';               // Custom styles
```

## Configuration

### vite.config.js

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,  // Auto-refresh on Blade changes
        }),
    ],
});
```

## Dependencies

### Production Dependencies
- `bootstrap` - CSS framework
- `@popperjs/core` - Required by Bootstrap

### Development Dependencies
- `vite` - Build tool
- `laravel-vite-plugin` - Laravel integration
- `axios` - HTTP client (for future AJAX features)

## Troubleshooting

### Assets Not Loading

1. **Development:**
   - Ensure `npm run dev` is running
   - Check browser console for errors
   - Verify Vite dev server is on port 5173

2. **Production:**
   - Run `npm run build` before deployment
   - Check `public/build/` directory exists
   - Verify file permissions

### Build Errors

```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf node_modules/.vite
npm run build
```

### Port Already in Use

If port 5173 is taken, Vite will use the next available port. Check terminal output for the actual port.

## Deployment

### Pre-Deployment Checklist

- [ ] Run `npm run build` to compile assets
- [ ] Verify `public/build/` directory exists
- [ ] Test production build locally
- [ ] Include `package.json` in deployment
- [ ] Configure build step on hosting platform

### Platform-Specific Notes

**Laravel Forge:** Add `npm install && npm run build` to deployment script  
**Railway/Render:** Add to build command  
**Heroku:** Requires Node.js buildpack  
**Traditional VPS:** Run `npm run build` before or after deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed platform instructions.

## Switching Back to CDN (Not Recommended)

If you need to use CDN instead of Vite:

1. Remove `@vite()` directive from layout
2. Add CDN links back:
   ```blade
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   ```

**Note:** This loses optimization benefits and hot reload during development.

## Additional Resources

- [Vite Documentation](https://vitejs.dev/)
- [Laravel Vite Plugin](https://laravel.com/docs/vite)
- [Bootstrap Documentation](https://getbootstrap.com/)
