# Telegram Bot Integration & Dashboard Plan for Srijaya ERP

## Overview
This feature integrates a Telegram Bot to provide the business owner with actionable insights and control. The implementation prioritizes **ease of use** for the admin via a dedicated settings dashboard and logical categorization of notifications.

## 1. Architecture
*   **Platform**: Telegram Bot API.
*   **Backend**: PHP (`inc/TelegramHelper.php`).
*   **Frontend**: Admin Panel > Settings > Telegram Tab.
*   **Database**: Tables for config, topics, schedules, and logs.

## 2. 🧩 Functional Areas (The "Whole System" Coverage)

We have analyzed the entire ERP (Sales, Expenses, Purchase, HRM, Bank, etc.) and categorized features into 5 logical streams.

### A. 📊 Business Overview (Daily Reports)
*   **Daily Executive Summary**: Delivered at closing time (e.g., 10 PM).
    *   Total Sales (Cash/Card/Credit).
    *   Total Expenses & Returns.
    *   Total Bank Deposits.
    *   Net Profit Estimate.
    *   Top Selling Items.

### B. 💰 Finance & Banking
*   **Supplier Payment Reminders**: "You need to pay Supplier X Rs. 50,000 by tomorrow."
*   **Credit Collection**: "Customer Y's credit balance (Rs. 25,000) is due."
*   **New Expense Alert**: Real-time alert when staff records a large expense.
*   **Bank Deposits**: Notification when cash is deposited to the bank.

### C. 📦 Inventory & Purchasing
*   **Low Stock Alerts**: "Warning: Sugar is down to 5kg."
*   **Expiry Warnings**: "Milk Batches expiring in 3 days."
*   **Purchase Orders (PO)**: Notification when a new PO is created.
*   **Goods Received (GRN)**: "Stock Updated: Received 100 units of Rice from Supplier Z."

### D. 📝 Sales & Operations
*   **High Value Invoice**: Alert for sales > Rs. 50,000 (Customizable limit).
*   **Sales Returns**: Alert when a refund is processed (Loss prevention).
*   **Quotations**: Notification when a Quotation is sent or accepted.

### E. 👥 HR & Staff
*   **Attendance**: "Induwara clocked in at 08:05 AM." / "Staff X clocked out."
*   **Payroll**: Notification when Salary or Advances are paid.

### F. 🚨 System Health
*   **Critical Errors**: database failures, printer errors.
*   **Backup Status**: Confirmation that nightly backup succeeded.

## 3. The Telegram Dashboard (Admin > Settings > Telegram)
Designed for non-tech admins to manage everything easily.

### A. Connection
*   **Connect Bot**: Simple fields for Bot Token & Chat ID.
*   **Test Connectivity**: One-click test.

### B. Topic Router (Organize the Chaos)
Map the functional areas above to Telegram Topics.
*   *Example Setup:*
    *   `Reports` -> Topic: "📊 Summary"
    *   `Finance` -> Topic: "💰 Money Matters"
    *   `Inventory` -> Topic: "📦 Stock & PO"
    *   `Staff` -> Topic: "👥 HR Updates"
    *   `Alerts` -> Topic: "🚨 Red Flags"

### C. Schedule Manager (Hourly)
*   Set specific **Hours (0-23)** for Daily Summaries or Payment Reminders.
*   *Example*: Set "Daily Summary" to **22:00 (10 PM)**.

### D. Monitoring & Logs
*   **History**: See what the bot sent recently.
*   **Error Logs**: If the bot failed to send a message.

## 4. Bot Commands (Interactive)
The admin can ask the bot questions:
*   `/summary` -> Get the daily report immediately.
*   `/stock [item]` -> Check current qty and price.
*   `/staff` -> Who is currently clocked in?
*   `/cash` -> Current cash drawer balance (if managed).

## 5. Implementation Roadmap

### Stage 1: Core Foundation (Database & Class)
*   Create tables: `telegram_config`, `telegram_topics`, `telegram_schedules`, `telegram_logs`.
*   Create `TelegramService.php`: The brain that handles sending logic.

### Stage 2: Dashboard UI
*   Add **Traffic Controller** interface in Settings.
*   Settings to enable/disable specific modules (e.g., "Don't send Attendance alerts").

### Stage 3: Module Integration
*   **Hook into Events**:
    *   Modify `submit.php` (Sales) -> Trigger High Value Alert.
    *   Modify `process_return.php` (Returns) -> Trigger Return Alert.
    *   Modify `create_grn.php` (Stock) -> Trigger GRN Alert.
    *   Modify `updateClockInOut.php` (HR) -> Trigger Attendance Alert.

### Stage 4: Scheduled Tasks (Cron)
*   Create `telegram_runner.php` (Runs hourly).
*   Handles Scheduled Summaries and Stock Checks.

---
**Status**: Final Plan. Comprehensive System Coverage.
