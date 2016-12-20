# Triamudom Central Authentication Service

The centralized authentication system for Triamudom personnel, based on Laravel 5.2.

**This version of TUSSO utilizes existing directory server** ("TUSSO as Extension")

Please read the contribution guide before contributing any changes.

## Features
- Integrated with directory server using LDAP
- Works with Nginx auth_module
- Implemented OpenID Connect implicit, authorization code, and hybrid flow as provider (OP)
- Issues Resource Server (TURS) access tokens (as JWT, signed using RSA)
- Also works during directory server shortage, using local database
- Allow new student to register for temporary account by working with registration dept. student database (Disabled)
- Allow applications to search for user id using name and vice-versa
- Implemented OpenID Connect Discovery (Not fully comply with the specification)
- Implemented single sign-out. (based on OpenID Connect Session Management, but not comply with)
- Utilize Credential Management API, making sign-in more easy for Chrome 51+ users.
- Can be used as Captive Portal Auth for Unifi AP (Disabled)

### Specification/Standard Compliance
- OpenID Connect Core 1.0
- OpenID Connect Discovery 1.0 (Now, partially)
- The OAuth 2.0 Authorization Framework: Bearer Token Usage
- OAuth 2.0 Threat Model and Security Considerations (RFC6819)
- HTTP Authentication: Basic and Digest Access Authentication (RFC2617)
- JSON Web Token (JWT) (draft)
- Credential Management Level 1 (W3C Working Draft, 25 April 2016)