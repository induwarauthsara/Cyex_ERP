# Expenses Management - Changelog

## Version 2.0 - Partial Payment Support (2025-12-06)

### 🎉 Major Features Added

#### Partial Payment System
- Added ability to record multiple payments against a single expense
- Visual progress bars showing payment completion percentage
- Automatic status transitions (unpaid → partial → paid)
- Payment history viewer with full audit trail

#### Database Changes
- **New Table:** `expense_payments` - Stores individual payment transactions
- **New Column:** `expenses.amount_paid` - Tracks cumulative payments
- **Updated Column:** `expenses.status` - Now includes 'partial' and 'unpaid' statuses
- **New Trigger:** `update_expense_status_after_payment` - Auto-updates expense status

#### API Endpoints (2 New)
- `POST /api/v1/expenses/add_payment.php` - Record partial/full payments
- `GET /api/v1/expenses/payment_history.php` - Retrieve payment history

#### API Endpoints (2 Updated)
- `GET /api/v1/expenses/list.php` - Now includes `amount_paid`, `remaining_amount`, `payment_percentage`
- `GET /api/v1/expenses/summary.php` - Enhanced with partial payment metrics

#### Frontend Enhancements
- Progress bars in expenses table showing payment completion
- "Add Payment" button for unpaid/partial expenses
- "Payment History" button showing all payment transactions
- Payment modal with remaining balance validation
- Updated status badges (paid, partial, unpaid, overdue)
- Enhanced dashboard showing unpaid amounts including partials

#### UX Improvements
- Animated gradient progress bars with shimmer effect
- Real-time validation preventing overpayment
- Payment summary in history modal
- Color-coded status badges for quick identification
- Responsive payment modals with SweetAlert2

### 🔧 Technical Improvements

#### Database Integrity
- Foreign key constraints on `expense_payments.expense_id`
- Cascade delete for related payment records
- Transaction-based payment recording
- Automatic rollback on errors

#### Performance Optimizations
- Indexed `expense_payments.expense_id` for faster lookups
- Efficient SUM calculations in trigger
- CSS-only progress bar animations (GPU-accelerated)
- Client-side payment validation reducing API calls

#### Security Enhancements
- Payment amount validation (must not exceed remaining balance)
- Prepared statements for all payment queries
- Transaction isolation for payment recording
- Input sanitization for payment notes

### 📚 Documentation
- **NEW:** `PARTIAL_PAYMENT_UPDATE.md` - Comprehensive technical documentation
- **NEW:** `MIGRATION_GUIDE.md` - Upgrade instructions for existing installations
- **UPDATED:** `README.md` - Added partial payment user guide

### 🐛 Bug Fixes
- Fixed pending payment calculations to include partial payments
- Corrected dashboard summary to show unpaid amounts accurately
- Updated status badge colors for better visibility

### 🔄 Migration Path
- Backward compatible with existing installations
- Migration script provided: `expenses_partial_payment_update.sql`
- No data loss during upgrade
- Zero downtime migration possible

---

## Version 1.0 - Initial Release (2025-12-06)

### Features

#### Database Schema
- Created `expense_categories` table with 10 default categories
- Created `recurring_expenses` table for subscription tracking
- Created `expenses` table for transaction storage
- Created `v_expense_category_summary` view for reporting
- Created `v_upcoming_recurring_payments` view for alerts

#### Backend API (9 Endpoints)
- `GET /categories.php` - List all expense categories
- `POST /add_category.php` - Create new category
- `POST /add.php` - Add one-time or recurring expense
- `GET /list.php` - List expenses with advanced filtering
- `PUT /update.php` - Update expense details
- `DELETE /delete.php` - Delete expense
- `GET /recurring.php` - List recurring expenses
- `POST /pay_recurring.php` - Mark recurring payment as paid
- `GET /summary.php` - Dashboard analytics

#### Frontend Components
- Dashboard with 4 summary cards
- Tabbed interface (All Expenses | Recurring | Analytics)
- Advanced filtering (date range, category, status, search)
- DataTables integration for sorting and pagination
- Add/Edit expense modal
- Category management interface
- Recurring payment scheduler
- Upcoming payments viewer

#### Charts & Analytics
- Category breakdown donut chart (ApexCharts)
- Month-over-month trend indicators
- Top spending category highlight
- Pending payments summary

#### Authentication & Security
- Integration with existing ApiAuth system
- Admin-only access control
- SQL injection prevention via prepared statements
- XSS protection via output escaping
- Input validation and sanitization

---

## Upcoming Features (Roadmap)

### Version 2.1 (Planned)
- [ ] Payment reminders via email
- [ ] Payment receipt auto-generation (PDF)
- [ ] Expense attachment uploads
- [ ] Payment reversals/refunds

### Version 3.0 (Future)
- [ ] Payment plans with installment schedules
- [ ] Multi-currency support
- [ ] Bank feed integration
- [ ] OCR for bill scanning
- [ ] Budget limits and alerts
- [ ] Expense approval workflows

---

## Breaking Changes

### Version 2.0
- **Status enum changed:** 'pending' renamed to 'unpaid' for clarity
- **API response format:** `list.php` and `summary.php` now include additional fields
  - Clients should handle new fields: `amount_paid`, `remaining_amount`, `payment_percentage`
  - Old clients will still work but won't display partial payment info

### Deprecation Notices
- None for version 2.0 (fully backward compatible)

---

## Migration Notes

### From v1.0 to v2.0
**Required Steps:**
1. Backup database
2. Run `expenses_partial_payment_update.sql`
3. Upload updated API files
4. Upload updated frontend files
5. Clear browser cache

**Estimated Time:** 5 minutes  
**Downtime:** None (can run live)

**See:** [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) for detailed instructions

---

## Known Issues

### Version 2.0
- None reported

### Version 1.0
- ✅ **Fixed in 2.0:** Dashboard pending payments didn't account for partial payments

---

## Credits

**Development Team:** Srijaya ERP Development Team  
**Database Design:** Custom MySQL schema with views and triggers  
**Frontend Framework:** Vanilla JavaScript with jQuery, DataTables, ApexCharts  
**Backend:** PHP 7.4+ REST API  
**UI Components:** SweetAlert2, FontAwesome 6  

---

## License

Proprietary - Srijaya ERP System  
© 2024 All rights reserved

---

## Support

For issues, questions, or feature requests:
1. Check documentation files (README.md, PARTIAL_PAYMENT_UPDATE.md)
2. Review API responses in browser Developer Tools
3. Check server error logs for PHP errors
4. Verify database triggers and constraints

**Version Compatibility:**
- PHP: 7.4+
- MySQL: 5.7+ (8.0 recommended)
- MariaDB: 10.2+
- Browsers: Modern browsers (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
