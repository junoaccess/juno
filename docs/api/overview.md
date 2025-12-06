# API Overview

Juno provides a RESTful API for integrating with external applications and services.

## Base URL

All API requests should be made to:

```
https://your-domain.com/api/v1/
```

## Authentication

Juno uses Laravel Sanctum for API authentication.

### Creating a Token

```bash
POST /api/v1/auth/token
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

Response:

```json
{
  "token": "1|laravel_sanctum_token_here",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "first_name": "John",
    "last_name": "Doe"
  }
}
```

### Using the Token

Include the token in the Authorization header:

```bash
GET /api/v1/users
Authorization: Bearer 1|laravel_sanctum_token_here
```

## Response Format

All API responses follow a consistent format:

### Success Response

```json
{
  "data": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

### Collection Response

```json
{
  "data": [
    { "id": 1, "email": "user@example.com" },
    { "id": 2, "email": "another@example.com" }
  ],
  "links": {
    "first": "http://api.example.com/api/v1/users?page=1",
    "last": "http://api.example.com/api/v1/users?page=10",
    "prev": null,
    "next": "http://api.example.com/api/v1/users?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### Error Response

```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

## Endpoints

### Users

- `GET /api/v1/users` - List users
- `POST /api/v1/users` - Create user
- `GET /api/v1/users/{id}` - Get user
- `PUT /api/v1/users/{id}` - Update user
- `DELETE /api/v1/users/{id}` - Delete user

### Organisations

- `GET /api/v1/organisations` - List organisations
- `POST /api/v1/organisations` - Create organisation
- `GET /api/v1/organisations/{id}` - Get organisation
- `PUT /api/v1/organisations/{id}` - Update organisation
- `DELETE /api/v1/organisations/{id}` - Delete organisation

### Teams

- `GET /api/v1/teams` - List teams
- `POST /api/v1/teams` - Create team
- `GET /api/v1/teams/{id}` - Get team
- `PUT /api/v1/teams/{id}` - Update team
- `DELETE /api/v1/teams/{id}` - Delete team

### Roles

- `GET /api/v1/roles` - List roles
- `POST /api/v1/roles` - Create role
- `GET /api/v1/roles/{id}` - Get role
- `PUT /api/v1/roles/{id}` - Update role
- `DELETE /api/v1/roles/{id}` - Delete role

### Permissions

- `GET /api/v1/permissions` - List permissions

### Invitations

- `GET /api/v1/invitations` - List invitations
- `POST /api/v1/invitations` - Create invitation
- `GET /api/v1/invitations/{id}` - Get invitation
- `DELETE /api/v1/invitations/{id}` - Revoke invitation

## Rate Limiting

API requests are rate limited to prevent abuse:

- **Authenticated requests**: 60 requests per minute
- **Unauthenticated requests**: 10 requests per minute

Rate limit headers are included in responses:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640000000
```

## Pagination

Collection endpoints support pagination with the following query parameters:

- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

Example:

```
GET /api/v1/users?page=2&per_page=25
```

## Filtering and Sorting

Use query parameters to filter and sort results:

```
GET /api/v1/users?filter[email]=example.com&sort=-created_at
```

## Next Steps

- View detailed endpoint documentation:
  - [Authentication](/api/authentication)
  - [Users](/api/users)
  - [Organisations](/api/organisations)
  - [Teams](/api/teams)
  - [Roles](/api/roles)
  - [Permissions](/api/permissions)
  - [Invitations](/api/invitations)
