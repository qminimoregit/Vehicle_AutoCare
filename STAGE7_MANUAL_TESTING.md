# AutoCare System – Stage 7 Manual Testing Checklist

**Document Version:** 1.0  
**Project:** AutoCare System  
**Stage:** 7 – Reports & Credit Management  
**Testing Environment:** Local Development (`http://127.0.0.1:8000`)  
**Database:** MySQL (Database: `autocare_system`)  
**Test Data Seeder:** `database/seeders/Stage7ManualTestSeeder.php`

---

## 1. Test Environment & Credentials

| Role | Username / Email | Password | Scope |
| :--- | :--- | :--- | :--- |
| **System Admin** | `admin@autocare.test` (or `admin@autocare.com`) | `password` | Full system access, all branches, credit account overrides |
| **Branch Staff** | `staff@autocare.test` | `password` | Colombo Branch counter operations & reports |

---

## 2. Key Seeded Entities & Record Numbers for Searching

Use these actual record numbers and names when searching in the application:

### Products & Batches
- **Dual-Batch Oil Filter:** `PRD-FLT-001` (Batches: `B001` @ LKR 800 [10 units] + `B002` @ LKR 1000 [5 units] = LKR 13,000 Stock Value)
- **Dual-Batch Engine Oil 1L:** `PRD-OIL-1L` (Batches: `BATCH-001` [15 units] + `BATCH-002` [20 units] = 35 Available Units)
- **Low Stock Sensor:** `PRD-LOW-001` (Stock = 2 units, Reorder level = 10)
- **Zero Stock Part:** `PRD-ZERO-001` (Stock = 0 units)

### Vehicles
- **CAB-1234:** Toyota HiAce Super GL (Fleet Vehicle – ABC Transport Ltd, 2 completed services: 50,000 km & 55,000 km, next: 60,000 km)
- **CAA-5678:** Toyota Corolla Axio (Fleet Vehicle – ABC Transport Ltd, 2 completed services: 28,000 km & 32,000 km, next: 37,000 km)
- **BCD-7890:** Isuzu ELF Flatbed (Fleet Vehicle – ABC Transport Ltd, 1 completed service: 78,000 km)
- **WP-NB-1122:** Mitsubishi Fuso Canter (Fleet Vehicle – XYZ Logistics Ltd, 2 completed services: 102,000 km & 110,000 km)
- **WP-NB-3344:** Isuzu Forward Cargo Carrier (Fleet Vehicle – XYZ Logistics Ltd, 1 completed service: 145,000 km)
- **WP-CAE-4554:** Toyota Allion NZT260 (Individual Customer – Kamal Gunaratne, 1 completed service: 42,000 km)
- **WP-NEW-1010:** Honda Grace Hybrid (Unserviced vehicle – 0 jobs)
- **WP-NEW-2020:** Toyota Vitz (Unserviced vehicle – 0 jobs)

### Company & Individual Credit Accounts
- **ABC Transport Ltd:** Contact: Sunil Fernando (`0112445566`). Credit Limit: LKR 50,000. Balance: LKR 15,000. Available: LKR 35,000.
- **XYZ Logistics Ltd:** Contact: Dinesh Gamage (`0112883344`). Credit Limit: LKR 100,000. Balance: LKR 90,000. Available: LKR 10,000.
- **Kamal Gunaratne:** Individual Executive (`0779988776`). Credit Limit: LKR 40,000. Balance: LKR 20,000. Available: LKR 20,000.
- **Lanka Cabs & Tours (Pvt) Ltd:** Contact: Gamini Wijesinghe (`0112778899`). Credit Limit: LKR 200,000. Spread across all 5 aging buckets (Total: LKR 118,500).

### Key Test Invoices
- `INV-202609-0001`: September Service (CAB-1234) – LKR 12,000 (Paid via Bank Transfer)
- `INV-202610-0010`: October Service (CAB-1234) – LKR 15,000 (Fully Paid by PAY-202610-0010)
- `INV-202610-0011`: October Service (CAA-5678) – LKR 8,000 (Partially Paid: Paid LKR 5,000, Balance LKR 3,000)
- `INV-202610-0012`: October Service (BCD-7890) – LKR 12,000 (Unpaid, Balance LKR 12,000)
- `INV-202610-0013`: Service (WP-NB-1122) – LKR 50,000 (XYZ Logistics, Unpaid)
- `INV-202610-0014`: Service (WP-NB-3344) – LKR 40,000 (XYZ Logistics, Unpaid)
- `INV-202610-0015`: Service (WP-CAE-4554) – LKR 18,000 (Kamal Gunaratne, Paid LKR 10,000, Balance LKR 8,000)
- `INV-202610-0016`: Service (WP-CAE-4554) – LKR 12,000 (Kamal Gunaratne, Balance LKR 12,000)
- `INV-202610-0017`: Direct Sale Cash – LKR 6,500 (Paid)
- `INV-202610-0018`: Direct Sale Card – LKR 12,500 (Paid)
- `INV-202610-0019`: Direct Sale Bank Transfer – LKR 21,000 (Paid)
- `INV-202610-0020`: Direct Sale Cheque (Deposited) – LKR 15,000 (Paid)
- `INV-202610-0021`: Direct Sale Cancelled/Void – LKR 9,000 (Cancelled)
- `INV-202610-0022`: Kandy Branch Direct Sale – LKR 14,500 (Paid)
- `INV-202610-0030`: Oct 17 Daily Cash Sale – LKR 20,000 (Paid)
- `INV-202610-0031`: Oct 17 Daily Card Sale – LKR 15,000 (Paid)
- `INV-202610-0032`: Oct 17 Daily Cheque Sale (Received) – LKR 10,000 (Paid)
- `INV-202610-0033`: Oct 17 Daily Credit Job Sale – LKR 5,000 (Unpaid)

---

## 3. Comprehensive Manual Testing Checklist

| Test ID | Module | Test Record | Testing Steps | Expected Result | Actual Result | Pass/Fail | Remarks |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **ST7-001** | Stock Report | `PRD-OIL-1L` | 1. Go to **Reports > Stock Report** (`/reports/stock`).<br>2. Search `PRD-OIL-1L` or "Engine Oil 1L".<br>3. Click "Batches" button. | 1. Total available stock displays **35 units**.<br>2. Batch breakdown shows Batch 001 (15 units) and Batch 002 (20 units). | Available stock is 35 units across 2 active GRN batches. | **PASS** | Meets dual-batch requirement. |
| **ST7-002** | Stock Value | `PRD-FLT-001` | 1. Go to **Reports > Stock Value** (`/reports/stock-value`).<br>2. Filter by Product `PRD-FLT-001` (Oil Filter). | Total stock value is exactly **LKR 13,000.00** (Batch B001: 10 @ LKR 800 = 8,000; Batch B002: 5 @ LKR 1,000 = 5,000). | Product value calculated as LKR 13,000.00 directly from MySQL batch inventory costs. | **PASS** | Validates FIFO batch cost vs flat price. |
| **ST7-003** | Direct Sales Report | `INV-202610-0017` to `INV-202610-0020` | 1. Go to **Reports > Sales Report** (`/reports/sales`).<br>2. Select Type filter: **Direct Sales**.<br>3. Set Date Range: `2026-10-01` to `2026-10-31`. | Displays direct counter sales separated from service jobs. Completed sales sum properly without duplicate counts. | Only direct sales displayed with total matching counter invoices. | **PASS** | Direct vs Service isolation confirmed. |
| **ST7-004** | Service Sales Report | `INV-202610-0010` to `INV-202610-0014` | 1. Go to **Reports > Sales Report**.<br>2. Select Type filter: **Job Cards (Service Sales)**.<br>3. Set Date Range: `2026-10-01` to `2026-10-31`. | Shows service job invoices with linked vehicle numbers (`CAB-1234`, `CAA-5678`, etc.). | Service invoices displayed with grand totals matching job cards. | **PASS** | Links to vehicle and job cards. |
| **ST7-005** | Profit & Loss | October 2026 Ledger | 1. Go to **Reports > Profit & Loss** (`/reports/profit-loss`).<br>2. Set Period: `2026-10-01` to `2026-10-31`.<br>3. Review Revenue, COGS, and Operating Expenses. | 1. Net Revenue = Direct Sales + Service Sales.<br>2. Operating Expenses excludes salary advances (LKR 20,000) and GRN supplier payments (LKR 45,000).<br>3. Net Profit = Gross Profit - Operating Expenses. | Operating expenses reflect pure utility, maintenance, and salary expenses without double counting balance sheet items. | **PASS** | Non-double counting verified. |
| **ST7-006** | Vehicle Service History | `CAB-1234` | 1. Go to **Reports > Vehicle Service History** (`/reports/vehicle-service`).<br>2. Search vehicle registration `CAB-1234`.<br>3. Inspect timeline. | 1. Shows 2 completed jobs in date order:<br>   - Sep 05, 2026 @ 50,000 km<br>   - Oct 05, 2026 @ 55,000 km<br>2. Next service mileage displays **60,000 km**. | Chronological service history displayed with accurate mileage and PDF export button. | **PASS** | Multi-service progression validated. |
| **ST7-007** | Cash Sales | `INV-202610-0030` | 1. Go to **Reports > Money Book** (`/reports/money-book`).<br>2. Filter by date `2026-10-17`. | Cash collection row shows **LKR 20,000.00**. | Payment method grouped as cash with payment receipt reference. | **PASS** | Cash register reconciliation verified. |
| **ST7-008** | Card Sales | `INV-202610-0031` | 1. On Money Book for `2026-10-17`, inspect Card payment column. | Card collection shows **LKR 15,000.00** with transaction reference `AUTH-MC-55442`. | Card sales grouped accurately under digital settlements. | **PASS** | Bank settlement tracking validated. |
| **ST7-009** | Cheque Sales | `INV-202610-0032`, `INV-202610-0020`, `INV-202609-0005` | 1. Go to **Reports > Money Book**.<br>2. Check cheques in Received (`CHQ-771122`), Deposited (`CHQ-889902`), Cleared (`CHQ-889901`), and Bounced/Reversed (`CHQ-889904`). | Cheques reflect status notes. Bounced cheque (`PAY-202610-0024`) does not inflate daily cash receipts. | Cheque lifecycle distinguished; reversed payment excluded from net collections. | **PASS** | Cheque audit lifecycle tested. |
| **ST7-010** | Credit Sales & Daily Sales Separation | Daily Scenario: `2026-10-17` | 1. On Money Book (`2026-10-17`), check Total Sales vs Liquid Cash/Card Inflow. | 1. Total Daily Sales = **LKR 50,000.00** (Cash 20k + Card 15k + Cheque 10k + Credit 5k).<br>2. Liquid Inflow = **LKR 35,000.00**.<br>3. Outstanding Credit Created = **LKR 5,000.00**. | System distinguishes recognized sales revenue from cash on hand. | **PASS** | Meets Section 7 example requirement. |
| **ST7-011** | Credit Limit & Blocking | XYZ Logistics Ltd | 1. Go to **Reports > Credit Management** (`/reports/credit-management`).<br>2. Check XYZ Logistics: Limit = LKR 100,000, Balance = LKR 90,000, Available = LKR 10,000.<br>3. Test check-limit API or invoice creation for LKR 15,000. | System evaluates limit check and blocks transaction with "Credit limit exceeded: Limit 100,000 vs Total 105,000" unless admin overrides. | Blocked appropriately; safe-mode prevents unauthorized limit breach. | **PASS** | Credit protection safeguard confirmed. |
| **ST7-012** | Monthly Company Statement | ABC Transport Ltd | 1. Go to **Reports > Credit Management**.<br>2. Find "ABC Transport Ltd" and click **Statement**.<br>3. Select Month: **October 2026**. | 1. Opening Balance: **LKR 0.00** (Sep fully settled).<br>2. October Credit Invoices: **LKR 35,000.00** across 3 vehicles (`CAB-1234`, `CAA-5678`, `BCD-7890`).<br>3. October Payments: **LKR 20,000.00**.<br>4. Closing Balance: **LKR 15,000.00**.<br>5. Available Credit: **LKR 35,000.00**. | Mathematical formula `0 + 35,000 - 20,000 = 15,000` matches exactly. All 3 vehicles and invoices listed. | **PASS** | Meets Section 8 & 9 statement requirements. |
| **ST7-013** | Partial Credit Payment Allocation | `INV-202610-0011` | 1. Inspect Invoice `INV-202610-0011` under ABC Transport profile. | Invoice grand total LKR 8,000; paid LKR 5,000; status is `partially_paid`; remaining balance is **LKR 3,000.00**. | Linked payment `PAY-202610-0011` allocated LKR 5,000, leaving exactly LKR 3,000 due. | **PASS** | Individual invoice balance correct. |
| **ST7-014** | Multiple Invoice Payment Allocation | ABC Transport Oct 18 Payment | 1. Check payment allocations for ABC Transport on Oct 18, 2026 (Total LKR 20,000). | LKR 20,000 is split across original invoices:<br>- LKR 15,000 settles `INV-202610-0010` (Balance = 0)<br>- LKR 5,000 partially settles `INV-202610-0011` (Balance = 3,000)<br>No duplicate invoice created. | Payments tied to existing invoices without generating phantom sales. | **PASS** | Clean ledger integrity. |
| **ST7-015** | Credit Aging Intervals | Lanka Cabs & Tours (Pvt) Ltd | 1. Go to **Reports > Credit Management**.<br>2. Open profile for **Lanka Cabs & Tours (Pvt) Ltd**.<br>3. Review Aging Analysis table. | All 5 aging buckets populated:<br>- Current (Not Yet Due): LKR 15,000<br>- 1–30 Days Overdue: LKR 18,500<br>- 31–60 Days Overdue: LKR 22,000<br>- 61–90 Days Overdue: LKR 28,000<br>- Over 90 Days Overdue: LKR 35,000<br>Total = **LKR 118,500.00**. | Each invoice accurately categorized by due date relative to today's date. | **PASS** | Full coverage of Section 10 aging categories. |

---

## 4. Advanced Filter & Edge-Case Verifications

| Test ID | Scenario | Steps to Execute | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **ST7-016** | Branch Scoping (Colombo vs Kandy) | 1. Switch active branch to "Colombo Branch" via topbar.<br>2. Open Sales Report.<br>3. Switch branch to "Kandy Branch" and refresh. | Colombo shows Colombo sales (`INV-202610-0017` to `0021`). Kandy shows Kandy counter sale (`INV-202610-0022` for LKR 14,500). | BranchContext properly isolates data by branch. | **PASS** |
| **ST7-017** | Date Range Filter | 1. On Sales Report, filter `2026-09-01` to `2026-09-30`.<br>2. Then filter `2026-10-01` to `2026-10-31`. | September displays only September sales (`INV-202609-0001` to `0005`). October displays October sales. | Date filtering accurately bounds MySQL query results. | **PASS** |
| **ST7-018** | Cancelled / Void Sale Exclusion | 1. Check Sales Report total for October 2026.<br>2. Locate `INV-202610-0021` (LKR 9,000 cancelled sale). | The LKR 9,000 cancelled sale is NOT included in recognized sales revenue totals. | Cancelled invoice displayed with "Cancelled" badge and 0 revenue impact. | **PASS** |
| **ST7-019** | Unserviced Vehicle Query | 1. Go to Vehicle Service History.<br>2. Search unserviced test vehicle `WP-NEW-1010` (or `WP-NEW-2020`). | Displays vehicle details (Make: Honda Grace Hybrid) with a friendly "No service records found for this vehicle" message. | Clean empty state; no 500 error or SQL exception. | **PASS** |
| **ST7-020** | PDF & Print Generation | 1. On Vehicle Service Report for `CAB-1234`, click **Print Report** (`/reports/vehicle-service/1/print`).<br>2. On Company Statement for ABC Transport, click **Print Statement** (`/reports/credit-management/1/statement/print`). | Clean, printable HTML/PDF layout with company branding, table structure, and signature areas. | HTTP 200; printable view loads smoothly. | **PASS** |
| **ST7-021** | Excel / CSV Export | 1. Click **Export** on Stock Report (`/reports/stock/export`).<br>2. Click **Export** on Sales Report (`/reports/sales/export`). | Generates downloadable `.csv` spreadsheet with exact report columns. | HTTP 200; file downloads cleanly. | **PASS** |
| **ST7-022** | Responsive Layout | 1. Open Chrome DevTools and toggle mobile view (e.g. iPhone 14 Pro, 390px width).<br>2. Navigate through Stock Report, Money Book, and Credit Management. | Topbar, stat cards, search filters, and table horizontally scroll or collapse without broken layouts. | Clean responsive behavior across mobile and desktop. | **PASS** |

---

## 5. Verification Sign-off

- [x] All 7 Stage 7 modules seeded with realistic, interconnected data
- [x] Safe idempotent execution verified via `php artisan db:seed --class=Stage7ManualTestSeeder`
- [x] Zero deletions or data loss from existing database
- [x] All 206 automated PHPUnit tests passing (`php artisan test`)
- [x] Laravel Pint style formatting clean
