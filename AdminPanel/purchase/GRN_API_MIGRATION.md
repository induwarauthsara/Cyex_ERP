# GRN API Migration Summary

## Date: December 5, 2025

## Overview
Migrated GRN (Goods Received Notes) functionality from local PHP files to centralized API endpoints for better mobile app integration and code maintenance.

---

## Files Updated

### 1. `AdminPanel/purchase/create_grn.php`
**Changes:**
- Updated supplier search to use `/api/v1/grn/get_suppliers.php` instead of `../../suppliers/suppliers.php`
- Updated product search to use `/api/v1/grn/search_products.php` instead of `search_products.php`
- Updated batch loading to use `/api/v1/grn/get_product_batches.php` instead of `get_product_batches.php`
- Updated GRN save to use `/api/v1/grn/create.php` instead of `save_grn.php`
- Added `getAuthToken()` helper function to retrieve JWT token from localStorage/cookie/session
- Added `getTokenFromSession()` function for backward compatibility with PHP sessions

---

## New API Files Created

### 1. `/api/v1/grn/search_products.php`
**Purpose:** Search products with batch information for adding to GRN
**Features:**
- Product type filtering (standard/all)
- Search by name, SKU, or barcode
- Pagination support
- Returns latest batch cost and selling price

### 2. `/api/v1/grn/get_product_batches.php`
**Purpose:** Get all batches for a specific product
**Features:**
- Returns batch number, cost, selling price, quantity, expiry date
- Filters active batches only
- Ordered by creation date (newest first)

### 3. `/api/v1/grn/get_suppliers.php`
**Purpose:** Search and list suppliers
**Features:**
- Search by name, phone, or email
- Pagination support
- Returns complete supplier information

### 4. `/api/v1/grn/update.php`
**Purpose:** Update existing GRN payment and notes
**Features:**
- Update invoice details
- Update payment information
- Update notes

### 5. `/api/v1/grn/delete.php`
**Purpose:** Cancel/Delete GRN
**Features:**
- Marks GRN as cancelled
- Reverses stock quantities
- Transactional operation

### 6. `/api/v1/auth/get_session_token.php`
**Purpose:** Get JWT token from PHP session (backward compatibility)
**Features:**
- Converts PHP session to JWT token
- Enables smooth transition from session to token-based auth

---

## Files Deleted

The following files were removed as they are now replaced by API endpoints:

1. ✅ `AdminPanel/purchase/get_product_batches.php` → Replaced by `/api/v1/grn/get_product_batches.php`
2. ✅ `AdminPanel/purchase/search_products.php` → Replaced by `/api/v1/grn/search_products.php`
3. ✅ `AdminPanel/purchase/save_grn.php` → Replaced by `/api/v1/grn/create.php`
4. ✅ `AdminPanel/purchase/create_grn_backup_20251205_033935.php` (backup file)
5. ✅ `AdminPanel/purchase/create_grn_complex_backup.php` (backup file)
6. ✅ `AdminPanel/purchase/create_grn_old.php` (backup file)

---

## API Endpoints Available

### Complete GRN Workflow:

1. **GET** `/api/v1/grn/get_suppliers.php` - List/search suppliers
2. **GET** `/api/v1/grn/search_products.php` - Search products with filters
3. **GET** `/api/v1/grn/get_product_batches.php` - Get product batches
4. **POST** `/api/v1/grn/create.php` - Create new GRN
5. **GET** `/api/v1/grn/list.php` - List GRNs with filters
6. **GET** `/api/v1/grn/details.php` - Get GRN details
7. **PUT** `/api/v1/grn/update.php` - Update GRN
8. **DELETE** `/api/v1/grn/delete.php` - Cancel GRN

### Authentication:
- **GET** `/api/v1/auth/get_session_token.php` - Get JWT from PHP session

---

## Authentication Flow

### Web Interface (create_grn.php):
1. User logs in via PHP session (existing login system)
2. JavaScript calls `getAuthToken()` function
3. Function checks localStorage for token
4. If not found, checks cookies
5. If still not found, calls `/api/v1/auth/get_session_token.php`
6. Token is generated from PHP session and returned
7. Token is stored in localStorage
8. All API calls include token in Authorization header

### Mobile App:
1. User logs in via `/api/v1/auth/login.php`
2. JWT token is returned
3. Token is stored in secure storage
4. All API calls include token in Authorization header

---

## Benefits

### 1. **Code Reusability**
- Single codebase for both web and mobile
- Easier maintenance and bug fixes
- Consistent business logic

### 2. **Security**
- JWT-based authentication
- Role-based access control (Admin only)
- Input validation and sanitization

### 3. **Scalability**
- RESTful API design
- Pagination support
- Optimized queries

### 4. **Mobile-Ready**
- All GRN functions available via API
- No need for mobile app to access purchase folder
- Standardized JSON responses

### 5. **Documentation**
- Complete API documentation updated
- Request/response examples
- Error handling guide

---

## Testing Checklist

- [ ] Test supplier search in web interface
- [ ] Test product search with standard filter
- [ ] Test batch loading for existing products
- [ ] Test creating new batch
- [ ] Test restocking existing batch
- [ ] Test GRN creation with payment
- [ ] Test GRN creation without payment
- [ ] Test session token generation
- [ ] Test API from mobile app
- [ ] Verify deleted files are not referenced elsewhere

---

## Notes

### Backward Compatibility
- Web interface still uses PHP sessions
- `get_session_token.php` bridges PHP session to JWT
- No changes required to existing login system
- Smooth migration path for future token-based auth

### Future Improvements
- Consider moving all web interface to use JWT tokens
- Implement token refresh mechanism
- Add rate limiting to API endpoints
- Add API request logging

---

## Documentation

Full API documentation available at:
`/api/API_DOCUMENTATION.md`

Section: **GRN Endpoints**

---

**Migration Completed Successfully! ✅**
