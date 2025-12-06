# Contributing to Juno

First off, thank you for considering contributing to Juno! It's people like you that make Juno such a great tool.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to conduct@junoaccess.site.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps which reproduce the problem**
- **Provide specific examples to demonstrate the steps**
- **Describe the behavior you observed after following the steps**
- **Explain which behavior you expected to see instead and why**
- **Include screenshots and animated GIFs if possible**
- **Include your environment details** (OS, PHP version, Laravel version, etc.)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and explain which behavior you expected to see instead**
- **Explain why this enhancement would be useful**
- **List some other projects where this enhancement exists, if applicable**

### Pull Requests

Please follow these steps to have your contribution considered:

1. **Fork the repository** and create your branch from `main`
2. **Follow the coding standards** (see below)
3. **Write or update tests** for your changes
4. **Ensure all tests pass** locally before pushing
5. **Update documentation** if you're changing functionality
6. **Write a clear commit message**
7. **Submit your pull request**

## Development Setup

### Prerequisites

- PHP 8.4+
- Composer 2.x
- Node.js 20.x+
- PostgreSQL 15+ (or MySQL 8.0+)
- Git

### Setting Up Your Development Environment

1. Fork and clone the repository:
   ```bash
   git clone https://github.com/junoaccess/juno.git
   cd juno
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Set up your environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database in `.env` and run migrations:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. Build assets and start the dev server:
   ```bash
   npm run dev
   php artisan serve
   ```

## Coding Standards

### PHP

We use Laravel Pint for PHP code formatting. Please ensure your code follows our standards:

```bash
# Check code style
vendor/bin/pint --test

# Fix code style automatically
vendor/bin/pint
```

**Key conventions:**
- Use PHP 8.4 features appropriately
- Use type declarations for all parameters and return types
- Use property promotion in constructors
- Use enum helpers instead of raw strings for permissions
- Follow PSR-12 coding standards (enforced by Pint)

### JavaScript/TypeScript

We use ESLint and Prettier for JavaScript/TypeScript formatting:

```bash
# Check code style
npm run lint

# Fix code style automatically
npm run lint:fix
```

**Key conventions:**
- Use TypeScript for all new code
- Use functional components with hooks
- Use named exports over default exports
- Follow React best practices
- Use Tailwind CSS classes, avoid inline styles

### Testing

All code changes must include tests:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/UserTest.php

# Run with coverage (aim for 80%+)
php artisan test --coverage --min=80
```

**Testing guidelines:**
- Write feature tests for user-facing functionality
- Write unit tests for isolated logic
- Use factories for test data
- Mock external services
- Follow the existing test structure

### Static Analysis

Run PHPStan to catch potential bugs:

```bash
vendor/bin/phpstan analyse
```

Fix any errors before submitting your PR.

## Git Workflow

### Branch Naming

Use descriptive branch names:
- `feature/add-user-export` - New features
- `fix/user-permission-check` - Bug fixes
- `docs/update-readme` - Documentation updates
- `refactor/extract-service` - Code refactoring
- `test/add-user-tests` - Test additions

### Commit Messages

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
type(scope): subject

body

footer
```

**Types:**
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Examples:**
```
feat(users): add user export functionality

Implements CSV and JSON export for users with filters.
Includes tests and documentation updates.

Closes #123
```

```
fix(permissions): correct wildcard permission matching

The wildcard matching was not properly handling resource-level
wildcards. This fix ensures users:* matches all user permissions.
```

### Pull Request Process

1. **Update documentation** - Ensure the README and any relevant docs are updated
2. **Add tests** - All new code should have corresponding tests
3. **Run checks locally** - Ensure all tests and checks pass:
   ```bash
   vendor/bin/pint
   vendor/bin/phpstan analyse
   php artisan test
   npm run lint
   npm run build
   ```
4. **Update CHANGELOG** - Add your changes to the Unreleased section
5. **Create PR** - Use the PR template and fill out all sections
6. **Address feedback** - Be responsive to review comments
7. **Rebase if needed** - Keep your branch up to date with main

### PR Title Format

Follow Conventional Commits in PR titles too:
- `feat: add user export functionality`
- `fix: correct wildcard permission matching`
- `docs: update installation instructions`

## Project Architecture

### Permission System

When working with permissions:
- Use the `{resource}:{action}` format (e.g., `users:view_any`)
- Use enum helpers: `Resource::USERS->viewAny()` instead of strings
- Support wildcards: `users:*` and `*`
- Update tests when adding new permissions
- Document new permissions in PERMISSIONS.md

### Controllers

- Keep controllers thin - move logic to services
- Use Form Requests for validation
- Use API Resources for JSON responses
- Return Inertia responses for web controllers
- Return JSON for API controllers

### Services

- Place complex business logic in service classes
- Make services reusable between web and API
- Use dependency injection
- Add type hints to all methods

### Models

- Use Eloquent relationships with type hints
- Define casts in the `casts()` method
- Use factories for test data
- Keep models focused on data access

### Frontend

- Place pages in `resources/js/Pages`
- Reuse components from `resources/js/Components`
- Use shadcn/ui components when possible
- Use Wayfinder for type-safe routes
- Use Inertia's Form component for forms

## Questions?

Don't hesitate to ask questions:
- Open a GitHub Discussion
- Join our Discord server
- Email us at support@junoaccess.site

## Recognition

Contributors will be recognized in:
- The project README
- Release notes
- Our contributors page

Thank you for contributing! 🎉
