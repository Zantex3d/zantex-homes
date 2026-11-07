# ZANTEX HOMES - Real Estate Operating System

A production-grade, multi-company real estate management platform built with Laravel 11, featuring comprehensive accounting, property management, tenant/landlord portals, and advanced financial controls.

## Project Overview

ZANTEX HOMES is a complete real estate operating system covering 18 integrated modules across 9 development phases:

### 18 Core Modules
01. Property & Asset Management
02. Real Estate CRM & Sales
03. Tenant & Lease Management
04. Accounting & Financial Management
05. Estate Development & Construction
06. Facility Management & Maintenance
07. Investment & Shareholders
08. Staff, Agents & Commission
09. Legal & Document Management
10. Land & Plot Management
11. Property Listings & Marketing
12. Shortlet & Hospitality
13. Service Charge & Estate Dues
14. Procurement & Inventory
15. Loans & Liabilities
16. Reports & Business Analytics
17. Notifications & Communication
18. Users, Security & Audit

### Technical Highlights
- **Double-Entry Accounting**: Immutable posted journals with automatic balance verification
- **Multi-Company Architecture**: Complete data isolation with company_id on all business entities
- **NGN Currency & Africa/Lagos Timezone**: Built for Nigerian real estate market
- **Responsive Admin Portal**: Livewire components with real-time search, filtering, and pagination
- **RESTful API (v1)**: Sanctum-protected endpoints for mobile/external integrations
- **Role-Based Access Control**: 23+ permissions per module with company-scoped security
- **Comprehensive Test Coverage**: Permissions, accounting workflows, API endpoints

## System Requirements

- PHP 8.5+
- MySQL 8.0+ / MariaDB 10.5+
- Node.js 18+
- Composer 2.5+
- Laravel 11.x

## Quick Start

### 1. Installation

```bash
# Clone the repository
cd zantex-homes-build

# Install dependencies
composer install
npm install

# Create .env file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database and run migrations
php artisan migrate:fresh --seed
```

### 2. Configuration

Update `.env` with your database credentials:

```env
APP_NAME="ZANTEX HOMES"
APP_TIMEZONE=Africa/Lagos

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zantex_homes
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Build Frontend Assets

```bash
npm run build      # Production build
npm run dev         # Development watch mode
```

### 4. Start Development Server

```bash
php artisan serve
# Access at http://localhost:8000
```

### 5. Login with Demo Credentials

After seeding, use:
- **Email**: admin@demo.zantex.local
- **Password**: password

## Architecture

### Database Schema

The system uses a normalized relational schema with 200+ tables organized by functional domain:

```
Companies (multi-tenancy root)
├── Branches
├── Estates
│   ├── Properties
│   │   └── Units
│   │       └── Leases
│   └── Projects
├── Parties (CRM)
│   ├── Leads
│   ├── Offers
│   └── Sales
├── Accounts (Chart of Accounts)
│   └── Journal Entries
│       └── Journal Lines
├── Invoices & Payments
├── Loans & Investments
├── Staff & Payroll
└── Audit Logs
```

All tables:
- Include `public_id` (UUID) for public API exposure
- Have `company_id` foreign key for multi-company isolation
- Use `DECIMAL(19,4)` for all money fields (NGN currency)
- Include audit timestamps and status tracking

### Authentication & Authorization

#### User Model Enhanced Features
- MFA support (TOTP-ready)
- Company preference system
- Multi-company role assignments
- Fine-grained permission checking

#### Role/Permission System
- Roles are company-scoped
- 23+ permissions across 18 modules
- Permissions can be granted/revoked at runtime
- Support for hierarchical roles (Admin → Manager → Accountant)

#### API Security
- Laravel Sanctum for token-based auth
- Bearer tokens in Authorization header
- Automatic company context injection
- Cross-company access prevention

### Accounting Engine

#### Double-Entry Bookkeeping
```php
// Create and post journal entries
$service = new AccountingService($company);
$entry = $service->createJournalEntry([
    'entry_date' => today(),
    'description' => 'Rent received from Unit 1A',
    'lines' => [
        ['account_id' => $cashId, 'debit' => 150000, 'credit' => 0],
        ['account_id' => $incomeId, 'debit' => 0, 'credit' => 150000],
    ]
]);

// Automatic balance validation
$posted = $service->postJournalEntry($entry); // throws if debit != credit

// Support for reversals (adjustments)
$reversal = $service->reverseJournalEntry($entry, 'Over-payment correction');
```

#### Key Features
- Immutable posted journals (no retroactive edits)
- Balance validation (debit must equal credit)
- Entry reversal for corrections (creates reversing entry)
- Trial balance generation
- Account balance calculations by type
- Multi-currency support (base currency per company)

### Admin Portal

#### Real-Time Data Interfaces
- **Property Management** (`/admin/properties`)
  - Live search by code/name
  - Sortable columns (name, code, date)
  - Quick view/edit actions
  - Estate and type information
  - Status indicators

- **Journal Entries** (`/admin/accounting/journals`)
  - Real-time description search
  - Status filtering (draft/posted)
  - Posted by user tracking
  - Line count per entry
  - Quick post/reverse actions

#### Livewire Components
- Two-way data binding
- Preserve component state across filters
- Debounced search (live updates)
- Query string persistence (shareable links)
- Automatic pagination reset on filter

### API Endpoints (v1)

#### Authentication
```
POST /api/login           # Obtain Bearer token
```

#### Properties
```
GET    /api/properties              # List (paginated, 15 per page)
GET    /api/properties/{id}         # View details
POST   /api/properties              # Create
PUT    /api/properties/{id}         # Update
DELETE /api/properties/{id}         # Delete
```

#### Accounting
```
GET    /api/journals                 # List (20 per page)
GET    /api/journals/{id}            # View with lines
POST   /api/journals                 # Create draft
POST   /api/journals/{id}/post       # Post (validates balance)
POST   /api/journals/{id}/reverse    # Reverse (with audit trail)
GET    /api/accounting/trial-balance # Trial balance report
```

#### User & Company
```
GET    /api/user          # Current user profile with companies/roles
```

## Development Phases

### Phase 0: Architecture ✅
- [x] Laravel 11 setup with Sanctum
- [x] Complete MySQL schema (200+ tables)
- [x] 9 comprehensive migrations
- [x] 131 Eloquent models with relationships

### Phase 1: Platform Services ✅
- [x] User authentication (login/register/logout)
- [x] Company context middleware
- [x] Role/permission system
- [x] Responsive admin layout with dark mode
- [x] Design tokens (Tailwind CSS)

### Phase 2: Domain Services ✅
- [x] AccountingService (journal posting, reversal)
- [x] Reference data seeders (35+ items)
- [x] Demo company seeder with test data
- [x] Chart of accounts (12 accounts)
- [x] Role templates (5 roles × 23 permissions)

### Phase 3: API Layer ✅
- [x] RESTful endpoints with versioning (/api/v1)
- [x] Sanctum authentication
- [x] Request validation with FormRequest
- [x] Authorization policies
- [x] Resource classes for consistent responses

### Phase 4: Admin Portal ✅
- [x] Livewire components (PropertyList, JournalList)
- [x] Real-time search and filtering
- [x] Responsive data tables
- [x] Pagination
- [x] Dark mode support

### Phase 5: Customer Portals 🚧
- [ ] Public property listing site
- [ ] Tenant self-service portal
- [ ] Landlord dashboard
- [ ] Investor reports
- [ ] Agent commission tracking
- [ ] Staff portal

### Phase 6: Background Jobs 🚧
- [ ] Queue setup (Redis/Database)
- [ ] Paystack payment webhook processing
- [ ] Scheduled tasks (rent reminders, reports)
- [ ] File processing jobs

### Phase 7: Automated Tests ✅ (Partial)
- [x] Permission tests (role-based access)
- [x] Accounting tests (journal posting, balance)
- [x] API tests (endpoints, auth, validation)
- [ ] Concurrency tests (lease overlap prevention)
- [ ] Payment idempotency tests

### Phase 8: Documentation 🚧
- [x] Architecture decisions
- [x] Database schema reference
- [x] API documentation

### Phase 9: Deployment & Polish 🚧
- [ ] Production deployment scripts
- [ ] Security audit
- [ ] Performance optimization
- [ ] Accessibility audit (WCAG)

## Running Tests

```bash
# All tests
php artisan test

# Specific test file
php artisan test tests/Feature/PermissionTest.php

# With coverage
php artisan test --coverage

# Feature tests only
php artisan test --type=feature
```

## File Structure

```
zantex-homes-build/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/AuthController.php
│   │   │   └── Api/
│   │   │       ├── PropertyController.php
│   │   │       └── JournalController.php
│   │   ├── Middleware/SetCompanyContext.php
│   │   ├── Policies/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/ (131 models)
│   ├── Services/AccountingService.php
│   └── Livewire/Admin/
├── database/
│   ├── migrations/ (9 files, 200+ tables)
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── auth/
│   │   ├── admin/
│   │   └── livewire/
│   └── css/app.css
├── routes/
│   ├── web.php
│   └── api.php
├── tests/Feature/
│   ├── PermissionTest.php
│   ├── AccountingTest.php
│   └── ApiTest.php
├── MASTER_BUILD_PLAN.md
└── README.md
```

## Key Design Decisions

### 1. Double-Entry Accounting
**Why**: Ensures financial accuracy and auditability. Every transaction is recorded twice (debit = credit), preventing arithmetic errors.

**Implementation**:
- JournalLine balance validation before posting
- Immutable posted entries (reversal-only corrections)
- Account balance calculation by type (asset/liability/equity/revenue/expense)

### 2. Multi-Company Architecture
**Why**: Supports real estate groups managing multiple brands/subsidiaries independently.

**Implementation**:
- `company_id` on all business entity tables
- User role assignments per company
- Company context middleware (automatic company_id injection)
- Cross-company access denied by default

### 3. NGN Currency & Africa/Lagos Timezone
**Why**: Platform is built for Nigerian real estate market.

**Implementation**:
- `DECIMAL(19,4)` for all money fields (4 decimal places for Kobo)
- Company base_currency field (defaults to NGN)
- Timezone field per company (defaults to Africa/Lagos)
- Seed data with Nigerian states/LGAs

### 4. Livewire for Admin Portal
**Why**: Real-time interactivity without page reloads, while keeping code in PHP/Blade.

**Implementation**:
- PropertyList & JournalList components with live properties
- Debounced search ($model.live)
- Query string persistence for bookmarkable links
- Pagination maintained across filters

### 5. Sanctum for API Authentication
**Why**: Token-based auth suitable for mobile/external integrations without session overhead.

**Implementation**:
- User tokens with rate limiting
- Bearer token in Authorization header
- Automatic user context in requests
- Revocable tokens per integration

## Common Tasks

### Create a New Role with Permissions
```php
$role = Role::create([
    'company_id' => $company->id,
    'name' => 'Property Manager',
    'status' => 'active',
]);

$role->grantPermission('view_properties');
$role->grantPermission('create_property');
$role->grantPermission('edit_property');
```

### Assign User to Company with Role
```php
UserRole::create([
    'user_id' => $user->id,
    'company_id' => $company->id,
    'role_id' => $role->id,
]);

UserScope::create([
    'user_id' => $user->id,
    'company_id' => $company->id,
    'branch_id' => $branch->id, // Optional: limit to branch
]);
```

### Post a Journal Entry
```php
$service = new AccountingService($company);

$entry = $service->createJournalEntry([
    'entry_date' => today(),
    'description' => 'Rent payment received',
    'lines' => [
        ['account_id' => 1, 'debit' => 100000, 'credit' => 0],
        ['account_id' => 2, 'debit' => 0, 'credit' => 100000],
    ]
]);

$posted = $service->postJournalEntry($entry); // Balance automatically validated
```

### Get Account Balance
```php
$service = new AccountingService($company);
$balance = $service->getAccountBalance($account);
```

### Generate Trial Balance
```php
$service = new AccountingService($company);
$trialBalance = $service->getTrialBalance();
// Returns: ['balances' => [...], 'total_debit' => ..., 'total_credit' => ..., 'is_balanced' => bool]
```

## Troubleshooting

### Database Connection Error
```
"SQLSTATE[HY000]: General error: 1030 Got error..."
```
Ensure MySQL server is running and database exists:
```bash
mysql -u root -p
CREATE DATABASE zantex_homes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Migration Fails
Clear cache and retry:
```bash
php artisan cache:clear
php artisan migrate:fresh --seed
```

### Livewire Component Not Updating
Ensure npm dependencies are installed and assets compiled:
```bash
npm install
npm run build
php artisan view:clear
```

## Contribution Guidelines

1. Create a feature branch: `git checkout -b feature/module-name`
2. Commit with descriptive messages
3. Include tests for new functionality
4. Ensure all tests pass: `php artisan test`
5. Submit pull request with phase checklist

## License

ZANTEX HOMES is proprietary software. All rights reserved.

## Support

For issues, feature requests, or documentation clarifications:
- Create an issue in the project tracker
- Reference the relevant phase/module
- Include error logs if applicable

## Roadmap

- **Q1 2026**: Complete Phase 5 (customer portals)
- **Q2 2026**: Phase 6 (background jobs) + payment webhook integration
- **Q3 2026**: Phase 7-9 (testing, docs, deployment)
- **Q4 2026**: Production release v1.0

---

**Last Updated**: November 6, 2026  
**Current Phase**: 4/9 (Admin Portal)  
**Test Coverage**: 15+ feature tests, 8+ accounting tests  
**Database Tables**: 200+ entities  
**Modules Implemented**: 4/18 (Foundation complete)
