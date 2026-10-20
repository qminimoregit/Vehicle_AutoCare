# AutoCare System

> **A professional, simple, and intuitive Auto Care Center Management System built with a clean Laravel Monolithic architecture.**

Designed for auto care center employees who value straightforward workflows, clear typography, and zero clutter.

---

## 1. Technology Stack

- **Backend**: Laravel 12 (latest stable), PHP 8.2+, MySQL, Eloquent ORM
- **Frontend**: Laravel Blade, Tailwind CSS v4, JavaScript, Alpine.js, Vite
- **Authentication**: Laravel session-based authentication with role-based access control (Admin & Staff)
- **Security**: CSRF protection, login rate limiting, password hashing, active account guards, activity audit logging

---

## 2. Design System & Theme Colors

The interface strictly adopts the mandated corporate color palette:

| Component | Color Code | Purpose |
| :--- | :--- | :--- |
| **Top Navigation** | `#0B1849` | Deep navy fixed navbar with system identity & user profile |
| **Primary Text** | `#4A4A4A` | Soft charcoal text ensuring readability without glare |
| **Main Background** | `#F6F7F9` | Modern neutral workspace canvas |
| **Cards & Panels** | `#FFFFFF` | Clean white cards with subtle borders |
| **Accents** | `#2563EB` & Emerald | Primary actions and active status indicators |

---

## 3. Implemented Modules (Phase 1)

### A. Authentication & Security
- **Clean Login Interface**: Email, Password, Remember Me, Forgot Password flow.
- **Role-Based Access Control**:
  - **Admin**: Full system management, staff accounts management, audit logs, master data configuration.
  - **Staff**: Operational access to workshop records and services. Direct URL access to admin routes is strictly denied with HTTP 403.
- **Account State Guard**: Inactive or deactivated accounts cannot sign in or perform actions.
- **Rate Limiting**: Protects against brute-force login attempts (5 attempts/min).
- **Secure Administrator Setup**: Artisan console command to create initial admin without hardcoded credentials.

### B. Center Operations Dashboard
- **5 Workshop Metrics**: Today's Jobs, Pending Jobs, Completed Jobs, Today's Sales, Pending Payments (displays real data or clear zero states, never fabricated numbers).
- **Quick Action Bar**: Shortcuts for New Job, New Customer, New Quotation, New Invoice (clearly labeled "Coming Soon").
- **Master Data Overview**: Live service counts, vehicle types, center profile shortcut.
- **Activity Stream**: Audit logs of recent workshop events.

### C. Master Data Modules
1. **Service Lists** (`/master-data/services`):
   - Service Code (with auto-suggested generator, e.g. `SRV-001`), Name, Description, Status (Active/Inactive).
   - CRUD: List, Add, Edit, Search (by code, name, or description), Filter by status, Activate / Deactivate toggle.
2. **Vehicle Types** (`/master-data/vehicle-types`):
   - Vehicle Type Name (e.g., Car, Van, SUV, Motorbike, Lorry), Description, Status.
   - CRUD: List, Add, Edit, Search, Filter by status, Activate / Deactivate toggle.
3. **Manage Service Payment** (`/master-data/service-payments`):
   - Clean, professional placeholder page explaining that payment formulas and commission rules are pending confirmation from management.
4. **Company Details** (`/master-data/company-details`):
   - Center's legal profile: Company Name, Business Registration Number (BRN), Phone, Email, Tax/VAT Number, Physical Address, and Company Logo upload (with real-time preview and safe storage).
5. **Search Client SMS** (`/master-data/client-sms`):
   - Customer search and recipient selection counter.
   - SMS Composer with real-time character and segment counter (`0 / 160`).
   - Clear policy safeguard explaining that real SMS broadcasting is paused until gateway credentials and consent rules are configured.

### D. Administration & Auditing (Admin-Only)
- **Staff Accounts Management** (`/admin/staff`): Admins create staff accounts, manage roles, and toggle user active/inactive status.
- **System Activity Logs** (`/admin/activity-logs`): Full searchable audit trail tracking user actions, IP addresses, and timestamps.

---

## 4. Installation & Local Setup

### Prerequisites
- PHP 8.2 or higher (with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip` extensions)
- MySQL / MariaDB Server
- Composer 2.x
- Node.js (v18+) & npm

### Step-by-Step Installation

1. **Navigate to the project folder**:
   ```bash
   cd autocare-system
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   ```

4. **Environment Configuration**:
   Copy `.env.example` to `.env` if not already present:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure MySQL Database in `.env`**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3307          # Set to 3306 or your local MySQL port (e.g., 3307 for XAMPP)
   DB_DATABASE=autocare_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Create MySQL Database**:
   Create the database in MySQL if it doesn't exist:
   ```sql
   CREATE DATABASE IF NOT EXISTS autocare_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

7. **Run Database Migrations & Initial Seeders**:
   ```bash
   php artisan migrate --seed
   ```
   *Seeds default vehicle types (Car, Van, SUV, Motorbike, Lorry), standard workshop services, and company settings.*

8. **Link Public Storage (for logos)**:
   ```bash
   php artisan storage:link
   ```

9. **Compile Frontend Assets**:
   ```bash
   npm run build
   # Or for active local development with hot reload:
   # npm run dev
   ```

---

## 5. First Administrator Account Setup

To create the initial Administrator securely without hardcoded credentials, run the custom Artisan command:

```bash
php artisan autocare:create-admin
```

It will prompt you securely for:
- Administrator Full Name
- Administrator Email Address
- Administrator Password (hidden input, min 8 characters)
- Contact Phone (optional)

### Non-Interactive Command (for automated deployments):
```bash
php artisan autocare:create-admin --name="Admin User" --email="admin@autocare.com" --password="AdminPassword123!" --phone="+94 77 987 6543"
```

### Pre-Seeded Test Credentials:
- **Admin**: `admin@autocare.com` / `AdminPassword123!` (or the credentials you chose)
- **Staff**: `staff@autocare.com` / `Staff@12345`

---

## 6. Running the Application Locally

Start the Laravel local server:

```bash
php artisan serve --port=8000
```

Open your browser and navigate to:
[http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 7. Running Automated Tests

AutoCare System includes 23 automated feature & unit tests covering authentication, session states, Master Data CRUD, unique validation rules, and role authorization:

```bash
php artisan test
```

Or for test descriptions:
```bash
php artisan test --testdox
```

---

## 8. Existing Quotation & Invoice System Integration

AutoCare System has been prepared to integrate an existing Quotation & Invoice Management System:
- **Clean Monolithic Architecture**: Models and controllers use standardized Eloquent relationships.
- **Database Consistency**: Tables use `id`, timestamps, and standardized indexing.
- **Database Co-location**: The existing `quotation_invoice_system` tables can be merged cleanly via migration or inspected through Eloquent models.
- **Integration Checklist**:
  1. Inspect existing Quotation & Invoice schemas (e.g. `quotations`, `invoices`, `invoice_items`).
  2. Map customer records to the upcoming `customers` table.
  3. Relate `invoice_items` to `services.id`.
  4. Bring across existing business calculation formulas into `ServicePayment` and `SalesBilling` controllers.
