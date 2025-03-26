# Two-Factor Authentication (2FA) API Documentation

## Overview
This API implements a secure authentication flow with:
1. User registration
2. Two-step login (credentials + 2FA code)
3. JWT-protected endpoints

## Endpoints

### 1. User Registration
**POST** `/signup.php`

#### Request:
```json
{
    "username": "ahmedbekhit",
    "password": "securepassword123"
}
```

#### Response:
```json
{
    "success": true,
    "message": "Registration successful. Complete 2FA setup on first login."
}
```

### 2. Login Flow

#### First Step - Credentials Verification
**POST** `/login.php`
```json
{
    "username": "ahmedbekhit",
    "password": "securepassword123"
}
```

##### Possible Responses:
1. If 2FA not set up:
```json
{
    "success": true,
    "message": "2FA setup required",
    "qr_code": "data:image/png;base64,...",
    "next_step": "verify_2fa"
}
```

2. If 2FA already set up:
```json
{
    "success": true,
    "message": "2FA verification required",
    "next_step": "verify_2fa"
}
```

#### Second Step - 2FA Verification
**POST** `/login.php`
```json
{
    "username": "ahmedbekhit",
    "password": "securepassword123",
    "twofa_code": "123456"
}
```

##### Successful Response:
```json
{
    "success": true,
    "message": "Login successful",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### 3. Protected Endpoints (Example: Products)
**GET** `/products.php`
```
Authorization: Bearer <your_jwt_token>
```

## Postman Testing Guide

### Test 1: Registration
1. Create new POST request to `/signup.php`
2. Set body to raw JSON
3. Enter username and password
4. Send request - should get success message

### Test 2: First Login (2FA Setup)
1. Create POST request to `/login.php`
2. Use same credentials
3. First response will contain QR code for 2FA setup

### Test 3: Verify 2FA Code
1. Scan QR code with Google Authenticator
2. Add the code to your login request:
```json
{
    "username": "ahmedbekhit",
    "password": "securepassword123",
    "twofa_code": "123456"
}
```
3. You'll receive JWT token on success

### Test 4: Access Protected Endpoint
1. Create GET request to `/products.php`
2. Add Authorization header:
```
Authorization: Bearer <your_token>
```
3. Should receive product data

## Error Responses

| Status Code | Error Message | Description |
|-------------|---------------|-------------|
| 400 | Username and password are required | Missing credentials |
| 401 | Invalid credentials | Wrong username/password |
| 401 | Invalid 2FA code | Wrong verification code |
| 409 | Username already exists | Duplicate registration |
| 500 | Database error | Server-side issue |

## Flow Diagram
1. Register → Login → (Setup 2FA if first time) → Verify 2FA → Get Token → Access Protected Endpoints

## Security Notes
- Always use HTTPS
- Store passwords hashed (bcrypt)
- JWT tokens expire after 1 hour
- 2FA codes are time-based (30s validity)
