---
layout: home

hero:
  name: Juno
  text: Access Management for Modern Applications
  tagline: Opinionated, multi-tenant access management system built with Laravel, Inertia.js, and React
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: View on GitHub
      link: https://github.com/usejuno/juno

features:
  - icon: 🏢
    title: Multi-Tenant Architecture
    details: Built-in support for multiple organisations with team management and isolated data scoping
  - icon: 🔐
    title: Advanced Permissions
    details: Fine-grained role-based access control with wildcard support and type-safe enum helpers
  - icon: 🎨
    title: Modern UI
    details: Beautiful, responsive interface built with React, Inertia.js, Tailwind CSS, and shadcn/ui
  - icon: 🚀
    title: RESTful API
    details: Versioned API with Laravel Sanctum authentication for seamless integration
  - icon: ✅
    title: Type-Safe
    details: Full TypeScript support with Laravel Wayfinder for type-safe routing
  - icon: 🧪
    title: Well Tested
    details: Comprehensive test suite with Pest PHP including browser testing capabilities
---

## Quick Start

Install Juno and get started in minutes:

```bash
# Clone the repository
git clone https://github.com/usejuno/juno.git
cd juno

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Start development servers
composer run dev
```

## What is Juno?

Juno is a modern, opinionated access management system designed for multi-tenant applications. It provides a complete foundation for building applications that require:

- Multiple organisations with isolated data
- Team-based collaboration
- Fine-grained role and permission management
- User invitation and onboarding flows
- RESTful API for integration
- Modern, responsive user interface

Built on Laravel 12 and React 19, Juno leverages the best tools in the modern PHP and JavaScript ecosystems to deliver a robust, maintainable foundation for your application.

## Core Features

### Multi-Tenant by Design

Every resource in Juno is scoped to an organisation, ensuring complete data isolation. Users can belong to multiple organisations and switch between them seamlessly.

### Flexible Permission System

Define permissions using a simple `resource:action` format with wildcard support. Use type-safe enum helpers for IDE autocomplete and refactoring support.

### Modern Development Experience

Enjoy a delightful development experience with hot module replacement, TypeScript support, comprehensive testing tools, and opinionated conventions that keep your code clean and maintainable.

## Technology Stack

**Backend**
- Laravel 12 with PHP 8.4+
- PostgreSQL database
- Laravel Sanctum for authentication
- Comprehensive test suite with Pest PHP

**Frontend**
- React 19 with TypeScript
- Inertia.js for seamless SPA experience
- Tailwind CSS v4 for styling
- shadcn/ui component library
- Laravel Wayfinder for type-safe routing

## Community

- [GitHub Discussions](https://github.com/usejuno/juno/discussions) - Ask questions and share ideas
- [Issue Tracker](https://github.com/usejuno/juno/issues) - Report bugs and request features
- [Contributing Guide](/guide/contributing) - Learn how to contribute

## License

Juno is open-source software licensed under the [MIT license](https://github.com/usejuno/juno/blob/main/LICENSE).
