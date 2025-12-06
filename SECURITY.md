# Security Policy

## Supported Versions

We release patches for security vulnerabilities for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

If you discover a security vulnerability within Juno, please send an email to security@junoaccess.site. All security vulnerabilities will be promptly addressed.

### What to Include

Please include the following information in your report:

- Type of issue (e.g., buffer overflow, SQL injection, cross-site scripting, etc.)
- Full paths of source file(s) related to the manifestation of the issue
- The location of the affected source code (tag/branch/commit or direct URL)
- Any special configuration required to reproduce the issue
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code (if possible)
- Impact of the issue, including how an attacker might exploit it

### What to Expect

- We will acknowledge receipt of your vulnerability report within 48 hours
- We will provide a detailed response within 7 days indicating the next steps
- We will work with you to understand the scope and severity of the issue
- We will notify you when the vulnerability has been fixed
- We will credit you in the security advisory (unless you prefer to remain anonymous)

## Security Best Practices

When using Juno, we recommend:

1. **Keep Dependencies Updated** - Regularly update your dependencies using `composer update` and `npm update`
2. **Use Environment Variables** - Never commit sensitive data like API keys or passwords
3. **Enable HTTPS** - Always use HTTPS in production
4. **Configure CORS Properly** - Only allow trusted origins
5. **Use Strong Passwords** - Enforce strong password policies
6. **Enable Two-Factor Authentication** - Use 2FA for sensitive accounts
7. **Regular Backups** - Maintain regular database and file backups
8. **Monitor Logs** - Regularly review application and security logs
9. **Rate Limiting** - Implement rate limiting on API endpoints
10. **Security Headers** - Configure proper security headers (CSP, HSTS, etc.)

## Known Security Considerations

### API Authentication

- Sanctum tokens should be treated as passwords
- Token rotation is recommended for sensitive operations
- Implement token expiration for enhanced security

### Permission System

- Wildcard permissions (`*`) should only be granted to super administrators
- Resource-level wildcards (`users:*`) should be carefully audited
- Regularly review role-permission assignments

### File Uploads

- This application does not handle file uploads by default
- If you add file upload functionality, ensure proper validation and storage

## Security Updates

Security updates will be released as soon as possible after a vulnerability is confirmed. Please keep your installation up to date.

Subscribe to security announcements:
- Watch this repository for releases
- Follow us on Twitter: [@usejuno](https://twitter.com/usejuno)
- Join our Discord for real-time updates

## Third-Party Dependencies

We regularly monitor our dependencies for known vulnerabilities. If you discover a vulnerability in a third-party dependency, please report it to us as well as the maintainers of that package.

## Bug Bounty Program

We currently do not have a formal bug bounty program, but we appreciate and acknowledge all security researchers who help us keep Juno secure.

## Contact

Security Team: security@junoaccess.site

For general support: support@junoaccess.site

Thank you for helping keep Juno and its users safe!
