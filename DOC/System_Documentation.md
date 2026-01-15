# Srijaya ERP System Documentation

**Date:** 2025-12-12
**System Version:** 1.0 (Inferred)
**Tech Stack:** PHP (Vanilla), MySQL, JavaScript (jQuery), HTML/CSS.

---

## 1. Executive Summary & Big Picture

*Address Point 1: Get full big picture idea about system all files, sections, functions, codes.*

The **Srijaya ERP System** is a Monolithic PHP application serving as a Point of Sale (POS), Inventory Manager, and HR System.
-   **Core Architecture**: The application runs on a standard LAMP/WAMP stack. The POS interface (`index.php`) is the main entry point, built with HTML/jQuery. The backend logic resides in `AdminPanel/` (Web Admin) and `api/v1/` (Mobile/External API).
-   **Data FLow**:
    -   **POS**: `index.php` -> JS AJAX -> `inc/fetch_product.php` / `invoice/*.php` -> MySQL Database.
    -   **Admin**: `AdminPanel/` -> Direct PHP MySQL Queries -> Database.
    -   **API**: `api/v1/` -> JSON Responses -> Mobile App.

For a detailed list of every file and its purpose, see the **[File Inventory](File_Inventory.md)**.
For the structural data layout, see the **[Database Schema](Database_Schema.md)**.

---

## 2. Problems & Weaknesses Analysis

*Address Point 2: Identify what are problems, weaks in system.*

### Critical Security Weaknesses
1.  **Plaintext Passwords**:
    -   **Issue**: In `AdminPanel/hrm/addNewEmployee.php`, passwords are inserted directly into the database without hashing (`VALUES (..., '$password')`).
    -   **Risk**: If the database is compromised, all user credentials are stolen instantly.
    -   **Fix**: Use `password_hash($password, PASSWORD_DEFAULT)` when saving and `password_verify()` during login.

2.  **Hardcoded Credentials**:
    -   **Issue**: `inc/config.php` and `api/v1/config.php` contain the database password in plain text.
    -   **Risk**: Anyone with file access (e.g., git repo access) can see the production DB password.

3.  **SQL Injection Vulnerability**:
    -   **Issue**: Many files use string interpolation for SQL queries (e.g., `INSERT INTO ... VALUES ('$emp_name')`).
    -   **Risk**: A user could enter `Induwara'); DROP TABLE employees;--` as a name and delete the table.
    -   **Fix**: Use Prepared Statements (`mysqli_prepare` and `mysqli_stmt_bind_param`) consistently.

---

## 3. Troubleshooting Guide

*Address Point 3: When error happen, read this doc for get idea.*

### Database Connection Failed
-   **Symptoms**: "Database connection failed" on screen.
-   **Cause**: Incorrect credentials in `inc/config.php`.
-   **Solution**: Update `$server`, `$db_user`, `$db_pwd`.

### Salary Payment Issues
-   **Symptoms**: "Salary Paid" message appears but Expense report doesn't show it.
-   **Cause**: The `syncSalaryExpense` function in `api/v1/expenses/sync_salary_expense.php` might contain logic errors or the `expenses` table trigger isn't firing.
-   **Solution**: Check `error_log` table in database.

### API CORS Errors
-   **Symptoms**: Mobile app return "Network Error" or "CORS Header Missing".
-   **Cause**: `api/v1/config.php` has `define('API_CORS_ORIGINS', '*')`. If you are sending credentials (Cookies/Auth Headers), wildcard `*` isn't allowed by browsers.
-   **Solution**: Set `API_CORS_ORIGINS` to the specific mobile app domain/scheme.

---

## 4. Future Implementation & Cleanup

*Address Point 4: Identify future implement, what are un-need files.*

### Files to Remove/Ignore
1.  **`vendor/`**: This directory is large and should be managed by Composer, not committed to Git.
2.  **`backup/*.sql`**: These contain full database dumps. **IMMEDIATE DELETE** recommended from the public web folder for security.
3.  **`*.log` files**: `error_log` files are scattered (found 7 instances). Configure PHP to write to a single system log instead.
4.  **`AdminPanel/OneTimeProduct/`**: Seems like legacy/duplicate logic if `api/v1/one_time_products/` exists.

### Roadmap
1.  **Framework Migration**: Move from "Vanilla PHP" to a framework (Laravel/Symfony) to handle Routing, Security, and ORM (Database) automatically.
2.  **Unified Auth**: Currently `AdminPanel` uses Sessions (`$_SESSION`) and API uses JWT tokens. Unifying this logic would simplify the code.

---

## 5. System Code Health Check (Audit)

*Address Point 5: Identify have any issues in system codes (logical, syntax, mismatch).*

### Audit Findings
-   **Session Management Mismatch**:
    -   Found **~70** `session_start` calls but **~150** usages of `$_SESSION`.
    -   **Risk**: Accessing `$_SESSION` without `session_start()` causes "Undefined variable" warnings and login failures.
    -   **Recommendation**: Create a single `init.php` file that starts the session and include it everywhere, rather than calling it manually in every file.
    
-   **Input Validation**:
    -   `$_POST` data is often assigned directly to variables without `isset()` checks (e.g., `$emp_name = $_POST['emp_name'];`).
    -   **Risk**: "Undefined index" PHP Warnings if a form is submitted incorrectly.

-   **Deprecated Functions**:
    -   No usages of `mysql_query` (Good, using `mysqli`).
    -   No usages of `md5` for passwords found in source (Good), but plaintext is worse.

---

## 6. Full Documentation

*Address Point 6: All other everything that normal system software docs have.*

See the attached sub-documents:
-   **[File Inventory](File_Inventory.md)**: What every file does.
-   **[Database Schema](Database_Schema.md)**: Tables, Columns, and Relations.

---
*Generated by Antigravity AI Agent*
