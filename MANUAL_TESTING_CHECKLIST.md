# AutoCare System — Manual Testing Checklist

This comprehensive checklist is designed to guide QA engineers, developers, and non-technical testers through end-to-end verification of all modules (Stages 1 through 6) of the AutoCare System.

---

## 1. Quick Test Environment & Credentials

- **Base URL:** [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Database:** `autocare_system` (MySQL on port `3307`, local environment)
- **Top Bar:** Navy Blue (`#0B1849`), Brand accents, Quick POS & Vehicle shortcuts
- **Background:** Light slate (`#F6F7F9`), Cards: Pure white (`#FFFFFF`)

### User Roles & Logins
| Role | Email / Username | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@gmail.com` | `Ironman@123` | Full access across all modules, branch management, salary reports, and Stage 6 invoice cancellations & corrections. |
| **Staff Member** | `staff@autocare.com` | `Staff@12345` | Operational workflow: vehicle registration, POS sales, job cards, attendance, and quotations. Restricted from Admin date changes and sale cancellations. |

---

## 2. Master Test Data Reference Catalog

### Branches
- **Colombo Main Branch:** `BR-001` (Main headquarters)
- **Kandy Service Center:** `BR-002` (Second branch for multi-branch isolation testing)

### Key Test Customers & Vehicles
| Customer Name | Type | Phone | Vehicle Number | Vehicle Type & Make/Model |
| :--- | :--- | :--- | :--- | :--- |
| **Sunil Shantha** | Individual | `0772345671` | `WP-CAB-5544` | Car – Toyota Prius Hybrid 1.8L *(Avoid Service Sale target)* |
| **Sunil Shantha** | Individual | `0772345671` | `WP-CAR-1289` | Car – Honda Fit GP5 Hybrid |
| **Nimal Jayasinghe** | Individual | `0713456782` | `CP-KH-8921` | Car – Toyota Corolla 141 (Kandy Branch) *(Invoice Date Change target)* |
| **Kasun Bandara** | Individual | `0754567893` | `SP-BC-4412` | SUV – Mitsubishi Montero Sport *(Split Payment target)* |
| **Chaminda Silva** | Individual | `0765678904` | `WP-CAD-8822` | SUV – Toyota Land Cruiser Prado |
| **Priyanka Fernando** | Individual | `0726789015` | `WP-BHM-9911` | Motorbike – Yamaha FZ-S V3 |
| **Dilshan Perera** | Individual | `0787890126` | `WP-BIP-4567` | Motorbike – Honda Dio 110cc |
| **Ruwan Wickramasinghe**| Individual | `0708901237` | `WP-AAH-3344` | Three-Wheeler – Bajaj RE 4S *(Fresh vehicle, 0 past jobs)* |
| **Nadeeka Damayanthi** | Individual | `0749012348` | `WP-CBB-1100` | Car – Toyota Vitz KSP130 *(Fresh vehicle, 0 past jobs)* |
| **QuickCab Fleet Services** | Company | `0112445566` | `WP-KX-7788`, `WP-KY-3321`, `WP-CAR-9090` | Multi-Van & Car Corporate Fleet *(Bulk Payment target)* |
| **Lanka Logistics Ltd** | Company | `0112778899` | `WP-LY-4455`, `WP-LZ-6677`, `CP-LE-1122` | Commercial Lorry & Van Fleet |

### Pre-Seeded Stage 6 Action Targets
- **Avoid Direct Sale:** Direct Sale `DS-202610-0005` (Invoice `INV-202610-0005`, Sunil Shantha)
- **Avoid Service Sale:** Job Card `JOB-202610-0010` (Invoice `INV-202610-0006`, Sunil Shantha, `WP-CAB-5544`)
- **Invoice Date Change:** Invoice `INV-202610-0007` (Nimal Jayasinghe, unpaid, issued 5 days ago)
- **Split Invoice Payment:** Invoice `INV-202610-0008` (Kasun Bandara, 25,000 LKR due)
- **Multiple Invoice Bulk Payment:** Customer `QuickCab Fleet Services` (Invoices `INV-202610-0002`, `INV-202610-0003`, `INV-202610-0004`)

---

## 3. Manual Functional Test Matrix

| # | Module | Test Case | Test Record / Data | Test Steps | Expected Result | Pass / Fail | Remarks |
|---|---|---|---|---|---|:---:|---|
| **M1** | **Master Data** | Search & View Services | Code: `SRV-001` to `SRV-008` | 1. Go to **Management → Master Data → Service Lists**.<br>2. Type `Oil` in search bar.<br>3. Clear search. | Table filters to `SRV-001` immediately. Clearing displays all 8 services. | [ ] | |
| **M2** | **Master Data** | Set Vehicle-Specific Pricing | Service: `SRV-001`, Car vs SUV | 1. Go to **Master Data → Manage Service Payment**.<br>2. Select `SRV-001`.<br>3. Inspect Car price vs SUV price. | Car price is 3,500 LKR; SUV price is 6,200 LKR. Updates save without errors. | [ ] | |
| **M3** | **Master Data** | Vehicle Types & Images | Vehicle Types: Car, Van, SUV, Motorbike, Lorry, Three-Wheeler | 1. Go to **Master Data → Vehicle Types**.<br>2. Inspect table images and descriptions. | All 6 types display real vehicle icons/images and active status badges. | [ ] | |
| **M4** | **Master Data** | Multi-Branch Management & Switching | Branches: `BR-001`, `BR-002` | 1. Go to **Master Data → Company Details → Branches**.<br>2. Verify Colombo & Kandy exist.<br>3. Use top bar branch dropdown to switch to Kandy. | Active branch switches in session; top bar shows `[AutoCare Kandy Service Center ▼]`. | [ ] | |
| **I1** | **Inventory** | Supplier & Product List | Suppliers: `Mobil`, `Denso`, `Bosch` | 1. Go to **Suppliers & Inventory → Products**.<br>2. Search `Filter`.<br>3. Inspect preferred supplier and UOM. | Shows Denso Engine Oil Filter and Denso Air Filter with preferred supplier. | [ ] | |
| **I2** | **Inventory** | Check Zero & Low Stock Indicators | Products: `PRD-GAT-014`, `PRD-OSR-015` | 1. Go to **Suppliers & Inventory → Stock Report**.<br>2. Check `Gates Fan Belt` and `Osram Bulb`. | Gates Fan Belt displays `0.00` (Out of Stock); Osram Bulb highlights as Low Stock (2 units). | [ ] | |
| **I3** | **Inventory** | GRN Receipt & FIFO Batches | `PRD-MOB-001` (Mobil 1 5W-30) | 1. Open **Stock Report → Batch Breakdown** for `PRD-MOB-001`. | Shows Batch 1 @ 9,500 LKR and Batch 2 @ 9,800 LKR received under GRN-1 and GRN-6. | [ ] | |
| **O1** | **Operations** | Register New Vehicle | Vehicle: `WP-CAB-9999` | 1. Click top bar **Vehicle Registration**.<br>2. Enter customer name, phone, vehicle number `WP-CAB-9999`, type `Car`.<br>3. Save. | Vehicle is saved, normalized number generated, customer record created. | [ ] | |
| **O2** | **Operations** | Search Vehicle & Service History | Vehicle: `WP-CAB-5544` vs `WP-AAH-3344` | 1. In Vehicle list, search `WP-CAB-5544`. View details.<br>2. Search `WP-AAH-3344`. View details. | `WP-CAB-5544` shows full history of previous job cards; `WP-AAH-3344` shows empty state cleanly. | [ ] | |
| **O3** | **Operations** | Create New Job Card | Vehicle: `WP-CAR-1289` | 1. Click **Job Cards → + New Job Card**.<br>2. Select vehicle `WP-CAR-1289`.<br>3. Add service `SRV-001`.<br>4. Add product `PRD-DEN-003`.<br>5. Save. | Job Card created in `pending` state; pricing pulled from vehicle price matrix. | [ ] | |
| **O4** | **Operations** | POS Direct Sale & Stock Deduction | Customer: Walk-in / Cash | 1. Click top bar **Direct Sale**.<br>2. Add 1x `PRD-WUR-009` (Brake fluid).<br>3. Select `Cash`, enter tendered amount.<br>4. Submit sale. | Sale completes; receipt modal / printable invoice displays; stock deducted via FIFO. | [ ] | |
| **B1** | **Billing** | View Quotations & Filter Status | `QT-202610-0001` to `0005` | 1. Go to **Sales & Billing → Quotations**.<br>2. Filter by status (`draft`, `sent`, `accepted`, `converted`). | Filter updates list correctly; items show breakdown of parts and labor. | [ ] | |
| **B2** | **Billing** | View Invoices & Payment Status | `INV-202610-0001` to `0008` | 1. Go to **Sales & Billing → Invoices**.<br>2. Verify Quotations and Invoices are the ONLY items in this section. | List shows correct balances; sidebar has ONLY Quotations & Invoices under Sales & Billing. | [ ] | |
| **P1** | **Payroll** | Employee Profiles & Wage Rates | `EMP-0001` to `EMP-0006` | 1. Go to **Employees & Payroll → Employee Registration**.<br>2. Inspect wage rates and joining dates. | All 6 employees display with active status and assigned daily wage rates. | [ ] | |
| **P2** | **Payroll** | Mark Daily Attendance | Date: Today | 1. Go to **Employees & Payroll → Employee Attendance**.<br>2. Select Employee `EMP-0005`.<br>3. Mark as `half_day`.<br>4. Save. | Half day recorded; daily earnings calculated as `0.5 * daily_wage` automatically. | [ ] | |
| **P3** | **Payroll** | Review Draft Monthly Payroll | Month: October 2026 | 1. Go to **Employees & Payroll → Employee Salary**.<br>2. Inspect draft October 2026 payroll report. | Displays net payable salary with pending salary advances deducted automatically. | [ ] | |
| **E1** | **Expenses** | Shop Expense vs Staff Expense | Categories: Utilities, Consumables | 1. Go to **Expenses → Expense Records**.<br>2. Filter by `Shop` vs `Staff`.<br>3. Add a new shop expense. | Filters work cleanly; new expense updates total expense summary. | [ ] | |
| **S6-1** | **Adjustments** | **Avoid Direct Sale (FIFO Stock Reversal)** | Sale: `DS-202610-0005` (Sunil Shantha) | 1. Go to **Invoice Adjustments → Avoid Direct Sale**.<br>2. Search `DS-202610-0005`.<br>3. Enter reason: `Customer returned unopened items`.<br>4. Ensure checkbox `Restore stock to inventory` is checked.<br>5. Confirm cancellation. | Sale status becomes `cancelled`; Invoice becomes `cancelled`; Payment status becomes `reversed`; Stock is restored to original batches via `issue_reversal`. | [ ] | |
| **S6-2** | **Adjustments** | **Avoid Service Sale (Vehicle History Preserved)** | Job: `JOB-202610-0010` (Vehicle `WP-CAB-5544`) | 1. Go to **Invoice Adjustments → Avoid Service Sale**.<br>2. Search vehicle `WP-CAB-5544`.<br>3. Select `INV-202610-0006`.<br>4. Enter reason: `Billing generated under wrong account`.<br>5. Confirm cancellation.<br>6. Check vehicle `WP-CAB-5544` in Operations. | Invoice cancelled; payment reversed; **Vehicle record and Job Card are NOT deleted**; service history notes note cancellation. | [ ] | |
| **S6-3** | **Adjustments** | **Invoice Date Change (Audit Tracking)** | Invoice: `INV-202610-0007` (Nimal Jayasinghe) | 1. Go to **Invoice Adjustments → Invoice Date Change**.<br>2. Select `INV-202610-0007`.<br>3. Change date to yesterday.<br>4. Enter reason: `Adjusted to align with customer PO`.<br>5. Save. | Date updates; original date and change audit log are preserved; future dates rejected. | [ ] | |
| **S6-4** | **Adjustments** | **Multiple Invoice Payment: Option 1 (Split Methods)** | Invoice: `INV-202610-0008` (Kasun Bandara, 25,000 LKR) | 1. Go to **Invoice Adjustments → Multiple Invoice Payment**.<br>2. Select Option 1 (Split Single Invoice).<br>3. Enter `INV-202610-0008`.<br>4. Split: Cash `10,000` + Card `10,000` + Bank `5,000` = `25,000`.<br>5. Submit. | 3 separate payments created; Invoice balance becomes `0.00`; status becomes `paid`. | [ ] | |
| **S6-5** | **Adjustments** | **Multiple Invoice Payment: Option 2 (Bulk Customer Receipt)** | Customer: `QuickCab Fleet Services` | 1. Go to **Invoice Adjustments → Multiple Invoice Payment**.<br>2. Select Option 2 (Pay Multiple Invoices).<br>3. Choose `QuickCab Fleet Services`.<br>4. Enter Payment Receipt total: `50,000.00` LKR.<br>5. Allocate `18,500` to INV-2, `14,800` to INV-3, and `16,700` to INV-4.<br>6. Submit. | Single Payment Receipt created; INV-2 & INV-3 become `paid`; INV-4 balance reduces to `3,300` LKR. | [ ] | |
| **SEC1** | **Security** | Staff Permission Restrictions | User: `staff@autocare.com` | 1. Log out Admin.<br>2. Log in as `staff@autocare.com`.<br>3. Check sidebar for **Invoice Adjustments**.<br>4. Attempt to access `/billing/avoid-direct-sale`. | Admin-only items (Avoid Direct Sale, Avoid Service Sale, Date Change) are hidden; direct URL access returns `403 Unauthorized`. | [ ] | |

---

## 4. Test Sign-Off Protocol

- **Tester Name:** _______________________
- **Date Executed:** _______________________
- **Total Test Cases:** 23
- **Passed:** _____ / 23
- **Failed:** _____ / 23
- **Sign-off Status:** `[ ] APPROVED FOR RELEASE`  /  `[ ] REJECTED (ISSUES FOUND)`
