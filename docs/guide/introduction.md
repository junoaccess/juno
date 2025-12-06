# Introduction

## What is Juno?

Juno is a modern, opinionated access management system built for multi-tenant applications. It provides a complete foundation for applications that need organisation-scoped data, role-based permissions, team collaboration, and user management.

## Why Juno?

Building access management systems from scratch is time-consuming and error-prone. Juno provides a battle-tested foundation that handles the complexity of multi-tenancy, permissions, and user management, allowing you to focus on your unique business logic.

### Key Benefits

**Multi-Tenant Architecture**
Every resource in Juno is automatically scoped to an organisation. Users can belong to multiple organisations and switch between them seamlessly, with complete data isolation guaranteed.

**Type-Safe Permissions**
Define permissions using enum helpers for IDE autocomplete and refactoring support. The system supports wildcard permissions and hierarchical permission structures.

**Modern Tech Stack**
Built on Laravel 12 and React 19, Juno leverages cutting-edge features and best practices from both ecosystems.

**Production Ready**
Comprehensive test coverage, static analysis, and CI/CD pipelines ensure Juno is reliable and maintainable.

## When to Use Juno

Juno is ideal for applications that require:

- Multiple organisations with isolated data
- Team-based collaboration within organisations
- Fine-grained role and permission management
- User invitation and onboarding workflows
- RESTful API for third-party integrations
- Modern, responsive user interface

## When Not to Use Juno

Juno may not be the right choice if:

- You need a simple, single-tenant application
- You require extensive customisation of the authentication flow
- Your permission requirements are extremely complex or hierarchical beyond what Juno provides
- You prefer a different frontend framework

## Core Concepts

### Organisations

Organisations are the top-level container for all data in Juno. Every user, team, role, and permission belongs to an organisation. Users can belong to multiple organisations and switch between them.

### Teams

Teams provide a way to group users within an organisation for collaboration and permission scoping. A user can belong to multiple teams within an organisation.

### Roles & Permissions

Roles define what users can do within an organisation. Each role has a set of permissions that follow the `resource:action` format (e.g., `users:create`, `teams:delete`). Permissions support wildcards for flexible access control.

### Invitations

The invitation system allows organisations to invite new users via email. Invitations can be configured with specific roles and can be single-use or revoked.

## Getting Help

- Read the [Getting Started Guide](/guide/getting-started) for installation instructions
- Browse the [API Reference](/api/overview) for integration details
- Join our [GitHub Discussions](https://github.com/usejuno/juno/discussions) for questions
- Report bugs in our [Issue Tracker](https://github.com/usejuno/juno/issues)

## Next Steps

Ready to get started? Head over to the [Getting Started Guide](/guide/getting-started) to install Juno and build your first application.
