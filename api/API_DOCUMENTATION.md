# Srijaya ERP Mobile POS API Documentation

**Version:** 1.0  
**Base URL:** `https://yourdomain.com/api/v1`  
**Last Updated:** October 17, 2025

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Standard Response Format](#standard-response-format)
4. [Error Handling](#error-handling)
5. [API Endpoints](#api-endpoints)
   - [Authentication](#authentication-endpoints)
   - [Products](#product-endpoints)
   - [Customers](#customer-endpoints)
   - [Invoices](#invoice-endpoints)
   - [Attendance](#attendance-endpoints)
   - [One-Time Products](#one-time-product-endpoints)
   - [Held Invoices](#held-invoice-endpoints)
   - [Reports (Admin)](#report-endpoints)
   - [Salary (Admin)](#salary-endpoints)
   - [Petty Cash](#petty-cash-endpoints)
   - [Suppliers (Admin)](#supplier-endpoints)
   - [Stock Management (Admin)](#stock-management-endpoints)
6. [Rate Limiting](#rate-limiting)
7. [Code Examples](#code-examples)

---

## Overview

The Srijaya ERP Mobile POS API provides secure RESTful endpoints for mobile point-of-sale operations. All endpoints return JSON responses and use standard HTTP methods.

### Key Features
- ✅ JWT-based authentication
- ✅ Standardized response format
- ✅ Comprehensive error handling
- ✅ CORS support
- ✅ Pagination for list endpoints
- ✅ Secure input validation

---

## Authentication

### JWT Token Authentication

All API endpoints (except login) require authentication using JWT tokens.

#### User Roles

The system has two user roles:
- **Employee**: Regular cashier/staff with access to POS operations
- **Admin**: Full administrative access including reports, salary, suppliers, and stock management

#### How to Authenticate:

1. **Get Token**: Call the login endpoint with username and password
2. **Check Role**: The login response includes the user's role
3. **Use Token**: Include the token in every subsequent request

**Important**: Admin-only endpoints will return a `403 Forbidden` error if accessed with an Employee token. Make sure to login with admin credentials to access these endpoints.

#### Including Token in Requests:

**Option 1: Authorization Header (Recommended)**
```http
Authorization: Bearer YOUR_JWT_TOKEN_HERE
```

**Option 2: Custom Header**
```http
X-Auth-Token: YOUR_JWT_TOKEN_HERE
```

**Option 3: Query Parameter (Not Recommended for Production)**
```http
GET /api/v1/products/list.php?token=YOUR_JWT_TOKEN_HERE
```

#### Token Expiration
- Tokens expire after 24 hours
- When expired, client must re-authenticate
- Check token validity using `/auth/verify.php`

---

### Admin Access

Certain endpoints require **Admin** role access. These include:
- Reports (daily, monthly)
- Salary management
- Supplier management
- Stock/inventory summaries

#### How to Get Admin Token

1. **Login with Admin Credentials**: Use the same login endpoint but with admin username and password
2. **Check the Role**: The login response will include `"role": "Admin"`
3. **Use Admin Token**: Use this token for admin-only endpoints

**Example Admin Login:**
```bash
POST /api/v1/auth/login.php
Content-Type: application/json

{
  "username": "admin",
  "password": "admin_password"
}
```

**Response:**
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

#### Error When Using Employee Token for Admin Endpoints

If you try to access an admin-only endpoint with an Employee token, you'll receive:

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

**HTTP Status Code:** `403 Forbidden`

**Solution:** Login with admin credentials and use the admin token for these endpoints.

---

## Standard Response Format

All API responses follow a consistent JSON structure:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data here
  },
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": "Specific error for this field"
  },
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [ /* Array of items */ ],
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1",
    "pagination": {
      "total": 150,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 8,
      "has_more": true
    }
  }
}
```

---

## Error Handling

### HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request data |
| 401 | Unauthorized | Authentication required or token invalid |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 405 | Method Not Allowed | HTTP method not supported |
| 422 | Validation Error | Input validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error occurred |

### Common Error Messages

```json
{
  "success": false,
  "message": "Authentication token required",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

---

## API Endpoints

## Authentication Endpoints

### 1. Login

Authenticate user and receive JWT token.

**Endpoint:** `POST /api/v1/auth/login.php`

**Request Body:**
```json
{
  "username": "cashier01",
  "password": "password123"
}
```

**Success Response (200) - Employee Login:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 5,
      "name": "cashier01",
      "role": "Employee"
    },
    "expires_in": 86400
  },
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

**Success Response (200) - Admin Login:**
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
  },
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

**Note:** The `role` field in the response indicates whether the user is an "Employee" or "Admin". Save both the token and role information. Admin-only endpoints require a token from an Admin user.

**Error Response (401):**
```json
{
  "success": false,
  "message": "Invalid username or password",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "username": "Username is required",
    "password": "Password is required"
  },
  "meta": {
    "timestamp": "2025-10-17 10:30:00",
    "version": "v1"
  }
}
```

---

### 2. Logout

Invalidate user session and log logout action.

**Endpoint:** `POST /api/v1/auth/logout.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Request Body:** None

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logout successful",
  "data": null,
  "meta": {
    "timestamp": "2025-10-17 11:45:00",
    "version": "v1"
  }
}
```

---

### 3. Verify Token

Check if JWT token is still valid.

**Endpoint:** `GET /api/v1/auth/verify.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Token is valid",
  "data": {
    "user": {
      "id": 5,
      "name": "cashier01",
      "role": "Employee"
    },
    "token_valid": true,
    "expires_at": "2025-10-18 10:30:00"
  },
  "meta": {
    "timestamp": "2025-10-17 15:00:00",
    "version": "v1"
  }
}
```

---

## Product Endpoints

### 1. Search Products

Search for products by name, SKU, or barcode.

**Endpoint:** `GET /api/v1/products/search.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `q` (required): Search query string
- `limit` (optional): Maximum results (default: 20, max: 50)

**Example Request:**
```http
GET /api/v1/products/search.php?q=printing&limit=10
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "5 products found",
  "data": [
    {
      "id": 101,
      "name": "A4 Color Printing",
      "sku": "PRINT-A4-COL",
      "barcode": "1234567890123",
      "price": 15.00,
      "cost_price": 8.50,
      "has_stock": true,
      "available_quantity": 500.0,
      "is_active": true,
      "image": "/products/create/uploads/products/a4_printing.jpg"
    },
    {
      "id": 102,
      "name": "A4 B&W Printing",
      "sku": "PRINT-A4-BW",
      "barcode": "1234567890124",
      "price": 5.00,
      "cost_price": 2.00,
      "has_stock": true,
      "available_quantity": 1000.0,
      "is_active": true,
      "image": null
    }
  ],
  "meta": {
    "timestamp": "2025-10-17 10:45:00",
    "version": "v1",
    "search_query": "printing",
    "result_count": 5
  }
}
```

---

### 2. List Products

Get paginated list of products.

**Endpoint:** `GET /api/v1/products/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `active_only` (optional): Filter active products (default: true)
- `in_stock_only` (optional): Filter in-stock products (default: false)

**Example Request:**
```http
GET /api/v1/products/list.php?page=1&per_page=20&active_only=true
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 101,
      "name": "A4 Color Printing",
      "sku": "PRINT-A4-COL",
      "barcode": "1234567890123",
      "price": 15.00,
      "cost_price": 8.50,
      "category": "Printing Services",
      "has_stock": true,
      "available_quantity": 500.0,
      "is_active": true,
      "image": "/products/create/uploads/products/a4_printing.jpg"
    }
    // ... more products
  ],
  "meta": {
    "timestamp": "2025-10-17 10:45:00",
    "version": "v1",
    "pagination": {
      "total": 150,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 8,
      "has_more": true
    }
  }
}
```

---

### 3. Product Details

Get detailed information about a specific product.

**Endpoint:** `GET /api/v1/products/details.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `id` (required): Product ID

**Example Request:**
```http
GET /api/v1/products/details.php?id=101
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Product details retrieved successfully",
  "data": {
    "id": 101,
    "name": "A4 Color Printing",
    "sku": "PRINT-A4-COL",
    "barcode": "1234567890123",
    "price": 15.00,
    "cost_price": 8.50,
    "profit_margin": 76.47,
    "category": "Printing Services",
    "description": "High-quality A4 color printing service",
    "has_stock": true,
    "available_quantity": 500.0,
    "alert_quantity": 100.0,
    "is_active": true,
    "image": "/products/create/uploads/products/a4_printing.jpg",
    "created_at": "2024-01-15 10:30:00",
    "batches": [
      {
        "batch_id": 501,
        "batch_number": "BATCH-2025-001",
        "quantity": 300.0,
        "cost_price": 8.50,
        "alert_quantity": 50.0,
        "is_active": true,
        "created_at": "2025-01-10 08:00:00"
      }
    ],
    "low_stock_alert": false
  },
  "meta": {
    "timestamp": "2025-10-17 10:50:00",
    "version": "v1"
  }
}
```

---

## Customer Endpoints

### 1. Search Customers

Search for customers by name or mobile number.

**Endpoint:** `GET /api/v1/customers/search.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `q` (required): Search query string
- `limit` (optional): Maximum results (default: 20, max: 50)

**Example Request:**
```http
GET /api/v1/customers/search.php?q=john&limit=10
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "3 customers found",
  "data": [
    {
      "id": 45,
      "name": "John Doe",
      "mobile": "0771234567",
      "address": "123 Main St, Colombo",
      "email": "john@example.com",
      "extra_fund": 150.00,
      "created_date": "2024-05-10"
    },
    {
      "id": 67,
      "name": "Johnny Smith",
      "mobile": "0759876543",
      "address": "456 Park Ave, Kandy",
      "email": "johnny@example.com",
      "extra_fund": 0.00,
      "created_date": "2024-08-22"
    }
  ],
  "meta": {
    "timestamp": "2025-10-17 11:00:00",
    "version": "v1",
    "search_query": "john",
    "result_count": 3
  }
}
```

---

### 2. Customer Details

Get detailed information about a specific customer.

**Endpoint:** `GET /api/v1/customers/details.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `id` (required): Customer ID

**Example Request:**
```http
GET /api/v1/customers/details.php?id=45
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Customer details retrieved successfully",
  "data": {
    "id": 45,
    "name": "John Doe",
    "mobile": "0771234567",
    "address": "123 Main St, Colombo",
    "email": "john@example.com",
    "extra_fund": 150.00,
    "created_date": "2024-05-10",
    "statistics": {
      "total_purchases": 5500.00,
      "outstanding_balance": 500.00,
      "recent_invoice_count": 10
    },
    "recent_invoices": [
      {
        "invoice_id": 1234,
        "total": 850.00,
        "advance": 500.00,
        "balance": 350.00,
        "date": "2025-10-15",
        "payment_method": "Cash",
        "full_paid": false
      }
      // ... more invoices
    ]
  },
  "meta": {
    "timestamp": "2025-10-17 11:05:00",
    "version": "v1"
  }
}
```

---

### 3. Add Customer

Create a new customer.

**Endpoint:** `POST /api/v1/customers/add.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Sarah Johnson",
  "mobile": "0771234568",
  "address": "789 Lake Rd, Galle",
  "email": "sarah@example.com"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Customer created successfully",
  "data": {
    "id": 89,
    "name": "Sarah Johnson",
    "mobile": "0771234568",
    "address": "789 Lake Rd, Galle",
    "email": "sarah@example.com"
  },
  "meta": {
    "timestamp": "2025-10-17 11:10:00",
    "version": "v1"
  }
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": "Customer name is required",
    "mobile": "Mobile number must be 10 digits"
  },
  "meta": {
    "timestamp": "2025-10-17 11:10:00",
    "version": "v1"
  }
}
```

---

### 4. List Customers

Get paginated list of all customers with statistics.

**Endpoint:** `GET /api/v1/customers/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `search` (optional): Search by name or mobile
- `type` (optional): Filter by customer type: all, regular, vip, wholesale (default: all)

**Example Request:**
```http
GET /api/v1/customers/list.php?page=1&per_page=20&search=john&type=regular
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Customers retrieved successfully",
  "data": [
    {
      "id": 45,
      "name": "John Doe",
      "mobile": "0771234567",
      "type": "regular",
      "extra_fund": 150.00,
      "statistics": {
        "invoice_count": 10,
        "total_purchases": 5500.00,
        "outstanding_balance": 500.00
      }
    },
    {
      "id": 46,
      "name": "Jane Smith",
      "mobile": "0759876543",
      "type": "vip",
      "extra_fund": 0.00,
      "statistics": {
        "invoice_count": 25,
        "total_purchases": 15000.00,
        "outstanding_balance": 0.00
      }
    }
  ],
  "meta": {
    "timestamp": "2025-10-22 10:30:00",
    "version": "v1",
    "pagination": {
      "total": 150,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 8,
      "has_more": true
    }
  }
}
```

---

### 5. Edit Customer

Update customer information with optional past invoice updates.

**Endpoint:** `PUT /api/v1/customers/edit.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 45,
  "name": "John Doe Updated",
  "mobile": "0771234567",
  "type": "vip",
  "update_past_invoices": true
}
```

**Request Parameters:**
- `id` (required): Customer ID
- `name` (required): Customer name
- `mobile` (required): 10 digit mobile number
- `type` (optional): Customer type (regular, vip, wholesale)
- `update_past_invoices` (optional): Boolean - if true, updates customer name/mobile in all past invoices (default: false)

**Important Note:** 
When `update_past_invoices` is set to `true`, the system will update the customer's name and/or mobile number in all past invoices in the `invoice` table. This is necessary because invoice records store customer information directly rather than using a relational reference. If set to `false`, changes only affect future invoices.

**Success Response (200):**
```json
{
  "success": true,
  "message": "Customer updated successfully",
  "data": {
    "id": 45,
    "name": "John Doe Updated",
    "mobile": "0771234567",
    "type": "vip",
    "extra_fund": 150.00,
    "invoices_updated": 10,
    "invoice_count": 10
  },
  "meta": {
    "timestamp": "2025-10-22 11:00:00",
    "version": "v1"
  }
}
```

**Response Fields:**
- `invoices_updated`: Number of past invoices that were updated (0 if `update_past_invoices` was false)
- `invoice_count`: Total number of invoices associated with this customer

**Error Response (404):**
```json
{
  "success": false,
  "message": "Customer not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 11:00:00",
    "version": "v1"
  }
}
```

**Error Response (409):**
```json
{
  "success": false,
  "message": "Mobile number is already used by another customer",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 11:00:00",
    "version": "v1"
  }
}
```

---

### 6. Delete Customer

Delete a customer (only if they have no invoices).

**Endpoint:** `DELETE /api/v1/customers/delete.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 45
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Customer deleted successfully",
  "data": null,
  "meta": {
    "timestamp": "2025-10-22 11:30:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Customer not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 11:30:00",
    "version": "v1"
  }
}
```

**Error Response (409) - Customer has invoices:**
```json
{
  "success": false,
  "message": "Cannot delete customer. This customer has 10 invoice(s) associated with their account. Please remove or reassign these invoices first.",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 11:30:00",
    "version": "v1"
  }
}
```

**Important Note:**
Customers with existing invoices cannot be deleted to maintain data integrity. You must first delete or reassign all invoices associated with the customer before deleting the customer record.

---

### 7. Customer Invoices

Get all invoices for a specific customer with filtering and pagination.

**Endpoint:** `GET /api/v1/customers/invoices.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `id` (required): Customer ID
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `status` (optional): Filter by status: all, paid, unpaid, partial (default: all)

**Example Request:**
```http
GET /api/v1/customers/invoices.php?id=45&page=1&per_page=20&status=unpaid
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Customer invoices retrieved successfully",
  "data": {
    "customer": {
      "id": 45,
      "name": "John Doe",
      "mobile": "0771234567",
      "type": "regular",
      "extra_fund": 150.00
    },
    "totals": {
      "total_sales": 5500.00,
      "total_paid": 5000.00,
      "total_outstanding": 500.00,
      "total_profit": 1200.00
    },
    "invoices": [
      {
        "invoice_number": 1235,
        "description": null,
        "customer_name": "John Doe",
        "customer_mobile": "0771234567",
        "date": "2025-10-17",
        "time": "11:15:30",
        "biller": "cashier01",
        "total": 2300.00,
        "discount": 100.00,
        "advance": 2000.00,
        "balance": 300.00,
        "cost": 1500.00,
        "profit": 700.00,
        "full_paid": false,
        "payment_method": "Cash",
        "credit_payment": false,
        "amount_received": 2000.00,
        "cash_change": 0.00,
        "status": "Partially Paid"
      }
    ]
  },
  "meta": {
    "timestamp": "2025-10-22 12:00:00",
    "version": "v1",
    "pagination": {
      "total": 10,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 1,
      "has_more": false
    }
  }
}
```

**Status Values:**
- `Paid`: Full payment received (full_paid = true)
- `Partially Paid`: Partial payment received (advance > 0 but full_paid = false)
- `Unpaid`: No payment received (advance = 0)

---

## Invoice Endpoints

### 1. Submit Invoice

Process and save a new invoice/transaction.

**Endpoint:** `POST /api/v1/invoices/submit.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "customerName": "John Doe",
  "customerNumber": "0771234567",
  "items": [
    {
      "product_id": 101,
      "product_name": "A4 Color Printing",
      "quantity": 100,
      "rate": 15.00,
      "amount": 1500.00,
      "discount": 0,
      "worker_id": 5
    },
    {
      "product_id": 102,
      "product_name": "Photo Frame 8x10",
      "quantity": 2,
      "rate": 450.00,
      "amount": 900.00,
      "discount": 50.00,
      "worker_id": 5
    }
  ],
  "subtotal": 2400.00,
  "discountType": "flat",
  "discountValue": 100.00,
  "totalPayable": 2300.00,
  "totalReceived": 2500.00,
  "paymentMethod": "Cash",
  "creditPayment": false,
  "printReceipt": true,
  "bool_useCustomerExtraFund": false,
  "useCustomerExtraFundAmount": 0
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Invoice submitted successfully",
  "data": {
    "invoice_id": 1235,
    "invoice_number": "INV-2025-001235",
    "total": 2300.00,
    "advance": 2300.00,
    "balance": 0.00,
    "cash_change": 200.00,
    "customer": {
      "id": 45,
      "name": "John Doe"
    },
    "date": "2025-10-17",
    "time": "11:15:30"
  },
  "meta": {
    "timestamp": "2025-10-17 11:15:30",
    "version": "v1"
  }
}
```

---

### 2. List Invoices

Get list of invoices with filtering and pagination.

**Endpoint:** `GET /api/v1/invoices/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `status` (optional): Filter by status: all, paid, pending, partially_paid, overdue (default: all)
- `date_from` (optional): Filter from date (YYYY-MM-DD)
- `date_to` (optional): Filter to date (YYYY-MM-DD)
- `customer_id` (optional): Filter by customer ID
- `invoice_number` (optional): Search by invoice number

**Example Request:**
```http
GET /api/v1/invoices/list.php?page=1&per_page=20&status=paid&date_from=2025-10-01
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Invoices retrieved successfully",
  "data": [
    {
      "invoice_id": 1235,
      "invoice_number": "INV-2025-001235",
      "customer": {
        "id": 45,
        "name": "John Doe",
        "mobile": "0771234567"
      },
      "total": 2300.00,
      "discount": 100.00,
      "advance": 2300.00,
      "balance": 0.00,
      "date": "2025-10-17",
      "time": "11:15:30",
      "payment_method": "Cash",
      "biller_name": "cashier01",
      "status": "Paid",
      "is_paid": true,
      "is_overdue": false,
      "days_old": 0
    }
    // ... more invoices
  ],
  "meta": {
    "timestamp": "2025-10-17 11:20:00",
    "version": "v1",
    "pagination": {
      "total": 85,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 5,
      "has_more": true
    }
  }
}
```

---

## Attendance Endpoints

### 1. Clock In/Out

Record employee attendance (clock in or clock out).

**Endpoint:** `POST /api/v1/attendance/clock.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "action": "Clock In"
}
```
OR
```json
{
  "action": "Clock Out"
}
```

**Success Response (201) - Clock In:**
```json
{
  "success": true,
  "message": "Clock In recorded successfully",
  "data": {
    "attendance_id": 4567,
    "action": "Clock In",
    "employee": {
      "id": 5,
      "name": "cashier01"
    },
    "timestamp": "2025-10-17 09:00:00",
    "is_clocked_in": true,
    "salary_info": null
  },
  "meta": {
    "timestamp": "2025-10-17 09:00:00",
    "version": "v1"
  }
}
```

**Success Response (201) - Clock Out:**
```json
{
  "success": true,
  "message": "Clock Out recorded successfully",
  "data": {
    "attendance_id": 4568,
    "action": "Clock Out",
    "employee": {
      "id": 5,
      "name": "cashier01"
    },
    "timestamp": "2025-10-17 18:00:00",
    "is_clocked_in": false,
    "salary_info": {
      "hours_worked": 9.0,
      "salary_paid": 1500.00,
      "reason": "Full day salary"
    }
  },
  "meta": {
    "timestamp": "2025-10-17 18:00:00",
    "version": "v1"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Already clocked in. Please clock out first.",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-17 09:05:00",
    "version": "v1"
  }
}
```

---

### 2. Get Attendance Status

Get current clock in/out status of authenticated employee.

**Endpoint:** `GET /api/v1/attendance/status.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Attendance status retrieved successfully",
  "data": {
    "is_clocked_in": true,
    "clock_in_time": "09:00:00",
    "hours_worked": 2.5,
    "today_records": [
      {
        "action": "Clock In",
        "time": "09:00:00"
      }
    ],
    "next_action": "Clock Out"
  },
  "meta": {
    "timestamp": "2025-10-17 11:30:00",
    "version": "v1"
  }
}
```

---

## Rate Limiting

Currently, rate limiting is **disabled** in the development environment. When enabled in production:

- **Limit:** 100 requests per minute per IP address
- **Response when exceeded:**
```json
{
  "success": false,
  "message": "Rate limit exceeded. Please try again later.",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-17 12:00:00",
    "version": "v1"
  }
}
```

---

## Code Examples

### JavaScript/React Example

```javascript
// Configuration
const API_BASE_URL = 'https://yourdomain.com/api/v1';
let authToken = null;
let userRole = null;

// Login
async function login(username, password) {
  try {
    const response = await fetch(`${API_BASE_URL}/auth/login.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ username, password })
    });
    
    const data = await response.json();
    
    if (data.success) {
      authToken = data.data.token;
      userRole = data.data.user.role;
      localStorage.setItem('auth_token', authToken);
      localStorage.setItem('user_role', userRole);
      localStorage.setItem('user_data', JSON.stringify(data.data.user));
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Login error:', error);
    throw error;
  }
}

// Check if user is admin
function isAdmin() {
  return userRole === 'Admin';
}

// Get Daily Report (Admin Only)
async function getDailyReport(date) {
  if (!isAdmin()) {
    throw new Error('Admin access required');
  }
  
  try {
    const response = await fetch(
      `${API_BASE_URL}/reports/daily.php?date=${date}`,
      {
        headers: {
          'Authorization': `Bearer ${authToken}`
        }
      }
    );
    
    const data = await response.json();
    
    if (data.success) {
      return data.data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Report error:', error);
    throw error;
  }
}

// Search Products (All Users)
async function searchProducts(query) {
  try {
    const response = await fetch(
      `${API_BASE_URL}/products/search.php?q=${encodeURIComponent(query)}&limit=20`,
      {
        headers: {
          'Authorization': `Bearer ${authToken}`
        }
      }
    );
    
    const data = await response.json();
    
    if (data.success) {
      return data.data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Search error:', error);
    throw error;
  }
}

// Usage Examples
// Employee Login
// await login('cashier01', 'password123');

// Admin Login
// await login('admin', 'admin_password');

// Check if admin
// if (isAdmin()) {
//   const report = await getDailyReport('2025-10-17');
// }

// Search products (works for all users)
// const products = await searchProducts('printing');
```

// Submit Invoice
async function submitInvoice(invoiceData) {
  try {
    const response = await fetch(`${API_BASE_URL}/invoices/submit.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`
      },
      body: JSON.stringify(invoiceData)
    });
    
    const data = await response.json();
    
    if (data.success) {
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Submit invoice error:', error);
    throw error;
  }
}

// Clock In/Out
async function clockAction(action) {
  try {
    const response = await fetch(`${API_BASE_URL}/attendance/clock.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`
      },
      body: JSON.stringify({ action })
    });
    
    const data = await response.json();
    
    if (data.success) {
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Clock action error:', error);
    throw error;
  }
}

// Usage Examples
// await login('cashier01', 'password123');
// const products = await searchProducts('printing');
// await clockAction('Clock In');
// const invoice = await submitInvoice(invoiceData);
```

### PHP/cURL Example

```php
<?php
// Configuration
define('API_BASE_URL', 'https://yourdomain.com/api/v1');
$authToken = null;

// Login
function login($username, $password) {
    global $authToken;
    
    $ch = curl_init(API_BASE_URL . '/auth/login.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'username' => $username,
        'password' => $password
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if ($data['success']) {
        $authToken = $data['data']['token'];
        return $data;
    } else {
        throw new Exception($data['message']);
    }
}

// Search Products
function searchProducts($query, $limit = 20) {
    global $authToken;
    
    $url = API_BASE_URL . '/products/search.php?q=' . urlencode($query) . '&limit=' . $limit;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $authToken
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Submit Invoice
function submitInvoice($invoiceData) {
    global $authToken;
    
    $ch = curl_init(API_BASE_URL . '/invoices/submit.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $authToken
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invoiceData));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
try {
    login('cashier01', 'password123');
    $products = searchProducts('printing');
    print_r($products);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

---

## One-Time Product Endpoints

### 1. Add One-Time Product

Create a custom product that isn't in regular inventory.

**Endpoint:** `POST /api/v1/one_time_products/add.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "product_name": "Custom Business Cards - Premium",
  "rate": 5000.00,
  "quantity": 1,
  "description": "Premium business cards with gold foil"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "One-time product created successfully",
  "data": {
    "product_id": 45,
    "product_name": "Custom Business Cards - Premium",
    "rate": 5000.00,
    "quantity": 1,
    "amount": 5000.00,
    "description": "Premium business cards with gold foil",
    "status": "pending",
    "created_at": "2025-10-17 14:30:00"
  }
}
```

---

### 2. List One-Time Products

Get list of one-time products with filtering.

**Endpoint:** `GET /api/v1/one_time_products/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `status` (optional): Filter by status: all, uncleared, cleared, skip, pending (default: all)

**Example Request:**
```http
GET /api/v1/one_time_products/list.php?page=1&status=uncleared
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "One-time products retrieved successfully",
  "data": [
    {
      "id": 45,
      "product_name": "Custom Business Cards - Premium",
      "quantity": 1,
      "rate": 5000.00,
      "amount": 5000.00,
      "status": "uncleared",
      "invoice_number": "INV-2025-001245",
      "worker": {
        "id": 5,
        "name": "cashier01"
      },
      "created_at": "2025-10-17 14:30:00"
    }
  ],
  "meta": {
    "timestamp": "2025-10-17 15:00:00",
    "version": "v1",
    "pagination": {
      "total": 25,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 2,
      "has_more": true
    }
  }
}
```

---

### 3. Update Product Status

Update the status of a one-time product.

**Endpoint:** `PUT /api/v1/one_time_products/update_status.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "product_id": 45,
  "status": "cleared"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Status updated successfully",
  "data": {
    "product_id": 45,
    "product_name": "Custom Business Cards - Premium",
    "old_status": "uncleared",
    "new_status": "cleared",
    "updated_at": "2025-10-17 15:10:00"
  }
}
```

---

## Held Invoice Endpoints

### 1. Hold Invoice

Save current invoice for later processing.

**Endpoint:** `POST /api/v1/held_invoices/hold.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "customerName": "John Doe",
  "customerNumber": "0771234567",
  "items": [
    {
      "product_id": 101,
      "product_name": "A4 Color Printing",
      "quantity": 50,
      "rate": 15.00,
      "amount": 750.00
    }
  ],
  "subtotal": 750.00,
  "discountType": "flat",
  "discountValue": 0,
  "totalPayable": 750.00,
  "notes": "Customer will return tomorrow"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Invoice held successfully",
  "data": {
    "held_id": 12,
    "customer_name": "John Doe",
    "customer_number": "0771234567",
    "item_count": 1,
    "total": 750.00,
    "held_date": "2025-10-17",
    "held_time": "15:30:00",
    "held_by": {
      "id": 5,
      "name": "cashier01"
    }
  }
}
```

---

### 2. List Held Invoices

Get list of held invoices.

**Endpoint:** `GET /api/v1/held_invoices/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `status` (optional): Filter by status: held, all (default: held)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Held invoices retrieved successfully",
  "data": [
    {
      "id": 12,
      "customer": {
        "name": "John Doe",
        "number": "0771234567"
      },
      "items": [...],
      "item_count": 1,
      "subtotal": 750.00,
      "discount": {
        "type": "flat",
        "value": 0
      },
      "total": 750.00,
      "notes": "Customer will return tomorrow",
      "held_by": {
        "id": 5,
        "name": "cashier01"
      },
      "held_date": "2025-10-17",
      "held_time": "15:30:00",
      "status": "held",
      "created_at": "2025-10-17 15:30:00"
    }
  ],
  "meta": {
    "pagination": {...}
  }
}
```

---

### 3. Resume Held Invoice

Retrieve a held invoice for processing.

**Endpoint:** `GET /api/v1/held_invoices/resume.php?id=12`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Held invoice retrieved successfully",
  "data": {
    "held_id": 12,
    "customerName": "John Doe",
    "customerNumber": "0771234567",
    "items": [...],
    "subtotal": 750.00,
    "discountType": "flat",
    "discountValue": 0,
    "totalPayable": 750.00,
    "notes": "Customer will return tomorrow",
    "held_info": {
      "held_by": {...},
      "held_date": "2025-10-17",
      "held_time": "15:30:00",
      "created_at": "2025-10-17 15:30:00"
    }
  }
}
```

---

### 4. Delete Held Invoice

Cancel/delete a held invoice.

**Endpoint:** `DELETE /api/v1/held_invoices/delete.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 12
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Held invoice cancelled successfully",
  "data": {
    "held_id": 12,
    "customer_name": "John Doe",
    "old_status": "held",
    "new_status": "cancelled",
    "cancelled_at": "2025-10-17 16:00:00"
  }
}
```

---

## Report Endpoints

**Admin Access Required** for all report endpoints.

### 1. Daily Report

Get daily business report.

**Endpoint:** `GET /api/v1/reports/daily.php?date=2025-10-17`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Daily report retrieved successfully",
  "data": {
    "date": "2025-10-17",
    "sales": {
      "invoice_count": 45,
      "cash_in": 125000.00,
      "gross_profit": 45000.00,
      "avg_invoice_value": 2777.78,
      "paid_invoices": 42,
      "pending_invoices": 3
    },
    "expenses": {
      "total": 15000.00,
      "breakdown": {
        "Petty Cash": 3000.00,
        "Raw Item Purchase": 10000.00,
        "Salary Payment": 2000.00
      }
    },
    "banking": {
      "deposits": 100000.00
    },
    "summary": {
      "net_profit": 30000.00,
      "profitability_ratio": 24.00
    }
  }
}
```

---

### 2. Monthly Report

Get monthly business report with daily breakdown.

**Endpoint:** `GET /api/v1/reports/monthly.php?month=2025-10`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Monthly report retrieved successfully",
  "data": {
    "month": "2025-10",
    "period": {
      "start_date": "2025-10-01",
      "end_date": "2025-10-31"
    },
    "sales": {
      "invoice_count": 1250,
      "cash_in": 3500000.00,
      "gross_profit": 1200000.00,
      "avg_invoice_value": 2800.00,
      "paid_invoices": 1180,
      "pending_invoices": 70
    },
    "expenses": {
      "total": 450000.00,
      "breakdown": {...}
    },
    "banking": {
      "deposits": 3000000.00
    },
    "summary": {
      "net_profit": 750000.00,
      "profitability_ratio": 21.43
    },
    "daily_breakdown": [
      {
        "date": "2025-10-01",
        "invoice_count": 40,
        "revenue": 110000.00,
        "profit": 38000.00
      }
    ]
  }
}
```

---

## Salary Endpoints

**Admin Access Required** for all salary endpoints.

### 1. Pay Salary

Process employee salary payment.

**Endpoint:** `POST /api/v1/salary/pay.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "employee_id": 5,
  "amount": 15000.00,
  "account": "Cash in Hand",
  "description": "Monthly Salary - October 2025"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Salary paid successfully",
  "data": {
    "employee": {
      "id": 5,
      "name": "John Employee"
    },
    "amount": 15000.00,
    "account": "Cash in Hand",
    "description": "Monthly Salary - October 2025",
    "paid_by": {
      "id": 1,
      "name": "admin"
    },
    "paid_at": "2025-10-17 16:30:00"
  }
}
```

---

### 2. Salary History

Get salary payment history.

**Endpoint:** `GET /api/v1/salary/history.php?employee_id=5`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Query Parameters:**
- `employee_id` (optional): Filter by employee ID
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Salary history retrieved successfully",
  "data": [
    {
      "id": 150,
      "employee": {
        "id": 5,
        "name": "John Employee"
      },
      "date": "2025-10-17",
      "amount": -15000.00,
      "description": "Monthly Salary - October 2025",
      "type": "payment"
    }
  ],
  "meta": {
    "pagination": {...}
  }
}
```

---

## Petty Cash Endpoints

### List Petty Cash

Get petty cash transactions.

**Endpoint:** `GET /api/v1/pettycash/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `date_from` (optional): Filter from date (YYYY-MM-DD)
- `date_to` (optional): Filter to date (YYYY-MM-DD)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Petty cash records retrieved successfully",
  "data": [
    {
      "id": 234,
      "description": "Office Supplies",
      "amount": 1500.00,
      "date": "2025-10-17",
      "time": "14:20:00",
      "employee_name": "cashier01"
    }
  ],
  "meta": {
    "pagination": {...},
    "summary": {
      "total_amount": 15000.00,
      "date_range": {
        "from": "2025-10-01",
        "to": "2025-10-31"
      }
    }
  }
}
```

---

## Supplier Endpoints

**Admin Access Required** for all supplier endpoints.

### 1. List Suppliers

Get list of suppliers.

**Endpoint:** `GET /api/v1/suppliers/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `search` (optional): Search by name, mobile, or address

**Success Response (200):**
```json
{
  "success": true,
  "message": "Suppliers retrieved successfully",
  "data": [
    {
      "id": 8,
      "name": "ABC Paper Supplies",
      "mobile": "0771234567",
      "address": "Colombo 03",
      "credit_balance": 25000.00,
      "created_at": "2024-01-15 10:00:00"
    }
  ],
  "meta": {
    "pagination": {...}
  }
}
```

---

### 2. Supplier Details

Get detailed information about a supplier.

**Endpoint:** `GET /api/v1/suppliers/details.php?id=8`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Supplier details retrieved successfully",
  "data": {
    "id": 8,
    "name": "ABC Paper Supplies",
    "mobile": "0771234567",
    "address": "Colombo 03",
    "credit_balance": 25000.00,
    "created_at": "2024-01-15 10:00:00",
    "statistics": {
      "total_purchases": 45,
      "total_amount": 1500000.00
    },
    "recent_purchases": [
      {
        "purchase_id": 234,
        "total": 50000.00,
        "paid": 30000.00,
        "balance": 20000.00,
        "date": "2025-10-15",
        "status": "partial"
      }
    ]
  }
}
```

---

## Stock Management Endpoints

**Admin Access Required** for all stock management endpoints.

### Stock Summary

Get comprehensive stock/inventory summary.

**Endpoint:** `GET /api/v1/stock/summary.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Stock summary retrieved successfully",
  "data": {
    "overview": {
      "total_products": 150,
      "total_stock_quantity": 5000.00,
      "total_stock_value": 750000.00,
      "low_stock_count": 12,
      "out_of_stock_count": 3
    },
    "low_stock_items": [
      {
        "product_id": 45,
        "product_name": "A4 Paper - White",
        "current_stock": 50.00,
        "alert_level": 100.00,
        "stock_level_percentage": 50.00,
        "cost_price": 500.00,
        "status": "low_stock"
      }
    ],
    "out_of_stock_items": [
      {
        "product_id": 67,
        "product_name": "Photo Frame 8x10",
        "alert_level": 20.00,
        "status": "out_of_stock"
      }
    ],
    "stock_by_category": [
      {
        "category": "Paper Products",
        "product_count": 45,
        "total_quantity": 2000.00,
        "total_value": 300000.00
      }
    ]
  }
}
```

---

## Security Best Practices

1. **Always use HTTPS** in production
2. **Store tokens securely** (never in plain cookies)
3. **Implement token refresh** mechanism for long sessions
4. **Validate all input** on client and server
5. **Never log sensitive data** (passwords, tokens)
6. **Use environment variables** for secrets
7. **Enable rate limiting** in production
8. **Implement CSRF protection** for web clients
9. **Keep JWT secret secure** and rotate periodically
10. **Monitor for suspicious activity**

---

## Support & Contact

For API support or questions:
- **Email:** support@srijayaprint.com
- **Phone:** 0714730996
- **Address:** FF26, Megacity, Athurugiriya

---

**Document End**

*Last Updated: October 17, 2025*
