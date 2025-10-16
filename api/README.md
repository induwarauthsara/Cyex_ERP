# Srijaya ERP - Mobile POS API

RESTful API endpoints for Srijaya ERP Mobile POS Application.

## 📁 Directory Structure

```
api/
├── .htaccess                    # Apache configuration for clean URLs and security
├── API_DOCUMENTATION.md         # Comprehensive API documentation
├── README.md                    # This file
├── v1/                          # API Version 1
│   ├── config.php              # API configuration and CORS settings
│   ├── ApiResponse.php         # Standardized response handler
│   ├── ApiAuth.php             # JWT authentication helper
│   ├── auth/                   # Authentication endpoints
│   │   ├── login.php           # User login
│   │   ├── logout.php          # User logout
│   │   └── verify.php          # Token verification
│   ├── products/               # Product management
│   │   ├── search.php          # Search products
│   │   ├── list.php            # List products (paginated)
│   │   └── details.php         # Product details
│   ├── customers/              # Customer management
│   │   ├── search.php          # Search customers
│   │   ├── details.php         # Customer details
│   │   └── add.php             # Add new customer
│   ├── invoices/               # Invoice management
│   │   ├── submit.php          # Submit new invoice
│   │   └── list.php            # List invoices (paginated)
│   ├── attendance/             # Employee attendance
│   │   ├── clock.php           # Clock in/out
│   │   └── status.php          # Get attendance status
│   ├── one_time_products/      # Custom/non-inventory products
│   │   ├── add.php             # Create one-time product
│   │   ├── list.php            # List one-time products
│   │   └── update_status.php   # Update product status
│   ├── held_invoices/          # Held invoice management
│   │   ├── hold.php            # Hold current invoice
│   │   ├── list.php            # List held invoices
│   │   ├── resume.php          # Resume held invoice
│   │   └── delete.php          # Cancel held invoice
│   ├── reports/                # Business reports (Admin only)
│   │   ├── daily.php           # Daily business report
│   │   └── monthly.php         # Monthly business report
│   ├── salary/                 # Salary management (Admin only)
│   │   ├── pay.php             # Process salary payment
│   │   └── history.php         # Salary payment history
│   ├── pettycash/              # Petty cash tracking
│   │   └── list.php            # List petty cash transactions
│   ├── suppliers/              # Supplier management (Admin only)
│   │   ├── list.php            # List suppliers
│   │   └── details.php         # Supplier details & statistics
│   └── stock/                  # Stock/inventory management (Admin only)
│       └── summary.php         # Stock summary with alerts
```

## 🚀 Quick Start

### 1. Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- Composer (optional, for future dependencies)

### 2. Configuration

**Step 1:** Update database connection in `inc/config.php` (root directory)

**Step 2:** Update JWT secret in `api/v1/config.php`
```php
define('JWT_SECRET', 'your-secure-random-string-here');
```

**Step 3:** Enable mod_rewrite in Apache
```bash
# Enable rewrite module
sudo a2enmod rewrite

# Restart Apache
sudo service apache2 restart
```

### 3. Test the API

**Using cURL:**
```bash
# Login
curl -X POST https://yourdomain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"yourpassword"}'

# Search Products (with token)
curl -X GET "https://yourdomain.com/api/v1/products/search?q=printing" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Using Browser:**
```
GET https://yourdomain.com/api/v1/auth/verify
```

## 📖 Documentation

See **[API_DOCUMENTATION.md](./API_DOCUMENTATION.md)** for:
- Complete endpoint reference
- Request/response examples
- Authentication guide
- Error handling
- Code examples (JavaScript, PHP)
- Security best practices

## 🔐 Authentication

All endpoints (except `/auth/login`) require JWT token authentication.

**Include token in requests:**
```http
Authorization: Bearer YOUR_JWT_TOKEN
```

**Token expiration:** 24 hours

## 📊 API Versioning

Current version: **v1**

Base URL: `https://yourdomain.com/api/v1`

Future versions will be accessible via `/api/v2`, `/api/v3`, etc.

## 🛡️ Security Features

✅ JWT token-based authentication  
✅ Prepared SQL statements (SQL injection prevention)  
✅ Input validation and sanitization  
✅ CORS configuration  
✅ Rate limiting (ready for production)  
✅ HTTPS enforcement (production)  
✅ Security headers (XSS, clickjacking protection)  

## 📱 Mobile App Integration

This API is designed for the **Srijaya Mobile POS** application with Glass UI design.

### Key Features Supported

- ✅ User authentication
- ✅ Product search and selection
- ✅ Customer management
- ✅ Invoice processing
- ✅ Multiple payment methods
- ✅ Cashier attendance tracking
- ✅ Invoice history with filters
- ✅ Held invoice management (hold & resume)
- ✅ One-time custom products
- ✅ Business reports (daily/monthly)
- ✅ Salary payment processing
- ✅ Petty cash tracking
- ✅ Supplier management
- ✅ Stock/inventory management with alerts

## 🧪 Testing

### Local Testing (XAMPP)
```
http://localhost/Srijaya/api/v1/auth/login
```

### Production Testing
```
https://yourdomain.com/api/v1/auth/login
```

### Test Credentials (Development Only)
- **Admin:** admin / your_admin_password
- **Employee:** cashier01 / your_employee_password

**⚠️ Change default passwords in production!**

## ⚙️ Configuration Options

### Debug Mode
In `api/v1/config.php`:
```php
define('API_DEBUG', false); // Set to false in production
```

### CORS Settings
In `api/v1/config.php`:
```php
header("Access-Control-Allow-Origin: https://yourmobileapp.com");
```

### Rate Limiting
Currently disabled. To enable, implement rate limiting in `config.php`.

## 🐛 Error Handling

All errors return consistent JSON format:
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": "Field-specific error"
  },
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

## 📋 Response Format

All successful responses:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { /* response data */ },
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

## 🔄 Updates & Maintenance

### Adding New Endpoints
1. Create new PHP file in appropriate directory
2. Include `config.php` and `ApiAuth.php`
3. Use `ApiResponse` class for responses
4. Add authentication check with `ApiAuth::requireAuth()`
5. Update API documentation

### Version Migration
When creating v2:
1. Copy `v1` directory to `v2`
2. Update `config.php` version constant
3. Make necessary changes
4. Maintain v1 for backward compatibility

## 📞 Support

**Business:** Srijaya Print Shop  
**Email:** support@srijayaprint.com  
**Phone:** 0714730996  
**Address:** FF26, Megacity, Athurugiriya  

## 📝 License

Proprietary - Srijaya Print Shop  
© 2024-2025 All Rights Reserved

---

## 📈 API Statistics

**Total API Endpoints:** 29 endpoints  
**API Categories:** 12 categories  
**API Version:** v1.1  
**Authentication:** JWT-based  
**Response Format:** JSON  

## 🎯 Roadmap

### Version 1.1 (Current) ✅
- ✅ Authentication system
- ✅ Product management
- ✅ Customer management
- ✅ Invoice processing
- ✅ Attendance tracking
- ✅ Held invoice management
- ✅ One-time product APIs
- ✅ Business reports (daily/monthly)
- ✅ Salary payment APIs
- ✅ Petty cash tracking
- ✅ Supplier management APIs
- ✅ Stock/inventory management

### Version 1.2 (Planned)
- ⏳ Payment method APIs
- ⏳ Invoice editing/void APIs
- ⏳ Rate limiting implementation
- ⏳ API usage analytics
- ⏳ Advanced search filters
- ⏳ Batch operations

### Version 2.0 (Future)
- 📋 Webhook support
- 📋 Real-time notifications (WebSocket)
- 📋 GraphQL support
- 📋 Advanced analytics APIs
- 📋 Multi-language support
- 📋 API marketplace integration

---

**For detailed API documentation, see [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)**
