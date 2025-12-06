# Architecture Overview

Juno follows a clean, layered architecture that separates concerns and promotes maintainability.

## High-Level Architecture

```
┌─────────────────────────────────────────────────┐
│           Frontend (React + Inertia)             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│  │  Pages   │  │Components│  │ Layouts  │      │
│  └──────────┘  └──────────┘  └──────────┘      │
└─────────────────────────────────────────────────┘
                      ↕ Inertia.js
┌─────────────────────────────────────────────────┐
│              Backend (Laravel)                   │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│  │Controller│→ │ Service  │→ │  Model   │      │
│  └──────────┘  └──────────┘  └──────────┘      │
│       ↓              ↓              ↓           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│  │ Request  │  │  Action  │  │ Observer │      │
│  └──────────┘  └──────────┘  └──────────┘      │
└─────────────────────────────────────────────────┘
                      ↕
┌─────────────────────────────────────────────────┐
│           Database (PostgreSQL)                  │
└─────────────────────────────────────────────────┘
```

## Backend Architecture

### Directory Structure

```
app/
├── Actions/          # Domain-specific actions
├── Enums/           # Type-safe enumerations
├── Http/
│   ├── Controllers/ # Request handlers
│   ├── Middleware/  # HTTP middleware
│   └── Requests/    # Form validation
├── Models/          # Eloquent models
├── Observers/       # Model observers
├── Policies/        # Authorization policies
└── Services/        # Business logic
```

### Key Components

**Controllers**
Handle HTTP requests and return Inertia responses. Controllers delegate business logic to services and use policies for authorization.

**Services**
Contain business logic and orchestrate models, actions, and external services. Services return data to controllers.

**Models**
Eloquent models represent database tables and define relationships. Models use observers for lifecycle hooks.

**Policies**
Define authorization rules for each model. Policies integrate with the permission system.

**Requests**
Form Request classes handle validation logic, keeping controllers clean.

**Actions**
Encapsulate specific domain operations (e.g., CreateUser, SendInvitation) following the single responsibility principle.

## Frontend Architecture

### Directory Structure

```
resources/js/
├── components/      # Reusable components
│   ├── ui/         # shadcn/ui components
│   └── ...         # Custom components
├── layouts/        # Page layouts
├── pages/          # Inertia pages
└── types/          # TypeScript types
```

### Key Concepts

**Pages**
Inertia pages correspond to routes and receive data as props from Laravel controllers.

**Components**
Reusable React components following atomic design principles. Uses shadcn/ui for base components.

**Layouts**
Wrap pages with common UI elements like navigation, headers, and footers.

**Type Safety**
Laravel Wayfinder generates TypeScript types for routes, ensuring type safety between backend and frontend.

## Data Flow

### Request Lifecycle

1. User interaction triggers a client-side navigation
2. Inertia sends XHR request to Laravel
3. Laravel route matches and calls controller
4. Controller authorizes request via policy
5. Controller calls service for business logic
6. Service interacts with models and actions
7. Controller returns Inertia response with props
8. Inertia updates React component with new props
9. React re-renders page with new data

### Multi-Tenant Scoping

All queries are automatically scoped to the current organisation using global scopes:

```php
// Models use the UserOrganizationScope
class Team extends Model
{
    use ScopedBy;

    protected $attributes = [
        ScopedBy::class => [UserOrganizationScope::class]
    ];
}
```

This ensures data isolation without explicit filtering in queries.

## API Architecture

The RESTful API follows the same layered architecture but returns JSON responses instead of Inertia responses:

```
API Request → Controller → Service → Model → Database
            ← Resource  ← Service ← Model ←
```

**API Resources**
Transform models into JSON responses with consistent formatting.

**Authentication**
Uses Laravel Sanctum for token-based authentication.

**Versioning**
API routes are versioned (`/api/v1/`) to support backward compatibility.

## Testing Architecture

```
tests/
├── Feature/         # Integration tests
├── Unit/           # Unit tests
└── Browser/        # Browser tests (Pest v4)
```

**Feature Tests**
Test complete request-response cycles including database interactions.

**Unit Tests**
Test individual classes and methods in isolation.

**Browser Tests**
Test user interactions using real browsers with Pest v4.

## Next Steps

- Learn about [Backend Structure](/guide/backend-structure) in detail
- Understand [Frontend Structure](/guide/frontend-structure)
- Explore [Organisations](/guide/organisations) and access control
