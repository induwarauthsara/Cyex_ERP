# Admin Access Guide

## Overview

The Srijaya ERP Mobile POS API has two user roles:
- **Employee**: Regular cashier/staff with access to POS operations
- **Admin**: Full administrative access including reports, salary, suppliers, and stock management

## Admin-Only Endpoints

The following endpoints require admin authentication:

### Reports
- `GET /api/v1/reports/daily.php` - Daily business report
- `GET /api/v1/reports/monthly.php` - Monthly business report

### Salary Management
- `POST /api/v1/salary/pay.php` - Process salary payment
- `GET /api/v1/salary/history.php` - View salary history

### Supplier Management
- `GET /api/v1/suppliers/list.php` - List all suppliers
- `GET /api/v1/suppliers/details.php` - Get supplier details

### Stock Management
- `GET /api/v1/stock/summary.php` - Get inventory summary

## How to Get Admin Token

### Step 1: Login with Admin Credentials

Use the same login endpoint as employees, but with admin credentials:

```bash
POST /api/v1/auth/login.php
Content-Type: application/json

{
  "username": "admin",
  "password": "your_admin_password"
}
```

### Step 2: Check the Response

The response will include the role:

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 1,
      "name": "admin",
      "role": "Admin"
    },
    "expires_in": 86400
  }
}
```

**Important:** Note the `"role": "Admin"` in the response. This confirms you have admin access.

### Step 3: Use the Token

Include the admin token in the Authorization header:

```bash
GET /api/v1/reports/daily.php?date=2025-10-17
Authorization: Bearer YOUR_ADMIN_TOKEN
```

## Error Handling

### 403 Forbidden - Wrong Token Type

If you try to access admin endpoints with an employee token:

```json
{
  "success": false,
  "message": "Admin access required",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

**HTTP Status:** `403 Forbidden`

**Solution:** Login with admin credentials and use the admin token.

### 401 Unauthorized - Invalid Token

If your token is invalid or expired:

```json
{
  "success": false,
  "message": "Invalid or expired token",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

**HTTP Status:** `401 Unauthorized`

**Solution:** Login again to get a fresh token.

## Database Information

### User Roles in Database

The `employees` table has a `role` column with two possible values:
- `'Admin'` - Full administrative access
- `'Employee'` - Regular POS access

### Creating Admin Users

To create a new admin user in the database:

```sql
INSERT INTO employees (emp_name, role, password, status)
VALUES ('newadmin', 'Admin', 'secure_password', 1);
```

Or update an existing user:

```sql
UPDATE employees 
SET role = 'Admin' 
WHERE employ_id = 5;
```

## Postman Collection

The Postman collection includes two login requests:

1. **Login (Employee)** - For regular POS operations
   - Save token in `{{token}}` variable
   
2. **Login (Admin)** - For admin operations
   - Save token in `{{admin_token}}` variable

Admin-only endpoints use `{{admin_token}}` in their Authorization headers.

## Security Best Practices

1. **Never share admin credentials** with regular employees
2. **Use strong passwords** for admin accounts
3. **Rotate admin passwords** regularly
4. **Monitor admin access** through action logs
5. **Limit admin accounts** to trusted personnel only
6. **Use HTTPS** in production to protect tokens in transit

## Troubleshooting

### Q: I'm getting "Admin access required" error

**A:** You're using an employee token. Login with admin credentials.

### Q: How do I know if I'm logged in as admin?

**A:** Check the login response - it will have `"role": "Admin"`.

### Q: Can I change a user from Employee to Admin?

**A:** Yes, update the `role` field in the `employees` table to `'Admin'`.

### Q: Do admin tokens expire?

**A:** Yes, all tokens expire after 24 hours. Login again to get a fresh token.

### Q: Can I use the same token for both employee and admin endpoints?

**A:** If you login as admin, your token works for ALL endpoints (both employee and admin). If you login as employee, your token only works for employee endpoints.

## Contact

For security concerns or admin access requests, contact:
- **Email:** support@srijayaprint.com
- **Phone:** 0714730996

---

Last Updated: October 17, 2025
