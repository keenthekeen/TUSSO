# Triamudom Single Sign-On Service Provider
Centralized Authentication System for Triamudom Personnel

The free-time project, based on Laravel 5 and Materialize

Development Status: Release Candidate

Please read the contribution guide before contributing.


### Features
- [x] LDAP Integration
- [x] Nginx Auth module
- [x] Simple authentication
- [x] OAuth

##### Simple Authentication
1. Application send user to /simple_auth?application=APP_ID
2. If user hasn't logged in, show login dialog.
3. Send user back to application with "userinfo" app-secret-encrypted-JSON