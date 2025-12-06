# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Initial release
- Multi-tenant architecture with organization and team management
- Comprehensive role-based permission system with wildcard support
- Type-safe enum helpers for permissions
- RESTful API v1 with Laravel Sanctum authentication
- Inertia.js + React frontend with shadcn/ui components
- User, Team, Role, Invitation, Permission, and Organization management
- Comprehensive test suite with Pest PHP
- CI/CD pipeline with GitHub Actions
- Browser testing capabilities with Pest v4
- Static analysis with PHPStan
- Code formatting with Laravel Pint
- API Resources for JSON transformation
- Form Request validation
- Policy-based authorization
- Service layer for business logic
- Factory and seeder infrastructure
- PostgreSQL database support
- Laravel Wayfinder for type-safe routing

### Security

- Laravel Sanctum for API and SPA authentication
- Policy-based authorization on all resources
- Fine-grained permission checking
- Protected API routes with auth middleware

## [1.0.0] - YYYY-MM-DD

Initial release.

[Unreleased]: https://github.com/usejuno/juno/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/usejuno/juno/releases/tag/v1.0.0
