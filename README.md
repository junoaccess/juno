# Juno

[![CI](https://github.com/usejuno/juno/workflows/CI/badge.svg)](https://github.com/usejuno/juno/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)

A modern, multi-tenant Laravel application with a comprehensive permission system, built with Inertia.js, React, and Tailwind CSS.

## Features

- 🏢 **Multi-tenant Architecture** - Organization-scoped data with team management
- 🔐 **Advanced Permission System** - Fine-grained role-based access control with wildcard support
- 🎨 **Modern UI** - Built with React, Inertia.js, and shadcn/ui components
- 🚀 **RESTful API** - Versioned API (v1) with Laravel Sanctum authentication
- ✅ **Type-Safe** - Enum-based permission helpers for IDE autocomplete
- 🧪 **Well Tested** - Comprehensive test suite with Pest PHP
- 📱 **Responsive Design** - Mobile-first design with Tailwind CSS v4
- 🔄 **Real-time Ready** - SPA architecture with seamless client-side navigation

## Tech Stack

### Backend
- **PHP 8.4+** - Latest PHP features including property hooks
- **Laravel 12.x** - Modern PHP framework with streamlined structure
- **PostgreSQL** - Robust relational database
- **Laravel Sanctum** - API and SPA authentication
- **Laravel Fortify** - Frontend-agnostic authentication backend

### Frontend
- **React 19** - Latest React with concurrent features
- **Inertia.js v2** - Modern monolith with SPA experience
- **TypeScript** - Type-safe JavaScript
- **Tailwind CSS v4** - Utility-first CSS framework
- **shadcn/ui** - Beautiful, accessible component library
- **Laravel Wayfinder** - Type-safe route helpers

### Testing & Quality
- **Pest PHP v4** - Elegant testing framework with browser testing
- **PHPStan** - Static analysis for PHP
- **Laravel Pint** - Opinionated code formatter
- **ESLint 9** - JavaScript linting
- **Prettier** - Code formatting

## Requirements

- PHP 8.4 or higher
- Composer 2.x
- Node.js 20.x or higher
- PostgreSQL 15+ (or MySQL 8.0+)
- NPM or Yarn

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/usejuno/juno.git
   cd juno
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Set up environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database in `.env`**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=juno
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seed the database**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   # or for development with hot reload
   npm run dev
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000` in your browser.

## Development

### Code Style

This project uses Laravel Pint for PHP code formatting:

```bash
# Check code style
vendor/bin/pint --test

# Fix code style
vendor/bin/pint
```

ESLint and Prettier for JavaScript/TypeScript:

```bash
# Check code style
npm run lint

# Fix code style
npm run lint:fix
```

### Static Analysis

Run PHPStan for static analysis:

```bash
vendor/bin/phpstan analyse
```

### Testing

Run the test suite with Pest:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/UserTest.php

# Run with coverage
php artisan test --coverage --min=80

# Run in parallel
php artisan test --parallel
```

### Browser Testing

Pest v4 includes powerful browser testing capabilities:

```bash
# Run browser tests
php artisan test tests/Browser
```

## Permission System

Juno features a comprehensive permission system with the following structure:

### Permission Format

Permissions follow the `{resource}:{action}` format:

- `users:view_any` - View all users
- `roles:create` - Create roles
- `teams:update` - Update teams

### Wildcard Permissions

- `users:*` - All permissions for users resource
- `*` - Global super admin (all permissions)

### Type-Safe Enum Helpers

```php
use App\Enums\Resource;
use App\Enums\Permission;

// Using Resource enum helpers
Resource::USERS->viewAny();      // returns "users:view_any"
Resource::USERS->create();       // returns "users:create"
Resource::USERS->wildcard();     // returns "users:*"

// Using Permission enum helpers
Permission::VIEW_ANY->for(Resource::USERS);  // returns "users:view_any"
Permission::all();                            // returns "*"

// In policies
public function viewAny(User $user): bool
{
    return $this->hasPermission($user, Resource::USERS->viewAny());
}
```

See [PERMISSIONS.md](PERMISSIONS.md) for detailed documentation.

## API Documentation

The application provides a RESTful API at `/api/v1/`:

### Authentication

Use Laravel Sanctum for API authentication:

```bash
# Create a token
POST /api/v1/auth/token
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}

# Use token in requests
GET /api/v1/users
Authorization: Bearer {your-token}
```

### Available Endpoints

- `GET /api/v1/users` - List users
- `POST /api/v1/users` - Create user
- `GET /api/v1/users/{user}` - Get user
- `PUT /api/v1/users/{user}` - Update user
- `DELETE /api/v1/users/{user}` - Delete user

Similar endpoints exist for: `organizations`, `teams`, `roles`, `invitations`, `permissions`

## Project Structure

```
juno/
├── app/
│   ├── Actions/          # Business logic actions
│   ├── Enums/            # Type-safe enumerations
│   ├── Http/
│   │   ├── Controllers/  # Web and API controllers
│   │   ├── Middleware/   # Custom middleware
│   │   └── Requests/     # Form request validation
│   ├── Models/           # Eloquent models
│   ├── Policies/         # Authorization policies
│   └── Services/         # Service layer classes
├── database/
│   ├── factories/        # Model factories
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/   # React components
│   │   ├── Layouts/      # Layout components
│   │   └── Pages/        # Inertia page components
│   └── css/              # Stylesheets
├── routes/
│   ├── api/v1/           # API routes
│   └── web/              # Web routes
└── tests/
    ├── Feature/          # Feature tests
    ├── Unit/             # Unit tests
    └── Browser/          # Browser tests
```

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Write or update tests
5. Run code quality checks:
   ```bash
   vendor/bin/pint
   vendor/bin/phpstan analyse
   php artisan test
   npm run lint
   ```
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## Security

If you discover a security vulnerability, please email security@usejuno.com instead of using the issue tracker. All security vulnerabilities will be promptly addressed.

## License

Juno is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

Built with ❤️ by the Juno team.

### Key Dependencies

- [Laravel](https://laravel.com) - The PHP framework
- [Inertia.js](https://inertiajs.com) - The modern monolith
- [React](https://react.dev) - The JavaScript library
- [Tailwind CSS](https://tailwindcss.com) - The CSS framework
- [shadcn/ui](https://ui.shadcn.com) - Component library
- [Pest PHP](https://pestphp.com) - Testing framework

## Support

- 📧 Email: support@usejuno.com
- 💬 Discord: [Join our community](https://discord.gg/juno)
- 📖 Documentation: [docs.usejuno.com](https://docs.usejuno.com)
- 🐛 Bug Reports: [GitHub Issues](https://github.com/usejuno/juno/issues)

## Roadmap

- [ ] Advanced analytics dashboard
- [ ] Webhook system
- [ ] Advanced audit logging
- [ ] Role templates
- [ ] Export functionality
- [ ] Advanced search and filtering
- [ ] Mobile app (React Native)

---

**Made with Laravel & Inertia.js**
