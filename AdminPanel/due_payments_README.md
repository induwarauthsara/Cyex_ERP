# Due Payments System Documentation

## Overview
A comprehensive due payments management system for tracking and processing outstanding payments from GRN (Goods Receipt Notes) with future expansion capabilities for Utilities, Rent, Salary, Printer Rent, Loan, and Subscription payments.

## Files Created

### 1. Main Interface
- **`/AdminPanel/due_payments.php`** - Main interface with tabbed layout for different payment categories

### 2. API Endpoints
- **`/AdminPanel/api/fetch_grn_payments.php`** - Fetches all outstanding GRN payments with supplier details
- **`/AdminPanel/api/process_payment.php`** - Processes payments with full transaction logging
- **`/AdminPanel/api/test_due_payments.php`** - Test script to verify system functionality

### 3. Database Updates
- **`/AdminPanel/sql/due_payments_db_updates.sql`** - SQL script for database verification and optimization

## Features Implemented

### GRN Payments Section
1. **Payment Listing**
   - Displays all outstanding GRN payments
   - Shows GRN number, supplier name, invoice details
   - Shows total amount, paid amount, and outstanding balance
   - Payment status badges (unpaid, partial, paid)
   - Sortable and searchable DataTable

2. **Payment Processing**
   - Modal form for adding payments
   - Payment method selection (Cash, Bank Transfer, Cheque, Credit Card)
   - Reference number tracking
   - Payment notes
   - Real-time validation

3. **Database Updates**
   - Updates `goods_receipt_notes` table with payment details
   - Records in `supplier_payments` table
   - Updates supplier `credit_balance`
   - Logs in `transaction_log` table
   - Records action in `action_log` table

### Technical Features
1. **Transaction Safety**
   - All database operations wrapped in transactions
   - Rollback on any failure
   - Data integrity maintained

2. **User Experience**
   - Responsive design
   - Real-time feedback with SweetAlert2
   - Loading states and error handling
   - DataTable integration for better data management

3. **Security**
   - Admin-only access control
   - SQL injection prevention
   - Input validation and sanitization

## Database Tables Used

### Primary Tables
- **`goods_receipt_notes`** - Stores GRN information and payment status
- **`suppliers`** - Supplier information with credit balance tracking
- **`supplier_payments`** - Individual payment records
- **`transaction_log`** - System transaction logging
- **`action_log`** - User action logging

### Key Fields Updated
- `goods_receipt_notes.paid_amount`
- `goods_receipt_notes.outstanding_amount`
- `goods_receipt_notes.payment_status`
- `suppliers.credit_balance`

## Installation Instructions

1. **Upload Files**
   - Upload all created files to your server
   - Ensure proper file permissions

2. **Database Verification**
   - Run the test script: `/AdminPanel/api/test_due_payments.php`
   - Execute any necessary SQL updates from `due_payments_db_updates.sql`

3. **Access Control**
   - Ensure user has Admin role to access the system
   - Test login and navigation

## Usage Instructions

### For Users
1. Navigate to **Due Payments** from the Admin Panel
2. View outstanding GRN payments in the table
3. Click **Pay** button to add a payment
4. Fill in payment details and submit
5. System automatically updates all related records

### For Developers
1. **Adding New Payment Categories**
   - Create new tab in the main interface
   - Add corresponding API endpoints
   - Implement database tables and logic

2. **Customizing Payment Methods**
   - Update the enum values in database
   - Modify the dropdown options in the modal

3. **Adding Payment Validations**
   - Extend the JavaScript validation
   - Add server-side checks in process_payment.php

## Testing

### Automated Tests
Run `/AdminPanel/api/test_due_payments.php` to verify:
- Database connectivity
- Table structure
- Data availability
- API file existence
- Sample queries

### Manual Tests
1. **Payment Processing**
   - Create a test GRN with outstanding amount
   - Process partial payment
   - Process full payment
   - Verify all database updates

2. **User Interface**
   - Test responsive design
   - Verify DataTable functionality
   - Test modal interactions
   - Check error handling

## Future Enhancements

### Planned Categories
1. **Utilities** - Electricity, water, internet bills
2. **Rent** - Office/warehouse rent payments
3. **Salary** - Employee salary management
4. **Printer Rent** - Printer lease payments
5. **Loans** - Business loan payments
6. **Subscriptions** - Software/service subscriptions

### Technical Improvements
1. **Payment Scheduling** - Recurring payment setup
2. **Approval Workflow** - Multi-level payment approval
3. **Reporting** - Detailed payment reports and analytics
4. **Notifications** - Email/SMS alerts for due payments
5. **Integration** - Bank API integration for automated reconciliation

## Troubleshooting

### Common Issues
1. **"No outstanding payments found"**
   - Check if GRNs exist with status='completed' and outstanding_amount > 0
   - Verify supplier relationships

2. **Payment processing fails**
   - Check database connections
   - Verify user permissions
   - Check transaction log for errors

3. **UI not loading properly**
   - Verify all CSS/JS dependencies are loaded
   - Check browser console for errors
   - Ensure proper file paths

### Support
For technical support or feature requests, contact the development team with:
- Error messages
- Steps to reproduce issues
- Browser/server environment details

## Security Notes

1. **Access Control**
   - Only Admin users can access payment functions
   - Session validation on all API calls

2. **Data Protection**
   - All inputs are sanitized
   - SQL injection prevention implemented
   - Transaction logging for audit trails

3. **Financial Security**
   - Payment amounts validated against outstanding balances
   - All financial transactions logged
   - Database transaction rollback on errors