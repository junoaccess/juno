# Getting Started

This guide will walk you through setting up Juno for local development.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.4 or higher** with the following extensions:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PCRE
  - PDO
  - Tokenizer
  - XML
- **Composer 2.x**
- **Node.js 20.x or higher**
- **npm or pnpm**
- **PostgreSQL 15+** (or MySQL 8.0+)
- **Git**

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/usejuno/juno.git
cd juno
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
# or
pnpm install
```

### 4. Configure Environment

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database

Edit your `.env` file and set your database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=juno
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

For PostgreSQL, create the database:

```bash
createdb juno
```

### 6. Run Migrations

Run the database migrations to create all necessary tables:

```bash
php artisan migrate
```

### 7. Seed the Database

Populate the database with sample data:

```bash
php artisan db:seed
```

This will create:
- Sample permissions for all resources
- A test user (test@example.com / password)
- Sample organisations and teams

### 8. Build Frontend Assets

For development with hot module replacement:

```bash
npm run dev
```

For production build:

```bash
npm run build
```

### 9. Start the Development Server

Juno provides a convenient development command that starts all necessary services:

```bash
composer run dev
```

This starts:
- PHP development server (http://localhost:8000)
- Queue worker
- Log viewer (Laravel Pail)
- Vite development server with HMR

Alternatively, start services individually:

```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend
npm run dev

# Terminal 3 - Queue worker (optional)
php artisan queue:listen
```

## Accessing the Application

Once the development servers are running:

1. Visit http://localhost:8000 in your browser
2. Login with the test credentials:
   - Email: `test@example.com`
   - Password: `password`

## Creating an Admin User

To create an admin user with full system access:

```bash
php artisan admin:create \
  --email=admin@example.com \
  --password=SecurePassword123 \
  --first-name=Admin \
  --last-name=User \
  --organisation="My Company" \
  --force
```

## Next Steps

Now that you have Juno running locally, you can:

- Explore the [Architecture](/guide/architecture) to understand how Juno is structured
- Learn about [Organisations](/guide/organisations) and access control
- Read the [API Reference](/api/overview) to integrate with Juno
- Check out the [Testing Guide](/guide/testing) to run tests

## Troubleshooting

### Port Already in Use

If port 8000 is already in use, specify a different port:

```bash
php artisan serve --port=8080
```

### Database Connection Errors

Verify your database credentials in `.env` and ensure the database service is running:

```bash
# PostgreSQL
pg_isready

# Check if database exists
psql -l | grep juno
```

### Asset Compilation Errors

If you encounter errors during `npm run build`, try:

```bash
# Clean and reinstall node modules
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf node_modules/.vite
```

### Permission Errors

On Linux/macOS, ensure storage and cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```

## Getting Help

If you encounter issues not covered here:

- Check the [GitHub Issues](https://github.com/usejuno/juno/issues)
- Ask in [GitHub Discussions](https://github.com/usejuno/juno/discussions)
- Review the [SUPPORT.md](https://github.com/usejuno/juno/blob/main/SUPPORT.md) guide
