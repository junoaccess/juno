# VitePress Documentation Integration

This document explains how the VitePress documentation system has been integrated with Juno.

## Overview

Juno now includes a professional documentation site built with VitePress that is served directly through Laravel routes. This allows the documentation to benefit from Laravel's middleware system while maintaining the performance of a static site.

## What's Been Completed

### 1. VitePress Installation & Configuration

- ✅ Installed VitePress v1.6.4 as a dev dependency
- ✅ Created VitePress configuration at `docs/.vitepress/config.ts`
- ✅ Set up navigation and sidebar structure
- ✅ Configured theme with Juno branding (#3b82f6 primary color)
- ✅ Enabled local search
- ✅ Added edit links to GitHub

### 2. Documentation Structure

Created the following documentation pages:

**Home**
- `docs/index.md` - Landing page with hero section and features

**Guide Section**
- `docs/guide/introduction.md` - What is Juno and core concepts
- `docs/guide/getting-started.md` - Installation and setup guide
- `docs/guide/architecture.md` - Architecture overview with diagrams
- `docs/guide/organisations.md` - Organisation and access control guide

**API Section**
- `docs/api/overview.md` - API documentation with endpoints and examples

### 3. Laravel Integration

- ✅ Created `DocsController` to serve VitePress static files
- ✅ Added `/docs` routes in `routes/web/routes.php`
- ✅ Controller handles both static assets and SPA client-side routing
- ✅ Written and passing tests for the docs controller

### 4. Build System

Added npm scripts in `package.json`:
```json
{
  "docs:dev": "vitepress dev docs",
  "docs:build": "vitepress build docs",
  "docs:preview": "vitepress preview docs",
  "docs:copy": "rm -rf public/docs && cp -r docs/.vitepress/dist public/docs"
}
```

### 5. Git Configuration

Updated `.gitignore` to exclude:
- `/docs/.vitepress/cache` - VitePress cache files
- `/docs/.vitepress/dist` - VitePress build output
- `/public/docs` - Copied documentation files

## How It Works

### Development Workflow

1. **Edit documentation**: Edit markdown files in `docs/`
2. **Preview changes**: Run `npm run docs:dev` to preview at http://localhost:5173
3. **Build for production**: Run `npm run docs:build` to create static files
4. **Copy to public**: Run `npm run docs:copy` to copy files to `public/docs`
5. **Serve via Laravel**: Access docs at http://your-app.test/docs

### Production Workflow

The VitePress documentation is served as static files through Laravel:

1. VitePress builds the docs to `docs/.vitepress/dist`
2. Files are copied to `public/docs` via npm script
3. Laravel routes at `/docs/*` are handled by `DocsController`
4. Controller serves static files directly (JS, CSS, images)
5. All other routes return `index.html` for client-side routing

### Route Configuration

```php
Route::prefix('docs')->group(function () {
    Route::get('/{any?}', DocsController::class)
        ->where('any', '.*')
        ->name('docs');
});
```

This configuration:
- Catches all requests under `/docs`
- Allows VitePress client-side router to handle navigation
- Can be wrapped with middleware (e.g., `auth`) if needed

## Documentation Structure

```
docs/
├── .vitepress/
│   ├── config.ts          # VitePress configuration
│   ├── cache/             # Build cache (gitignored)
│   └── dist/              # Build output (gitignored)
├── guide/
│   ├── introduction.md
│   ├── getting-started.md
│   ├── architecture.md
│   └── organisations.md
├── api/
│   └── overview.md
└── index.md               # Home page
```

## Next Steps

### Documentation Pages to Create

The VitePress config references these pages that still need to be created:

**Guide Section:**
- `docs/guide/installation.md` - Detailed installation guide
- `docs/guide/backend-structure.md` - Backend code organization
- `docs/guide/frontend-structure.md` - Frontend code organization
- `docs/guide/roles-and-permissions.md` - RBAC system details
- `docs/guide/teams.md` - Team management
- `docs/guide/invitations.md` - Invitation workflow
- `docs/guide/local-development.md` - Development environment setup
- `docs/guide/testing.md` - Testing guide with examples
- `docs/guide/code-style.md` - Code conventions and standards

**API Section:**
- `docs/api/authentication.md` - Authentication endpoints
- `docs/api/users.md` - User resource endpoints
- `docs/api/organisations.md` - Organisation endpoints
- `docs/api/teams.md` - Team endpoints
- `docs/api/roles.md` - Role endpoints
- `docs/api/permissions.md` - Permission endpoints
- `docs/api/invitations.md` - Invitation endpoints

### Enhancements

1. **Logo**: Copy `public/logo.svg` to `docs/public/logo.svg` for VitePress access
2. **Dead Links**: Once all pages are created, remove `ignoreDeadLinks: true` from config
3. **Code Syntax**: Add `shiki` language support for `.env` files
4. **CI Integration**: Add docs build step to GitHub Actions workflow
5. **Deployment**: Consider deploying docs separately or as part of main app

## Tips

### Writing Documentation

- Use "organisation" terminology consistently (never "tenant")
- Follow existing tone: professional, concise, helpful
- Include code examples with proper syntax highlighting
- Add frontmatter for special pages (e.g., `layout: home`)

### Building Locally

```bash
# Start dev server with hot reload
npm run docs:dev

# Build static site
npm run docs:build

# Preview production build
npm run docs:preview

# Copy to Laravel public directory
npm run docs:copy
```

### Adding New Pages

1. Create the markdown file in appropriate directory
2. Add link to sidebar in `docs/.vitepress/config.ts`
3. Test with `npm run docs:dev`
4. Build and copy for production

### Customizing Theme

Edit `docs/.vitepress/config.ts` to customize:
- Navigation items
- Sidebar structure
- Theme colors and styling
- Search behavior
- Footer content
- Social links

## Testing

Tests are located at `tests/Feature/Http/Controllers/DocsControllerTest.php`:

```bash
# Run docs tests
php artisan test --filter=DocsController

# Run all tests
php artisan test
```

Current tests verify:
- ✅ Index page is served correctly
- ✅ Client-side routing works (sub-pages return index.html)
- ✅ Static assets are handled properly

## Troubleshooting

**Issue**: Docs not loading
- Ensure `npm run docs:build` completed successfully
- Verify `npm run docs:copy` was run after building
- Check that `public/docs/index.html` exists

**Issue**: Changes not reflected
- Clear VitePress cache: `rm -rf docs/.vitepress/cache`
- Rebuild: `npm run docs:build && npm run docs:copy`
- Hard refresh browser (Cmd+Shift+R)

**Issue**: Dead link warnings during build
- This is expected until all referenced pages are created
- Temporarily disabled with `ignoreDeadLinks: true`
- Remove this setting once all pages exist

## Resources

- [VitePress Documentation](https://vitepress.dev/)
- [VitePress Theme Config](https://vitepress.dev/reference/default-theme-config)
- [Markdown Extensions](https://vitepress.dev/guide/markdown)
