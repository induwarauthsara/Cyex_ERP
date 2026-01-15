# Srijaya ERP Mobile POS API Documentation

**Version:** 1.2  
**Base URL:** `https://yourdomain.com/api/v1`  
**Last Updated:** December 5, 2025

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
   - [Employees](#employee-endpoints)
   - [Attendance](#attendance-endpoints)
   - [One-Time Products](#one-time-product-endpoints)
   - [Held Invoices](#held-invoice-endpoints)
   - [Reports (Admin)](#report-endpoints)
   - [Salary (Admin)](#salary-endpoints)
   - [Petty Cash](#petty-cash-endpoints)
   - [Suppliers (Admin)](#supplier-endpoints)
   - [Stock Management (Admin)](#stock-management-endpoints)
   - [GRN - Goods Received Notes (Admin)](#grn-endpoints)
   - [Expenses Management](#expenses-endpoints)
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

Delete a customer from the system.

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

**Important Notes:**
- Customers can be deleted even if they have associated invoices
- The invoice table stores customer data directly (denormalized - `customer_name` and `customer_mobile` are stored in each invoice record)
- Deleting a customer will NOT affect existing invoices - they will retain all customer information
- The `customer_id` field in invoices is just a reference number and does not have a foreign key constraint
- Past invoices will remain accessible with the customer information that was stored at the time of sale

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
    "commission": {
      "enabled": true,
      "total_commission": 150.50,
      "employee_id": 5,
      "records_count": 2,
      "details": [
        {
          "product_name": "A4 Color Printing",
          "profit": 650.00,
          "commission_percentage": 5.00,
          "commission_amount": 32.50
        }
      ]
    },
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
      "invoice_number": 1235,
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
      "days_old": 0,
      "items": [
        {
          "sales_id": 5001,
          "product": "A4 Color Printing",
          "batch": "BATCH-2025-001",
          "description": "High quality printing",
          "quantity": 100.0,
          "rate": 15.00,
          "amount": 1500.00,
          "cost": 850.00,
          "profit": 650.00,
          "worker": "5",
          "discount_price": 0.00,
          "individual_discount_mode": false
        },
        {
          "sales_id": 5002,
          "product": "Photo Frame 8x10",
          "batch": "BATCH-2025-015",
          "description": null,
          "quantity": 2.0,
          "rate": 450.00,
          "amount": 900.00,
          "cost": 600.00,
          "profit": 300.00,
          "worker": "5",
          "discount_price": 50.00,
          "individual_discount_mode": true
        }
      ]
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

**Invoice Item Fields:**

- `sales_id`: Unique identifier for the sale item
- `product`: Product name
- `batch`: Batch number (if applicable)
- `description`: Additional description (optional)
- `quantity`: Quantity sold
- `rate`: Unit price
- `amount`: Total amount (quantity × rate)
- `cost`: Cost price
- `profit`: Profit earned
- `worker`: Worker/employee ID who handled the item
- `discount_price`: Discount applied to this item
- `individual_discount_mode`: Whether individual discount was applied (true/false)

---

## Employee Endpoints

### 1. List Employees

Get paginated list of employees with filtering.

**Endpoint:** `GET /api/v1/employees/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `status` (optional): Filter by status: all, active, inactive (default: all)
- `search` (optional): Search by name, mobile, or NIC
- `role` (optional): Filter by role: Employee, Admin

**Example Request:**
```http
GET /api/v1/employees/list.php?page=1&per_page=20&status=active&role=Employee
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Employees retrieved successfully",
  "data": [
    {
      "id": 5,
      "name": "John Employee",
      "mobile": 771234567,
      "address": "123 Main St, Colombo",
      "bank_account": "BOC - 123456789",
      "role": "Employee",
      "nic": "199012345678",
      "salary": 30000.00,
      "day_salary": 1500.00,
      "status": "active",
      "is_clocked_in": false,
      "onboard_date": "2024-01-15"
    },
    {
      "id": 1,
      "name": "Admin User",
      "mobile": 771234560,
      "address": "456 Park Ave, Kandy",
      "bank_account": "Commercial - 987654321",
      "role": "Admin",
      "nic": "198512345678",
      "salary": 50000.00,
      "day_salary": 2500.00,
      "status": "active",
      "is_clocked_in": true,
      "onboard_date": "2023-06-01"
    }
  ],
  "meta": {
    "timestamp": "2025-10-22 14:30:00",
    "version": "v1",
    "pagination": {
      "total": 15,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 1,
      "has_more": false
    }
  }
}
```

---

### 2. Employee Details

Get detailed information about a specific employee including statistics.

**Endpoint:** `GET /api/v1/employees/details.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `id` (required): Employee ID

**Example Request:**
```http
GET /api/v1/employees/details.php?id=5
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Employee details retrieved successfully",
  "data": {
    "id": 5,
    "name": "John Employee",
    "mobile": 771234567,
    "address": "123 Main St, Colombo",
    "bank_account": "BOC - 123456789",
    "role": "Employee",
    "nic": "199012345678",
    "salary": 30000.00,
    "day_salary": 1500.00,
    "status": "active",
    "is_clocked_in": false,
    "onboard_date": "2024-01-15",
    "statistics": {
      "attendance": {
        "total_records": 250,
        "total_clock_ins": 125,
        "total_clock_outs": 125
      },
      "salary": {
        "total_payments": 10,
        "total_paid": 300000.00
      }
    },
    "recent_attendance": [
      {
        "id": 4567,
        "action": "Clock Out",
        "date": "2025-10-22",
        "time": "18:00:00"
      },
      {
        "id": 4566,
        "action": "Clock In",
        "date": "2025-10-22",
        "time": "09:00:00"
      },
      {
        "id": 4565,
        "action": "Clock Out",
        "date": "2025-10-21",
        "time": "18:00:00"
      }
    ]
  },
  "meta": {
    "timestamp": "2025-10-22 14:35:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Employee not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 14:35:00",
    "version": "v1"
  }
}
```

---

### 3. Add Employee

Create a new employee (Admin only).

**Endpoint:** `POST /api/v1/employees/add.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Jane Smith",
  "mobile": "0771234568",
  "address": "789 Lake Rd, Galle",
  "bank_account": "BOC - 987654321",
  "role": "Employee",
  "nic": "199512345678",
  "salary": 28000.00,
  "day_salary": 1400.00,
  "password": "securepass123",
  "status": "active"
}
```

**Request Parameters:**
- `name` (required): Employee name
- `mobile` (required): Mobile number (9-10 digits)
- `nic` (required): National ID number
- `password` (required): Login password (minimum 4 characters)
- `role` (required): "Employee" or "Admin"
- `salary` (required): Monthly salary amount
- `day_salary` (required): Daily salary rate
- `address` (optional): Employee address
- `bank_account` (optional): Bank account details
- `status` (optional): "active" or "inactive" (default: active)

**Success Response (201):**
```json
{
  "success": true,
  "message": "Employee created successfully",
  "data": {
    "id": 15,
    "name": "Jane Smith",
    "mobile": 771234568,
    "address": "789 Lake Rd, Galle",
    "bank_account": "BOC - 987654321",
    "role": "Employee",
    "nic": "199512345678",
    "salary": 28000.00,
    "day_salary": 1400.00,
    "status": "active"
  },
  "meta": {
    "timestamp": "2025-10-22 15:00:00",
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
    "name": "Employee name is required",
    "mobile": "Mobile number must be 9 or 10 digits",
    "password": "Password must be at least 4 characters"
  },
  "meta": {
    "timestamp": "2025-10-22 15:00:00",
    "version": "v1"
  }
}
```

**Error Response (409):**
```json
{
  "success": false,
  "message": "Mobile number is already registered",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 15:00:00",
    "version": "v1"
  }
}
```

---

### 4. Edit Employee

Update employee information (Admin only).

**Endpoint:** `PUT /api/v1/employees/edit.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 15,
  "name": "Jane Smith Updated",
  "mobile": "0771234568",
  "address": "New Address, Colombo",
  "bank_account": "Commercial - 111222333",
  "role": "Admin",
  "nic": "199512345678",
  "salary": 35000.00,
  "day_salary": 1750.00,
  "password": "newpassword123",
  "status": "active"
}
```

**Request Parameters:**
- `id` (required): Employee ID
- `name` (required): Employee name
- `mobile` (required): Mobile number (9-10 digits)
- `nic` (required): National ID number
- `role` (required): "Employee" or "Admin"
- `salary` (required): Monthly salary amount
- `day_salary` (required): Daily salary rate
- `address` (optional): Employee address
- `bank_account` (optional): Bank account details
- `password` (optional): New password (minimum 4 characters) - only if changing
- `status` (optional): "active" or "inactive"

**Success Response (200):**
```json
{
  "success": true,
  "message": "Employee updated successfully",
  "data": {
    "id": 15,
    "name": "Jane Smith Updated",
    "mobile": 771234568,
    "address": "New Address, Colombo",
    "bank_account": "Commercial - 111222333",
    "role": "Admin",
    "nic": "199512345678",
    "salary": 35000.00,
    "day_salary": 1750.00,
    "status": "active"
  },
  "meta": {
    "timestamp": "2025-10-22 15:10:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Employee not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 15:10:00",
    "version": "v1"
  }
}
```

**Error Response (409):**
```json
{
  "success": false,
  "message": "Mobile number is already used by another employee",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 15:10:00",
    "version": "v1"
  }
}
```

---

### 5. Delete Employee

Delete or deactivate an employee (Admin only).

**Endpoint:** `DELETE /api/v1/employees/delete.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "id": 15,
  "hard_delete": false
}
```

**Request Parameters:**
- `id` (required): Employee ID
- `hard_delete` (optional): Boolean - if true, permanently deletes; if false, deactivates (default: false)

**Important Notes:**
- **Soft Delete (Deactivate)**: Sets employee status to inactive. Employee data is preserved.
- **Hard Delete**: Permanently removes employee from database. Will fail if employee has related records (attendance, invoices, etc.).
- Cannot delete your own account
- Hard delete recommended only for test/duplicate entries

**Success Response (200) - Soft Delete:**
```json
{
  "success": true,
  "message": "Employee deactivated successfully",
  "data": {
    "id": 15,
    "name": "Jane Smith",
    "action": "deactivated"
  },
  "meta": {
    "timestamp": "2025-10-22 15:20:00",
    "version": "v1"
  }
}
```

**Success Response (200) - Hard Delete:**
```json
{
  "success": true,
  "message": "Employee permanently deleted successfully",
  "data": {
    "id": 15,
    "name": "Jane Smith",
    "action": "deleted"
  },
  "meta": {
    "timestamp": "2025-10-22 15:20:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Employee not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 15:20:00",
    "version": "v1"
  }
}
```

**Error Response (409) - Hard Delete with Relations:**
```json
{
  "success": false,
  "message": "Cannot delete employee with existing records. Consider deactivating instead.",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 15:20:00",
    "version": "v1"
  }
}
```

**Error Response (400) - Self Delete:**
```json
{
  "success": false,
  "message": "Cannot delete your own account",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-22 15:20:00",
    "version": "v1"
  }
}
```

---

### 6. Get Employee Photo

Get employee profile photo (200x200px).

**Endpoint:** `GET /api/v1/employees/photo.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `id` (required): Employee ID

**Example Request:**
```http
GET /api/v1/employees/photo.php?id=5
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Employee photo retrieved successfully",
  "data": {
    "employee_id": 5,
    "employee_name": "John Employee",
    "image": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAA...",
    "mime_type": "image/jpeg",
    "size_bytes": 8542
  },
  "meta": {
    "timestamp": "2025-10-25 10:30:00",
    "version": "v1"
  }
}
```

**Response Fields:**
- `image`: Base64-encoded data URI that can be used directly in HTML `<img>` tags or mobile apps
- `mime_type`: Image MIME type (image/jpeg or image/png)
- `size_bytes`: Size of the image data in bytes

**Error Response (404) - No Photo:**
```json
{
  "success": false,
  "message": "No photo found for this employee",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:30:00",
    "version": "v1"
  }
}
```

**Error Response (404) - Employee Not Found:**
```json
{
  "success": false,
  "message": "Employee not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:30:00",
    "version": "v1"
  }
}
```

---

### 7. Upload Employee Photo

Upload or update employee profile photo. Images are automatically resized to 200x200px.

**Endpoint:** `POST /api/v1/employees/upload_photo.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: multipart/form-data
```

**Form Data:**
- `photo` (required): Image file (JPG or PNG, max 5MB)
- `employee_id` (optional): Employee ID (if not provided, uses authenticated user's ID)

**Authorization Rules:**
- **Admins** can upload photos for any employee
- **Employees** can only upload their own photo

**Image Processing:**
- Accepts JPG and PNG formats
- Maximum file size: 5MB before processing
- Automatically crops to square (centered)
- Resizes to exactly 200x200 pixels
- Maintains aspect ratio with center crop
- Preserves PNG transparency
- JPEG quality: 85%
- PNG compression: Maximum

**Example Request (cURL):**
```bash
curl -X POST https://yourdomain.com/api/v1/employees/upload_photo.php \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "photo=@/path/to/image.jpg" \
  -F "employee_id=5"
```

**Example Request (JavaScript/Fetch):**
```javascript
const formData = new FormData();
formData.append('photo', fileInput.files[0]);
formData.append('employee_id', 5);

fetch('https://yourdomain.com/api/v1/employees/upload_photo.php', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Employee photo updated successfully",
  "data": {
    "employee_id": 5,
    "employee_name": "John Employee",
    "photo_size_bytes": 8542,
    "dimensions": {
      "width": 200,
      "height": 200
    },
    "mime_type": "image/jpeg",
    "message": "Photo uploaded and resized successfully"
  },
  "meta": {
    "timestamp": "2025-10-25 10:35:00",
    "version": "v1"
  }
}
```

**Error Response (400) - No File:**
```json
{
  "success": false,
  "message": "No photo file uploaded",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:35:00",
    "version": "v1"
  }
}
```

**Error Response (400) - Invalid Type:**
```json
{
  "success": false,
  "message": "Invalid file type. Only JPG and PNG are allowed",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:35:00",
    "version": "v1"
  }
}
```

**Error Response (400) - File Too Large:**
```json
{
  "success": false,
  "message": "File size exceeds 5MB limit",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:35:00",
    "version": "v1"
  }
}
```

**Error Response (403) - Unauthorized:**
```json
{
  "success": false,
  "message": "You are not authorized to update this employee photo",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:35:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Employee not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:35:00",
    "version": "v1"
  }
}
```

---

### 8. Delete Employee Photo

Delete employee profile photo.

**Endpoint:** `DELETE /api/v1/employees/delete_photo.php` or `POST /api/v1/employees/delete_photo.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters (for DELETE):**
- `id` (optional): Employee ID (if not provided, uses authenticated user's ID)

**Request Body (for POST):**
```json
{
  "employee_id": 5
}
```

**Authorization Rules:**
- **Admins** can delete photos for any employee
- **Employees** can only delete their own photo

**Example Request (DELETE):**
```http
DELETE /api/v1/employees/delete_photo.php?id=5
Authorization: Bearer YOUR_TOKEN
```

**Example Request (POST):**
```http
POST /api/v1/employees/delete_photo.php
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "employee_id": 5
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Employee photo deleted successfully",
  "data": {
    "employee_id": 5,
    "employee_name": "John Employee",
    "message": "Photo deleted successfully"
  },
  "meta": {
    "timestamp": "2025-10-25 10:40:00",
    "version": "v1"
  }
}
```

**Error Response (404) - No Photo:**
```json
{
  "success": false,
  "message": "Employee has no photo to delete",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:40:00",
    "version": "v1"
  }
}
```

**Error Response (403) - Unauthorized:**
```json
{
  "success": false,
  "message": "You are not authorized to delete this employee photo",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:40:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Employee not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-25 10:40:00",
    "version": "v1"
  }
}
```

---

### 9. Get Commission History

Get commission history for the logged-in employee.

**Endpoint:** `GET /api/v1/employees/commission_history.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Commission history retrieved",
  "data": [
    {
      "invoice_number": 1005,
      "product_name": "Luxury Item",
      "product_profit": 1000.00,
      "commission_percentage": 5.00,
      "commission_amount": 50.00,
      "date": "2026-01-13 14:30:00"
    }
  ],
  "meta": {
    "timestamp": "2025-10-25 10:45:00",
    "version": "v1",
    "pagination": {
      "total": 50,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 3,
      "has_more": true
    },
    "total_earnings": 1550.00
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

### 2. Clock Out All Employees (Admin Only)

Clock out all currently clocked-in employees at once. This endpoint processes salary calculations for each employee based on their worked hours, exactly like the auto clock-out cron job.

**Endpoint:** `POST /api/v1/attendance/clockout_all.php`

**Access:** Admin only

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:** None required (empty JSON object `{}` or no body)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Successfully clocked out 3 out of 3 employees",
  "data": {
    "employees_clocked_out": 3,
    "total_found": 3,
    "processed_employees": [
      {
        "employee_id": 5,
        "employee_name": "John Smith",
        "worked_hours": 8.5,
        "salary_paid": 1500.00,
        "status": "success"
      },
      {
        "employee_id": 7,
        "employee_name": "Jane Doe",
        "worked_hours": 7.2,
        "salary_paid": 1350.00,
        "status": "success"
      },
      {
        "employee_id": 12,
        "employee_name": "Bob Wilson",
        "worked_hours": 9.0,
        "salary_paid": 1600.00,
        "status": "success"
      }
    ]
  },
  "meta": {
    "timestamp": "2025-10-24 18:00:00",
    "version": "v1"
  }
}
```

**Success Response (200) - No Active Employees:**
```json
{
  "success": true,
  "message": "No active employees found to clock out",
  "data": {
    "employees_clocked_out": 0
  },
  "meta": {
    "timestamp": "2025-10-24 18:00:00",
    "version": "v1"
  }
}
```

**Success Response with Partial Errors (200):**
```json
{
  "success": true,
  "message": "Successfully clocked out 2 out of 3 employees",
  "data": {
    "employees_clocked_out": 2,
    "total_found": 3,
    "processed_employees": [
      {
        "employee_id": 5,
        "employee_name": "John Smith",
        "worked_hours": 8.5,
        "salary_paid": 1500.00,
        "status": "success"
      },
      {
        "employee_id": 7,
        "employee_name": "Jane Doe",
        "worked_hours": 7.2,
        "salary_paid": 1350.00,
        "status": "success"
      }
    ],
    "errors": [
      {
        "employee_id": 12,
        "employee_name": "Bob Wilson",
        "error": "No Clock In Record Found for employee Bob Wilson"
      }
    ]
  },
  "meta": {
    "timestamp": "2025-10-24 18:00:00",
    "version": "v1"
  }
}
```

**Error Response (403) - Non-Admin Access:**
```json
{
  "success": false,
  "message": "Admin access required",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-24 18:00:00",
    "version": "v1"
  }
}
```

**How it Works:**
1. Queries all employees with `is_clocked_in = 1`
2. For each employee:
   - Inserts a "Clock Out" record in the `attendance` table
   - Updates `is_clocked_in = 0` in the `employees` table
   - Calculates worked hours from today's clock in/out times
   - Calculates and pays salary based on hours worked:
     - **Full Day**: 8-17 hours → Full day salary
     - **Partial Day**: < 8 hours → Hourly rate × hours worked
     - **Overtime**: > 17 hours → Full day salary (logged as overtime)
   - Adds salary record to the `salary` table
   - Updates employee's salary balance in `employees` table
   - Deducts salary from "Company Profit" account
3. Returns detailed results for each employee processed

**Important Notes:**
- This endpoint uses the exact same logic as the auto clock-out cron job
- Only processes employees who are currently clocked in (`is_clocked_in = 1`)
- Salary calculations are based on the employee's `day_salary` setting
- If an employee has no salary configured, they are still clocked out but no payment is processed
- All database operations are logged in the transaction log
- Partial failures are handled gracefully - successful clock-outs are still processed

**Use Cases:**
- End of day when admin wants to clock out all remaining staff
- Emergency situations requiring immediate clock out of all employees
- Manual intervention when cron job fails or needs to run early
- Testing salary calculations for all employees

---

### 3. Get Attendance Status

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

Process employee salary payment. Deducts from employee salary balance and account balance.

**Endpoint:** `POST /api/v1/salary/pay.php`

**Access:** Admin only

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
  "account": "cash_in_hand",
  "description": "Monthly Salary - October 2025"
}
```

**Field Descriptions:**
- `employee_id` (required, integer): Employee ID to pay salary to
- `amount` (required, number): Amount to pay (must be positive and not exceed employee's salary balance)
- `account` (required, string): Account name to deduct from (must exist with sufficient funds)
  - Available accounts: `cash_in_hand`, `DFCC` 
  - **Important:** Account name is case-sensitive and must match exactly
- `description` (optional, string): Payment description (defaults to "Salary Paid")

**Validations:**
- Amount must be greater than zero
- Employee must exist in the system
- Employee must have sufficient salary balance
- Account must exist with sufficient funds
- Only Admin users can process payments

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
    "account": "cash_in_hand",
    "description": "Monthly Salary - October 2025",
    "paid_by": {
      "id": 1,
      "name": "admin"
    },
    "paid_at": "2025-10-24 16:30:00"
  },
  "meta": {
    "timestamp": "2025-10-24 16:30:00",
    "version": "v1"
  }
}
```

**Error Responses:**

**403 Forbidden - Non-admin access:**
```json
{
  "success": false,
  "message": "Admin access required",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-24 16:30:00",
    "version": "v1"
  }
}
```

**404 Not Found - Employee not found:**
```json
{
  "success": false,
  "message": "Employee not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-24 16:30:00",
    "version": "v1"
  }
}
```

**404 Not Found - Account not found:**
```json
{
  "success": false,
  "message": "Account not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-24 16:30:00",
    "version": "v1"
  }
}
```

**422 Unprocessable Entity - Insufficient salary balance:**
```json
{
  "success": false,
  "message": "Insufficient salary balance. Available: Rs. 10000.00",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-24 16:30:00",
    "version": "v1"
  }
}
```

**422 Unprocessable Entity - Insufficient account funds:**
```json
{
  "success": false,
  "message": "Insufficient funds in account. Available: Rs. 5000.00",
  "errors": [],
  "meta": {
    "timestamp": "2025-10-24 16:30:00",
    "version": "v1"
  }
}
```

**422 Unprocessable Entity - Validation errors:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "employee_id": "Employee id is required",
    "amount": "Amount is required",
    "account": "Account is required"
  },
  "meta": {
    "timestamp": "2025-10-24 16:30:00",
    "version": "v1"
  }
}
```

**What happens when salary is paid:**
1. Record is added to `salary` table with negative amount
2. Employee's salary balance is decreased
3. Account balance is decreased
4. Transaction is logged in `transaction_log` table
5. All changes are atomic (transaction-based)

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

## GRN Endpoints

**Admin Access Required** for all GRN (Goods Received Notes) endpoints.

GRN endpoints allow you to manage goods received from suppliers, including creating new GRN records, viewing existing ones, and tracking payment status. All endpoints are self-contained in the API and don't require access to the purchase folder.

---

### 1. List GRNs

Get a paginated list of Goods Received Notes with filtering options.

**Endpoint:** `GET /api/v1/grn/list.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20, max: 100)
- `search` (optional): Search by GRN number, invoice number, supplier name, or PO number
- `supplier_id` (optional): Filter by supplier ID
- `status` (optional): Filter by status (`draft`, `completed`, `cancelled`)
- `payment_status` (optional): Filter by payment status (`paid`, `partial`, `unpaid`)
- `date_from` (optional): Filter GRNs from this date (YYYY-MM-DD)
- `date_to` (optional): Filter GRNs up to this date (YYYY-MM-DD)

**Example Request:**
```http
GET /api/v1/grn/list.php?page=1&per_page=20&payment_status=unpaid&supplier_id=5
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "GRN list retrieved successfully",
  "data": [
    {
      "id": 45,
      "grn_number": "GRN-20251205-0001",
      "receipt_date": "2025-12-05",
      "invoice_number": "INV-2025-12345",
      "invoice_date": "2025-12-04",
      "status": "completed",
      "total_amount": 150000.00,
      "paid_amount": 100000.00,
      "outstanding_amount": 50000.00,
      "payment_status": "partial",
      "payment_method": "bank_transfer",
      "notes": "First batch of December stock",
      "created_at": "2025-12-05 10:30:00",
      "po_id": 12,
      "po_number": "PO-20251201-0003",
      "supplier": {
        "id": 5,
        "name": "ABC Paper Supplies",
        "mobile": "0771234567"
      },
      "created_by": "Admin User",
      "item_count": 15
    }
  ],
  "meta": {
    "timestamp": "2025-12-05 11:00:00",
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

### 2. GRN Details

Get detailed information about a specific GRN including all items.

**Endpoint:** `GET /api/v1/grn/details.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Query Parameters:**
- `id` (required): GRN ID

**Example Request:**
```http
GET /api/v1/grn/details.php?id=45
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "GRN details retrieved successfully",
  "data": {
    "id": 45,
    "grn_number": "GRN-20251205-0001",
    "receipt_date": "2025-12-05",
    "invoice_number": "INV-2025-12345",
    "invoice_date": "2025-12-04",
    "status": "completed",
    "notes": "First batch of December stock",
    "purchase_order": {
      "id": 12,
      "po_number": "PO-20251201-0003",
      "order_date": "2025-12-01"
    },
    "supplier": {
      "id": 5,
      "name": "ABC Paper Supplies",
      "mobile": "0771234567",
      "address": "Colombo 03"
    },
    "payment": {
      "total_amount": 150000.00,
      "paid_amount": 100000.00,
      "outstanding_amount": 50000.00,
      "payment_status": "partial",
      "payment_method": "bank_transfer",
      "payment_reference": "TRN-20251205-001",
      "payment_notes": "Partial payment via bank transfer"
    },
    "items": [
      {
        "grn_item_id": 201,
        "batch_id": 501,
        "batch_number": "BATCH-2025-001",
        "product": {
          "id": 101,
          "name": "A4 Paper - White",
          "sku": "PAPER-A4-W",
          "barcode": "1234567890123",
          "description": "Premium white A4 paper"
        },
        "received_qty": 500.0,
        "cost": 250.00,
        "selling_price": 300.00,
        "expiry_date": null,
        "notes": null,
        "total_cost": 125000.00,
        "total_selling_value": 150000.00
      },
      {
        "grn_item_id": 202,
        "batch_id": 502,
        "batch_number": "BATCH-2025-002",
        "product": {
          "id": 102,
          "name": "Printer Ink - Black",
          "sku": "INK-BLK-001",
          "barcode": "1234567890124",
          "description": "Black printer ink cartridge"
        },
        "received_qty": 100.0,
        "cost": 250.00,
        "selling_price": 350.00,
        "expiry_date": "2027-12-31",
        "notes": "Handle with care",
        "total_cost": 25000.00,
        "total_selling_value": 35000.00
      }
    ],
    "summary": {
      "item_count": 2,
      "total_quantity": 600.0,
      "calculated_total": 150000.00
    },
    "created_by": {
      "id": 1,
      "name": "Admin User"
    },
    "created_at": "2025-12-05 10:30:00",
    "updated_at": "2025-12-05 10:30:00"
  },
  "meta": {
    "timestamp": "2025-12-05 11:15:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "GRN not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-12-05 11:15:00",
    "version": "v1"
  }
}
```

---

### 3. Create GRN

Create a new Goods Received Note with items.

**Endpoint:** `POST /api/v1/grn/create.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "supplier_id": 5,
  "receipt_date": "2025-12-05",
  "invoice_number": "INV-2025-12345",
  "invoice_date": "2025-12-04",
  "po_id": 12,
  "notes": "First batch of December stock",
  "status": "completed",
  "payment_data": {
    "payment_status": "partial",
    "paid_amount": 100000.00,
    "payment_method": "bank_transfer",
    "payment_reference": "TRN-20251205-001"
  },
  "items": [
    {
      "product_id": 101,
      "received_qty": 500.0,
      "cost": 250.00,
      "selling_price": 300.00,
      "batch_data": {
        "isNew": true,
        "batchNumber": "BATCH-2025-001"
      },
      "expiry_date": null
    },
    {
      "product_id": 102,
      "received_qty": 100.0,
      "cost": 250.00,
      "selling_price": 350.00,
      "batch_data": {
        "isNew": false,
        "batchId": 502
      },
      "expiry_date": "2027-12-31"
    }
  ]
}
```

**Field Descriptions:**

**Required Fields:**
- `supplier_id` (integer): ID of the supplier
- `receipt_date` (string): Date goods were received (YYYY-MM-DD)
- `items` (array): Array of items received (minimum 1 item)
  - `product_id` (integer): Product ID
  - `received_qty` (number): Quantity received (must be > 0)
  - `cost` (number): Cost price per unit
  - `selling_price` (number): Selling price per unit

**Optional Fields:**
- `invoice_number` (string): Supplier invoice number
- `invoice_date` (string): Supplier invoice date (YYYY-MM-DD)
- `po_id` (integer): Related purchase order ID
- `notes` (string): General notes for the GRN
- `status` (string): GRN status (`draft`, `completed`, `cancelled`). Default: `completed`
- `payment_data` (object): Payment information
  - `payment_status` (string): `paid`, `partial`, `unpaid`
  - `paid_amount` (number): Amount paid
  - `payment_method` (string): `cash`, `bank_transfer`, `cheque`, `credit_card`
  - `payment_reference` (string): Transaction/reference number

**Item Optional Fields:**
- `batch_data` (object): Batch information
  - `isNew` (boolean): True to create new batch, false to restock existing
  - `batchNumber` (string): Custom batch number (for new batches)
  - `batchId` (integer): Existing batch ID (for restocking)
- `batch_number` (string): Legacy batch number field (deprecated, use batch_data)
- `expiry_date` (string): Expiry date (YYYY-MM-DD)

**Success Response (201):**
```json
{
  "success": true,
  "message": "GRN created successfully",
  "data": {
    "id": 45,
    "grn_number": "GRN-20251205-0001",
    "receipt_date": "2025-12-05",
    "invoice_number": "INV-2025-12345",
    "status": "completed",
    "total_amount": 150000.00,
    "paid_amount": 100000.00,
    "payment_status": "partial",
    "supplier": {
      "id": 5,
      "name": "ABC Paper Supplies",
      "mobile": "0771234567"
    },
    "created_by": "Admin User",
    "created_at": "2025-12-05 10:30:00"
  },
  "meta": {
    "timestamp": "2025-12-05 10:30:00",
    "version": "v1"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Missing required fields: supplier_id, items",
  "errors": [],
  "meta": {
    "timestamp": "2025-12-05 10:30:00",
    "version": "v1"
  }
}
```

**Validation Rules:**
- At least one item is required
- `received_qty` must be greater than 0
- Prices cannot be negative
- Supplier ID must exist in the database
- Product IDs must exist in the database
- If `batch_data.isNew = true`, a new batch is created
- If `batch_data.isNew = false`, existing batch quantity is updated
- Auto-generated batch numbers are unique (format: B-YYYYMMDD-PRODUCT_ID-###)
- Payment status is automatically calculated if not provided

**Notes:**
- GRN number is auto-generated in format: `GRN-YYYYMMDD-####`
- Total amount is calculated from items: `sum(received_qty * cost)`
- Each item updates or creates a product batch
- All operations are transactional (all or nothing)

---

### 4. Update GRN

Update an existing Goods Received Note (payment and notes only).

**Endpoint:** `PUT /api/v1/grn/update.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "grn_id": 45,
  "invoice_number": "INV-2025-12345-UPDATED",
  "invoice_date": "2025-12-04",
  "notes": "Updated notes",
  "payment_status": "paid",
  "paid_amount": 150000.00,
  "payment_method": "cash",
  "payment_reference": "CASH-20251205-001"
}
```

**Field Descriptions:**
- `grn_id` (required, integer): GRN ID to update
- `invoice_number` (optional, string): Update invoice number
- `invoice_date` (optional, string): Update invoice date
- `notes` (optional, string): Update notes
- `payment_status` (optional, string): `paid`, `partial`, `unpaid`
- `paid_amount` (optional, number): Update paid amount
- `payment_method` (optional, string): Update payment method
- `payment_reference` (optional, string): Update payment reference

**Success Response (200):**
```json
{
  "success": true,
  "message": "GRN updated successfully",
  "data": {
    "grn": {
      "grn_id": 45,
      "grn_number": "GRN-20251205-0001",
      "receipt_date": "2025-12-05",
      "invoice_number": "INV-2025-12345-UPDATED",
      "invoice_date": "2025-12-04",
      "total_amount": 150000.00,
      "paid_amount": 150000.00,
      "payment_status": "paid",
      "payment_method": "cash",
      "payment_reference": "CASH-20251205-001",
      "notes": "Updated notes",
      "status": "completed",
      "supplier": {
        "supplier_id": 5,
        "supplier_name": "ABC Paper Supplies",
        "supplier_tel": "0771234567",
        "supplier_address": "Colombo 03"
      },
      "created_at": "2025-12-05 10:30:00",
      "updated_at": "2025-12-05 14:45:00"
    }
  },
  "meta": {
    "timestamp": "2025-12-05 14:45:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "GRN not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-12-05 14:45:00",
    "version": "v1"
  }
}
```

---

### 5. Delete GRN

Cancel/Delete a Goods Received Note (marks as cancelled and reverses stock).

**Endpoint:** `DELETE /api/v1/grn/delete.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "grn_id": 45
}
```

**Or via Query String:**
```http
DELETE /api/v1/grn/delete.php?grn_id=45
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "GRN cancelled successfully",
  "data": {
    "grn_id": 45
  },
  "meta": {
    "timestamp": "2025-12-05 15:00:00",
    "version": "v1"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "GRN not found",
  "errors": [],
  "meta": {
    "timestamp": "2025-12-05 15:00:00",
    "version": "v1"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "GRN is already cancelled",
  "errors": [],
  "meta": {
    "timestamp": "2025-12-05 15:00:00",
    "version": "v1"
  }
}
```

**Notes:**
- Deleting/cancelling a GRN reverses all stock quantities
- Batch quantities are decreased by the received quantity
- This operation is transactional

---

### 6. Search Products for GRN

Search products to add to GRN with batch information.

**Endpoint:** `GET /api/v1/grn/search_products.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Query Parameters:**
- `q` (optional): Search term (product name, SKU, or barcode)
- `type` (optional): Product type filter. Values: `standard`, `all`. Default: `standard`
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 20, max: 100)

**Example Request:**
```http
GET /api/v1/grn/search_products.php?q=paper&type=standard&page=1&limit=20
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "products": [
      {
        "product_id": 101,
        "product_name": "A4 Paper - White",
        "sku": "PAPER-A4-W",
        "barcode": "1234567890123",
        "product_type": "standard",
        "cost": 250.00,
        "selling_price": 300.00,
        "current_stock": 1500.0,
        "stock_alert_limit": 100.0
      },
      {
        "product_id": 102,
        "product_name": "A4 Paper - Color",
        "sku": "PAPER-A4-C",
        "barcode": "1234567890124",
        "product_type": "standard",
        "cost": 280.00,
        "selling_price": 350.00,
        "current_stock": 800.0,
        "stock_alert_limit": 50.0
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 45,
      "total_pages": 3,
      "has_more": true
    }
  },
  "meta": {
    "timestamp": "2025-12-05 11:30:00",
    "version": "v1"
  }
}
```

---

### 7. Get Product Batches

Get all batches for a specific product.

**Endpoint:** `GET /api/v1/grn/get_product_batches.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Query Parameters:**
- `product_id` (required): Product ID

**Example Request:**
```http
GET /api/v1/grn/get_product_batches.php?product_id=101
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "batches": [
      {
        "batch_id": 501,
        "batch_number": "BATCH-2025-001",
        "cost": 250.00,
        "selling_price": 300.00,
        "quantity": 500.0,
        "expiry_date": null,
        "alert_quantity": 50,
        "status": "active",
        "discount_price": null,
        "created_at": "2025-12-01 10:00:00"
      },
      {
        "batch_id": 502,
        "batch_number": "BATCH-2025-002",
        "cost": 245.00,
        "selling_price": 300.00,
        "quantity": 1000.0,
        "expiry_date": "2026-12-31",
        "alert_quantity": 100,
        "status": "active",
        "discount_price": 280.00,
        "created_at": "2025-11-15 09:30:00"
      }
    ],
    "total": 2
  },
  "meta": {
    "timestamp": "2025-12-05 11:45:00",
    "version": "v1"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Product ID is required",
  "errors": [],
  "meta": {
    "timestamp": "2025-12-05 11:45:00",
    "version": "v1"
  }
}
```

---

### 8. Get Suppliers

Get all suppliers or search suppliers for GRN.

**Endpoint:** `GET /api/v1/grn/get_suppliers.php`

**Headers:**
```http
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Query Parameters:**
- `search` (optional): Search term (supplier name, phone, or email)
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 50, max: 100)

**Example Request:**
```http
GET /api/v1/grn/get_suppliers.php?search=ABC&page=1&limit=20
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "suppliers": [
      {
        "supplier_id": 5,
        "supplier_name": "ABC Paper Supplies",
        "supplier_tel": "0771234567",
        "supplier_email": "abc@example.com",
        "supplier_address": "No. 123, Galle Road",
        "supplier_city": "Colombo 03",
        "supplier_country": "Sri Lanka"
      },
      {
        "supplier_id": 8,
        "supplier_name": "ABC Stationery Ltd",
        "supplier_tel": "0777654321",
        "supplier_email": "info@abcstationery.com",
        "supplier_address": "456 Main Street",
        "supplier_city": "Kandy",
        "supplier_country": "Sri Lanka"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 2,
      "total_pages": 1,
      "has_more": false
    }
  },
  "meta": {
    "timestamp": "2025-12-05 12:00:00",
    "version": "v1"
  }
}
```

---

## GRN Workflow for Mobile App

### Complete Flow:

1. **Get Suppliers** → `GET /api/v1/grn/get_suppliers.php`
2. **Search Products** → `GET /api/v1/grn/search_products.php?type=standard`
3. **Get Product Batches** (if restocking) → `GET /api/v1/grn/get_product_batches.php?product_id=X`
4. **Create GRN** → `POST /api/v1/grn/create.php`
5. **View GRN List** → `GET /api/v1/grn/list.php`
6. **View GRN Details** → `GET /api/v1/grn/details.php?id=X`
7. **Update Payment** (optional) → `PUT /api/v1/grn/update.php`
8. **Cancel GRN** (if needed) → `DELETE /api/v1/grn/delete.php`

### Mobile App Implementation Tips:

1. **Cache Suppliers**: Load suppliers once and cache locally
2. **Debounce Product Search**: Wait 300ms after typing stops before searching
3. **Batch Selection UI**: Show radio buttons for "New Batch" vs "Restock Existing"
4. **Auto-fill Prices**: When selecting existing batch, auto-fill cost and selling price
5. **Calculate Totals**: Show running total as items are added
6. **Payment Status**: Auto-calculate payment status based on paid amount
7. **Offline Support**: Save draft GRNs locally and sync when online
8. **Validation**: Validate all required fields before submission
9. **Error Handling**: Show user-friendly error messages
10. **Success Feedback**: Show success message and GRN number after creation

---

## Expenses Endpoints

### 1. categories Management

#### 1.1 Get All Categories

**Endpoint:** `GET /api/v1/expenses/categories.php`

Retrieves all active expense categories.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": [
    {
      "category_id": 1,
      "category_name": "Rent",
      "description": "Monthly office/shop rent payments",
      "color_code": "#FF6B6B",
      "icon": "home",
      "status": 1,
      "created_at": "2025-01-01 10:00:00"
    },
    {
      "category_id": 2,
      "category_name": "Utilities",
      "description": "Electricity, water, internet bills",
      "color_code": "#4ECDC4",
      "icon": "bolt",
      "status": 1,
      "created_at": "2025-01-01 10:00:00"
    }
  ],
  "meta": {
    "total_count": 10
  }
}
```

#### 1.2 Add New Category

**Endpoint:** `POST /api/v1/expenses/add_category.php`

Creates a new expense category.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "category_name": "Insurance",
  "description": "Insurance premiums and coverage",
  "color_code": "#9B59B6",
  "icon": "shield-alt"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "category_id": 11,
    "category_name": "Insurance",
    "description": "Insurance premiums and coverage",
    "color_code": "#9B59B6",
    "icon": "shield-alt",
    "status": 1,
    "created_at": "2025-12-06 14:30:00"
  }
}
```

### 2. Expense Management

#### 2.1 Add Expense

**Endpoint:** `POST /api/v1/expenses/add.php`

Creates a new expense (one-time or recurring).

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body (One-Time Expense):**
```json
{
  "expense_type": "one_time",
  "title": "Office Supplies - Printer Paper",
  "amount": 5500.00,
  "category_id": 5,
  "expense_date": "2025-12-06 10:30:00",
  "payment_method": "bank_transfer",
  "status": "unpaid",
  "reference_no": "INV-2025-001",
  "notes": "Bulk purchase for quarter"
}
```

**Request Body (Recurring Expense):**
```json
{
  "expense_type": "recurring",
  "title": "Monthly Office Rent",
  "amount": 50000.00,
  "category_id": 1,
  "frequency": "monthly",
  "start_date": "2025-12-01",
  "payment_method": "bank_transfer",
  "remind_days_before": 5,
  "end_date": null
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Expense created successfully",
  "data": {
    "expense_id": 156,
    "reference_no": "INV-2025-001",
    "title": "Office Supplies - Printer Paper",
    "amount": 5500.00,
    "amount_paid": 0.00,
    "remaining_amount": 5500.00,
    "category_id": 5,
    "expense_date": "2025-12-06 10:30:00",
    "payment_method": "bank_transfer",
    "status": "unpaid",
    "created_at": "2025-12-06 14:35:00"
  }
}
```

#### 2.2 List Expenses

**Endpoint:** `GET /api/v1/expenses/list.php`

Retrieves expenses with advanced filtering and pagination.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**

- `start_date`: Filter from date (Y-m-d)
- `end_date`: Filter to date (Y-m-d)
- `category_id`: Filter by category
- `status`: 'paid', 'partial', 'unpaid', 'overdue'
- `payment_method`: Filter by payment method
- `search`: Search in title or reference_no
- `limit`: Records per page (default: 50, max: 500)
- `offset`: Pagination offset (default: 0)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Expenses retrieved successfully",
  "data": [
    {
      "expense_id": 145,
      "reference_no": "RENT-2025-12",
      "title": "Monthly Office Rent - December",
      "amount": 50000.00,
      "amount_paid": 30000.00,
      "remaining_amount": 20000.00,
      "payment_percentage": 60.00,
      "category": {
        "category_id": 1,
        "category_name": "Rent",
        "color_code": "#FF6B6B",
        "icon": "home"
      },
      "expense_date": "2025-12-01 00:00:00",
      "payment_method": "bank_transfer",
      "status": "partial",
      "is_recurring": true,
      "recurring_ref_id": 5,
      "attachment_url": null,
      "notes": "Monthly rent for main office",
      "created_by": {
        "id": 1,
        "name": "Admin User"
      },
      "created_at": "2025-12-01 08:00:00"
    }
  ],
  "meta": {
    "total_count": 48,
    "returned_count": 25,
    "limit": 25,
    "offset": 0,
    "has_more": true
  }
}
```

#### 2.3 Update Expense

**Endpoint:** `PUT /api/v1/expenses/update.php`

Updates an existing expense.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "expense_id": 156,
  "title": "Office Supplies - Printer Paper & Toner",
  "amount": 6500.00,
  "category_id": 5,
  "payment_method": "cash",
  "notes": "Added toner cartridges"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Expense updated successfully",
  "data": {
    "expense_id": 156,
    "title": "Office Supplies - Printer Paper & Toner",
    "amount": 6500.00,
    "updated_at": "2025-12-06 15:00:00"
  }
}
```

#### 2.4 Delete Expense

**Endpoint:** `DELETE /api/v1/expenses/delete.php`

Deletes an expense and all associated payment records.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "expense_id": 156
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Expense deleted successfully"
}
```

### 3. Recurring Expenses

#### 3.1 List Recurring Expenses

**Endpoint:** `GET /api/v1/expenses/recurring.php`

Retrieves all active recurring expense templates.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Recurring expenses retrieved successfully",
  "data": [
    {
      "recurring_id": 5,
      "title": "Monthly Office Rent",
      "amount": 50000.00,
      "category": {
        "category_id": 1,
        "category_name": "Rent",
        "color_code": "#FF6B6B",
        "icon": "home"
      },
      "frequency": "monthly",
      "start_date": "2025-01-01",
      "next_due_date": "2026-01-01",
      "end_date": null,
      "payment_method": "bank_transfer",
      "remind_days_before": 5,
      "is_active": true,
      "created_at": "2025-01-01 08:00:00"
    }
  ]
}
```

#### 3.2 Pay Recurring Expense

**Endpoint:** `POST /api/v1/expenses/pay_recurring.php`

Creates an expense record from a recurring template and updates next due date.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "recurring_id": 5,
  "payment_date": "2025-12-06",
  "payment_method": "bank_transfer",
  "reference_no": "RENT-DEC-2025",
  "notes": "December rent payment"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Recurring payment recorded successfully",
  "data": {
    "expense_id": 157,
    "title": "Monthly Office Rent",
    "amount": 50000.00,
    "recurring_ref_id": 5,
    "next_due_date": "2026-01-01"
  }
}
```

### 4. Payment Tracking

#### 4.1 Add Payment

**Endpoint:** `POST /api/v1/expenses/add_payment.php`

Records a partial or full payment against an expense.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "expense_id": 145,
  "payment_amount": 15000.00,
  "payment_date": "2025-12-06",
  "payment_method": "bank_transfer",
  "reference_no": "TXN-20251206-001",
  "notes": "Second installment payment"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Payment recorded successfully",
  "data": {
    "payment_id": 78,
    "expense_id": 145,
    "payment_amount": 15000.00,
    "total_amount": 50000.00,
    "amount_paid": 45000.00,
    "remaining_amount": 5000.00,
    "payment_percentage": 90.00,
    "status": "partial",
    "payment_date": "2025-12-06 00:00:00"
  }
}
```

#### 4.2 Get Payment History

**Endpoint:** `GET /api/v1/expenses/payment_history.php`

Retrieves all payment transactions for a specific expense.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `expense_id`: ID of the expense (required)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Payment history retrieved successfully",
  "data": {
    "expense": {
      "expense_id": 145,
      "title": "Monthly Office Rent - December",
      "total_amount": 50000.00,
      "amount_paid": 45000.00,
      "remaining_amount": 5000.00,
      "payment_percentage": 90.00,
      "status": "partial"
    },
    "payments": [
      {
        "payment_id": 75,
        "payment_amount": 20000.00,
        "payment_date": "2025-12-01 00:00:00",
        "payment_method": "bank_transfer",
        "reference_no": "TXN-001",
        "notes": "First installment",
        "created_by": {
          "id": 1,
          "name": "Admin User"
        },
        "created_at": "2025-12-01 10:00:00"
      }
    ],
    "summary": {
      "total_payments": 1,
      "total_paid": 20000.00,
      "remaining": 30000.00,
      "is_fully_paid": false
    }
  }
}
```

### 5. Dashboard & Analytics

#### 5.1 Get Dashboard Summary

**Endpoint:** `GET /api/v1/expenses/summary.php`

Retrieves comprehensive dashboard data including totals, category breakdown, and payment status overview.

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Dashboard summary retrieved successfully",
  "data": {
    "totals": {
      "current_month": 125000.00,
      "current_month_paid": 95000.00,
      "current_month_unpaid": 30000.00,
      "last_month": 108500.00,
      "year_to_date": 850000.00
    },
    "category_breakdown": [
      {
        "category_id": 1,
        "category_name": "Rent",
        "color_code": "#FF6B6B",
        "icon": "home",
        "total_amount": 50000.00,
        "transaction_count": 1,
        "percentage": 40.00
      }
    ]
  }
}
```

---

---

## Settings Endpoints

### 1. Get System Settings

Get global system settings including feature flags.

**Endpoint:** `GET /api/v1/settings/get.php`

**Headers:**
```http
Authorization: Bearer YOUR_TOKEN
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Settings retrieved successfully",
  "data": {
    "employee_commission_enabled": true,
    "sell_Insufficient_stock_item": true,
    "sell_Inactive_batch_products": true
  },
  "meta": {
    "timestamp": "2025-10-25 10:45:00",
    "version": "v1"
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

*Last Updated: December 5, 2025*
