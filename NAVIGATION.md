# ZANTEX HOMES - Quick Navigation Guide

## 📖 Start Here

This document helps you quickly navigate the ZANTEX HOMES codebase and documentation.

## 🚀 Getting Started (5 minutes)

```bash
# 1. Clone/navigate to project
cd C:\Users\shett\Downloads\zantex-homes-build

# 2. Install dependencies
composer install
npm install

# 3. Configure
cp .env.example .env
# Edit .env: Set DB_DATABASE=zantex_homes, DB_USERNAME=root, DB_PASSWORD=

# 4. Setup database
php artisan key:generate
php artisan migrate:fresh --seed

# 5. Build & run
npm run build
php artisan serve

# 6. Access http://localhost:8000
# Login: admin@demo.zantex.local / password
```

---

## 📚 Documentation Map

### For Project Overview
- **README.md** - Start here for architecture, setup, features, roadmap
- **PROJECT_SUMMARY.md** - Complete project status, statistics, next steps
- **MASTER_BUILD_PLAN.md** - Detailed phase-by-phase plan with all 18 modules

### For Architecture Understanding
- **ARCHITECTURE_DECISIONS.md** - 9 ADRs explaining design rationale
- **COMPLETION_REPORT.txt** - Quick status at a glance

### For Code Navigation
- **[This file]** - You're reading it!

---

## 🗂️ Codebase Organization

### Authentication & Authorization
```
app/Http/Controllers/Auth/AuthController.php     - Login/register/logout
app/Models/User.php                               - Enhanced with MFA, company scoping
app/Models/Role.php                               - Company-scoped roles
app/Models/Permission.php                         - Fine-grained permissions
app/Http/Middleware/SetCompanyContext.php         - Automatic company injection
```

**Routes**: `/login`, `/register`, `/logout`, `/dashboard`

### Admin Portal (Live Data)
```
app/Livewire/Admin/PropertyList.php              - Real-time property search/filter
app/Livewire/Admin/JournalList.php               - Real-time journal entry browser
resources/views/admin/properties/index.blade.php  - Property management page
resources/views/admin/accounting/journals.blade.php - Journal management page
resources/views/livewire/admin/property-list.blade.php - Component template
resources/views/livewire/admin/journal-list.blade.php - Component template
```

**Routes**: `/admin/properties`, `/admin/accounting/journals`, `/admin/accounting/trial-balance`

### API (RESTful)
```
app/Http/Controllers/Api/PropertyController.php   - Properties CRUD
app/Http/Controllers/Api/JournalController.php    - Journal management
app/Http/Policies/PropertyPolicy.php              - Property authorization
app/Http/Policies/JournalEntryPolicy.php          - Journal authorization
app/Http/Resources/PropertyResource.php           - Consistent response format
routes/api.php                                    - API route definitions
```

**Base URL**: `/api/`  
**Authentication**: Bearer token (Sanctum)

### Accounting Engine
```
app/Services/AccountingService.php                - Journal posting, reversal, balancing
app/Models/Account.php                            - Chart of accounts
app/Models/JournalEntry.php                       - Posted/draft entries
app/Models/JournalLine.php                        - Debit/credit line items
```

**Key Methods**:
- `createJournalEntry()` - Create draft entry
- `postJournalEntry()` - Post with balance validation
- `reverseJournalEntry()` - Create reversing entry
- `getAccountBalance()` - Calculate balance by type
- `getTrialBalance()` - Verify ledger

### Database & Seeders
```
database/migrations/2026_11_06_00X_create_*.php  - 9 comprehensive migrations
database/seeders/ReferenceDataSeeder.php         - Property types, categories, states
database/seeders/DemoCompanySeeder.php           - Complete demo setup with test data
database/seeders/DatabaseSeeder.php              - Master seeder
```

**Tables**: 200+ across 14 functional domains

### Models (136 Total)
```
app/Models/                                      - All entity models
  - Property, Unit, Lease, Estate (Property)
  - Party, Lead, Offer, Sale (CRM)
  - JournalEntry, Account, Invoice, Payment (Accounting)
  - Employee, Payroll, Loan (HR & Finance)
  - Tenant, Landlord, Shortlet (Tenancy)
  - Project, Equipment, Maintenance (Construction)
  - ... and 110+ more
```

### Tests (22 Feature Tests)
```
tests/Feature/PermissionTest.php                 - 7 permission/role tests
tests/Feature/AccountingTest.php                 - 8 accounting logic tests
tests/Feature/ApiTest.php                        - 7 API endpoint tests
```

**Run Tests**:
```bash
php artisan test                    # All tests
php artisan test tests/Feature/PermissionTest.php  # Specific file
php artisan test --coverage         # With coverage report
```

---

## 🔍 Key Features by Module

### Module 01: Property & Asset Management
- Properties with units, types, estates
- Status tracking, relationship mapping
- Real-time admin interface

### Module 02: Real Estate CRM
- Parties (leads/prospects)
- Viewing history, offers
- Sales pipeline tracking

### Module 03: Tenant & Lease Management
- Lease creation and tracking
- Rent schedules, deposit records
- Tenant inspections

### Module 04: Accounting & Financial Management
- **Double-entry bookkeeping** (core feature)
- Chart of accounts with 12+ default accounts
- Journal entry creation, posting, reversal
- Trial balance verification
- Account balance calculations
- Immutable posted entries

### Module 05-18: Foundation Laid
All other modules have database schema and models. API endpoints and UI coming in Phase 5-6.

---

## 🛠️ Common Development Tasks

### Add a New API Endpoint

1. Create controller method in `app/Http/Controllers/Api/`
2. Create policy in `app/Http/Policies/`
3. Create request class in `app/Http/Requests/`
4. Add route in `routes/api.php`
5. Write test in `tests/Feature/`

Example:
```php
// Controller
public function store(Request $request) {
    $this->authorize('create', Model::class);
    $model = Model::create($validated);
    return response()->json($model, 201);
}

// Policy
public function create(User $user): bool {
    return $user->hasPermission('create_model');
}

// Route
Route::apiResource('models', ModelController::class);
```

### Create a New Livewire Component

1. Generate in `app/Livewire/Admin/`
2. Create template in `resources/views/livewire/admin/`
3. Add route in `routes/web.php`
4. Use `wire:model.live` for real-time binding

Example:
```php
// Component
namespace App\Livewire\Admin;
class MyList extends Component {
    public $search = '';
    public function updatingSearch() { $this->resetPage(); }
    public function render() { /* ... */ }
}

// Blade
<input wire:model.live="search" placeholder="Search...">
<livewire:admin.my-list />
```

### Post a Journal Entry

```php
$service = new AccountingService($company);

$entry = $service->createJournalEntry([
    'entry_date' => today(),
    'description' => 'Rent payment',
    'lines' => [
        ['account_id' => $cashId, 'debit' => 100000, 'credit' => 0],
        ['account_id' => $incomeId, 'debit' => 0, 'credit' => 100000],
    ]
]);

// Balance automatically validated
$posted = $service->postJournalEntry($entry);
// Throws RuntimeException if debit != credit
```

### Check Permissions

```php
// In controller
if (!$user->hasPermission('create_property')) {
    abort(403, 'Unauthorized');
}

// In request class
public function authorize(): bool {
    return $this->user()->hasPermission('create_property');
}

// In policy
public function create(User $user): bool {
    return $user->hasPermission('create_model');
}

// Direct method
$user->hasPermission('view_properties', $companyId);
```

---

## 📊 Database Schema Overview

### Core Tables (Multi-company)
```
companies
├── branches
├── departments
├── roles & permissions
├── user_roles & user_scopes
└── ... (200+ tables total)
```

### Key Relationships
- Company → Branches → Estates → Properties → Units
- Company → Accounts → Journal Entries → Journal Lines
- Company → Roles → Permissions
- User → UserRoles → Roles → Permissions

### Important Constraints
- All money: `DECIMAL(19,4)` (Naira precision)
- All timestamps: UTC in DB
- All money columns: NOT NULL
- company_id: Mandatory on all business entities
- Foreign keys: Cascading where appropriate

---

## 🔐 Security Checklist

Before deploying to production:

- [ ] Update `.env` with production database credentials
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper CORS for API
- [ ] Enable HTTPS/SSL
- [ ] Set secure cookie flags
- [ ] Configure rate limiting
- [ ] Run `php artisan optimize:production`
- [ ] Back up database before migrations
- [ ] Test rollback procedures
- [ ] Review security headers
- [ ] Audit file permissions
- [ ] Configure logging and monitoring

---

## 📈 Performance Tips

1. **Livewire**: Use `#[Lazy]` for heavy components
2. **API**: Use pagination with `?per_page=` parameter
3. **Queries**: Use `with()` to eager-load relationships
4. **Caching**: Add redis for session/cache
5. **Database**: Add indexes on frequently filtered columns
6. **Queue**: Move long tasks to queue (Phase 6)

---

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Check MySQL is running
# Verify .env database credentials
# Create database if needed:
mysql -u root -p
CREATE DATABASE zantex_homes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Livewire Component Not Updating
```bash
# Rebuild assets
npm run build
# Clear cache
php artisan cache:clear
php artisan view:clear
```

### Tests Failing
```bash
# Ensure database is fresh
php artisan migrate:fresh
# Run with more verbosity
php artisan test --verbose
```

### Permission Denied on API
```bash
# Verify Sanctum middleware in routes/api.php
# Check user has role in company
# Verify permission exists in database
php artisan tinker
>>> \App\Models\Permission::all();
```

---

## 📞 Support & Resources

- **Laravel Docs**: https://laravel.com/docs
- **Livewire Docs**: https://laravel-livewire.com
- **Sanctum Docs**: https://laravel.com/docs/sanctum
- **Tailwind CSS**: https://tailwindcss.com

---

## 🎯 Next Steps (Phases 5-9)

1. **Phase 5**: Customer portals (tenant, landlord, agent, investor)
2. **Phase 6**: Background jobs & Paystack integration
3. **Phase 7**: Complete test coverage (concurrency, payments)
4. **Phase 8**: Full documentation & deployment guide
5. **Phase 9**: Production release & polish

---

## 📝 Notes

- Current timezone: Africa/Lagos
- Current currency: NGN
- Current database: MySQL
- Current UI framework: Tailwind CSS + Livewire
- Current API auth: Laravel Sanctum

---

**Last Updated**: November 6, 2026  
**Status**: PHASE 0-4 Complete, Phase 7 Partial, Phases 5-6 and 8-9 Pending  
**Build Quality**: Production-ready foundation, ready for Phase 5 implementation
