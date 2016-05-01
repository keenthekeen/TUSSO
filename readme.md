# Triamudom Central Authentication Service

The centralized authentication system for Triamudom personnel, based on Laravel 5.2.

Please read the contribution guide before contributing any changes.

## Features
- Integrated with directory server using LDAP
- Works with Nginx auth_module
- Implemented OpenID Connect implicit, authorization code, and hybrid flow as provider (OP)
- Issues Resource Server (TURS) access tokens (as JWT, signed using RSA)
- Also works during directory server shortage, using local database
- Allow new student to register for temporary account by working with TUENT applicant database
- Allow applications to search for user id using name and vice-versa
- Implemented OpenID Connect Discovery (Not fully comply with the specification yet)
- Implemented single sign-out. (based on OpenID Connect Session Management, but not comply with)

### Specification/Standard Compliance
- OpenID Connect Core 1.0
- OpenID Connect Discovery 1.0 (Now, partially)
- JSON Web Token (JWT) (draft)
- The OAuth 2.0 Authorization Framework: Bearer Token Usage
- HTTP Authentication: Basic and Digest Access Authentication (RFC2617)
- [ ] OAuth 2.0 Threat Model and Security Considerations