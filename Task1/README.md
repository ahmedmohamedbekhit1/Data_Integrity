```markdown
# Two-Factor Authentication (2FA) API with JWT

A secure RESTful API implementing Google Authenticator-style 2FA with JWT token authentication.

## Features

- User registration with password hashing
- Two-step login process:
  1. Username/password verification
  2. 2FA code validation
- JWT token generation for authenticated sessions
- Protected endpoints requiring valid JWT
- QR code generation for Google Authenticator setup

## API Endpoints

### 1. User Registration
**POST** `/signup.php`

Request:
```json
{
    "username": "ahmedbekhit",
    "password": "securepassword123"
}
```

Response:
```json
{
    "success": true,
    "message": "Registration successful. Complete 2FA setup on first login."
}
```

### 2. Login (Step 1 - Credentials)
**POST** `/login.php`

Request:
```json
{
    "username": "ahmedbekhit",
    "password": "securepassword123"
}
```

Response (if 2FA not set up):
```json
{
    "success": true,
    "message": "2FA setup required",
    "qr_code": "data:image/png;base64,...",
    "next_step": "verify_2fa"
}
```

Response (if 2FA already set up):
```json
{
    "success": true,
    "message": "2FA verification required",
    "next_step": "verify_2fa"
}
```

### 3. Login (Step 2 - 2FA Verification)
**POST** `/login.php`

Request:
```json
{
    "username": "ahmedbekhit",
    "password": "securepassword123",
    "twofa_code": "123456"
}
```

Response:
```json
{
    "success": true,
    "message": "Login successful",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### 4. Protected Endpoints (Example: Products)
**GET** `/products.php`
- Requires: `Authorization: Bearer <token>`

## Setup Instructions

1. **Database Setup**:
   ```sql
   ALTER TABLE Users ADD COLUMN twofa_secret VARCHAR(255);
   ALTER TABLE Users ADD COLUMN twofa_enabled BOOLEAN DEFAULT TRUE;
   ```

2. **Dependencies**:
   - PHP 7.4+
   - MySQL/MariaDB
   - Required files in `RobThree/` directory

3. **Installation**:
   - Place all PHP files in your web server directory
   - Configure database connection in `db_connection.php`

## Authentication Flow

1. User registers via `/signup.php`
2. On first login:
   - System generates and stores 2FA secret
   - Returns QR code for Google Authenticator setup
3. Subsequent logins require:
   - Valid username/password
   - Current 2FA code from authenticator app
4. Successful login returns JWT token
5. Token is used for accessing protected endpoints

## Error Responses

| Status Code | Description                 |
|-------------|-----------------------------|
| 400         | Missing required fields     |
| 401         | Invalid credentials/code    |
| 409         | Username already exists     |
| 500         | Server/database error       |

## Security Features

- Password hashing with bcrypt
- Time-based one-time passwords (TOTP)
- JWT with expiration
- Prepared SQL statements
- HTTPS recommended for production

## Testing with Postman

1. Import the included Postman collection
2. Test endpoints in this order:
   - Registration
   - First login (get QR code)
   - Second login (with 2FA code)
   - Access protected endpoints

```
