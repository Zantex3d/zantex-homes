# Architecture Decision Records (ADR) - ZANTEX HOMES

## ADR-001: Double-Entry Accounting System

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Accounting Module (Phase 2)  

### Context
Real estate operations require precise financial tracking. Transactions must be auditable, and financial statements must be reliable. A single-entry system is prone to errors.

### Decision
Implement double-entry bookkeeping where every transaction creates equal debit and credit entries.

### Rationale
1. **Financial Accuracy**: Debit = Credit constraint prevents arithmetic errors
2. **Auditability**: Every transaction leaves an immutable audit trail
3. **Compliance**: Matches standards for financial reporting (IFRS, GAAP)
4. **Trial Balance**: Easy to verify overall ledger correctness

### Implementation
```php
// JournalEntry with JournalLine balance validation
if ($totalDebit != $totalCredit) {
    throw new RuntimeException("Entry must balance");
}
// Once posted, entries are immutable (reversal-only corrections)
```

### Consequences
- ✅ Reliable financial records
- ✅ Audit trail for every change
- ✅ Trial balance verification
- ⚠️ Slightly more complex than single-entry
- ⚠️ Requires discipline in GL setup

### Alternatives Considered
1. **Single-Entry**: Simpler but error-prone ❌
2. **Cash Basis Only**: Insufficient for accrual accounting ❌

---

## ADR-002: Multi-Company Data Isolation

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Architecture (Phase 0)  

### Context
Real estate groups operate multiple brands or subsidiaries. Data must be strictly isolated to prevent cross-company access and ensure compliance.

### Decision
Implement company-scoped multi-tenancy with `company_id` foreign key on all business entity tables.

### Rationale
1. **Data Isolation**: Cross-company queries impossible by design
2. **Security**: Role/permission checks are company-scoped
3. **Compliance**: Supports regulatory requirements for data separation
4. **Scalability**: Single database can serve multiple organizations

### Implementation
```php
// Every business entity has company_id
Schema::create('properties', function (Blueprint $table) {
    $table->foreignId('company_id')->constrained();
    // ... other fields
});

// Middleware injects company_id into all queries
UserScope::whereCompanyId($companyId)->first();
```

### Consequences
- ✅ Complete data isolation
- ✅ Security by design
- ✅ Shared infrastructure cost
- ⚠️ Requires discipline (no cross-company queries)
- ⚠️ Adds complexity to migrations

### Alternatives Considered
1. **Separate Databases**: More isolation but operational complexity ❌
2. **Row-Level Security**: Database-dependent, complex to audit ❌

---

## ADR-003: NGN Currency & Africa/Lagos Timezone

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Configuration (Phase 0)  

### Context
ZANTEX HOMES is built for Nigerian real estate market. Currency and timezone matter for business operations (rent due dates, payment calculations).

### Decision
- Use `DECIMAL(19,4)` for all money fields (Naira precision to Kobo)
- Default timezone to `Africa/Lagos`
- Support per-company currency preference

### Rationale
1. **Precision**: 4 decimal places sufficient for NGN (1 Kobo = 0.01 NGN)
2. **Correctness**: Africa/Lagos is Nigeria's official timezone
3. **Flexibility**: Allows future expansion to other African markets
4. **Display**: NGN symbol (₦) used throughout UI

### Implementation
```php
// Column definition
$table->decimal('amount', 19, 4);

// Company configuration
$company->base_currency = 'NGN';
$company->timezone = 'Africa/Lagos';

// Time display
$date->setTimezone('Africa/Lagos');
```

### Consequences
- ✅ Correct for Nigerian market
- ✅ No floating-point precision errors
- ✅ Clear date/time handling
- ⚠️ Requires explicit timezone handling
- ⚠️ May need adjustment for global expansion

### Alternatives Considered
1. **USD for Simplicity**: Lost local relevance ❌
2. **Float**: Precision errors, not suitable for money ❌
3. **UTC Only**: Confusing for Nigeria-based users ❌

---

## ADR-004: Livewire for Admin Portal

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Admin Portal (Phase 4)  

### Context
Admin portal needs real-time interactivity (live search, filtering). Options are Livewire (Laravel), HTMX, or full SPA (Vue/React).

### Decision
Use Laravel Livewire for reactive admin components.

### Rationale
1. **Full-Stack PHP**: Logic stays in PHP/Blade, no context switching
2. **No Build Step**: Works with existing Laravel setup
3. **Real-Time**: Live properties provide instant feedback
4. **Familiar**: Developers already know Laravel
5. **Productivity**: Faster development than SPA

### Implementation
```php
// Livewire component with live properties
public $search = '';
public function updatingSearch() { $this->resetPage(); }
// Two-way binding in Blade: wire:model.live="search"
```

### Consequences
- ✅ Fast development
- ✅ Full-stack PHP consistency
- ✅ Real-time without JavaScript complexity
- ⚠️ Requires Alpine.js for additional interactivity
- ⚠️ Higher server load than SPA
- ⚠️ Network latency on slow connections

### Alternatives Considered
1. **HTMX**: Works but less integrated than Livewire ❌
2. **Vue.js/React**: More power but more complexity ⚠️

---

## ADR-005: Sanctum for API Authentication

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: API (Phase 3)  

### Context
Need stateless authentication for mobile/external integrations. Options: Sessions, JWT, OAuth2, Sanctum.

### Decision
Use Laravel Sanctum for API token-based authentication.

### Rationale
1. **Official**: Recommended by Laravel team
2. **Stateless**: No session overhead
3. **Revocable**: Tokens can be revoked per integration
4. **Secure**: CSRF protection included
5. **Simple**: Less setup than OAuth2
6. **Rate Limiting**: Built-in throttle support

### Implementation
```php
// Create token
$token = $user->createToken('integration-name')->plainTextToken;

// Use token
Authorization: Bearer {$token}

// Verify in middleware
middleware('auth:sanctum')
```

### Consequences
- ✅ Secure token-based auth
- ✅ Easy integration testing
- ✅ Per-integration token management
- ⚠️ Token storage on client side
- ⚠️ No built-in refresh tokens
- ⚠️ Overkill for pure SPA use case

### Alternatives Considered
1. **Sessions**: Stateful, not ideal for distributed systems ❌
2. **JWT**: More complex, Sanctum simpler for Laravel ✅ (chose Sanctum)
3. **OAuth2**: Overkill for internal API ❌

---

## ADR-006: UUID for Public IDs

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Database (Phase 0)  

### Context
API returns entity IDs. Using auto-increment IDs reveals system structure and allows enumeration attacks. Options: UUID, ULID, hash.

### Decision
Use UUID (`public_id`) as external identifier, keep auto-increment ID internal.

### Rationale
1. **Security**: Prevents ID enumeration
2. **Uniqueness**: Globally unique across systems
3. **Standard**: Well-established, widely supported
4. **Privacy**: Doesn't reveal creation order
5. **Shareability**: Safe to share in URLs/APIs

### Implementation
```php
$table->uuid('public_id')->unique();

// In API responses
return ['id' => $model->public_id, ...];

// URL routes
Route::get('/api/properties/{property}', function (Property $property) {
    // Implicit binding by public_id
});
```

### Consequences
- ✅ Better security
- ✅ Prevents ID enumeration
- ✅ Safe for public APIs
- ⚠️ Longer IDs in URLs
- ⚠️ Slight performance overhead
- ⚠️ Non-sequential (harder to debug)

### Alternatives Considered
1. **Auto-Increment Only**: Simple but security risk ❌
2. **ULID**: Sortable but less standard ⚠️
3. **Hash**: Works but custom implementation ⚠️

---

## ADR-007: Laravel Migrations for Schema

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Database (Phase 0)  

### Context
Need to manage 200+ tables across multiple domains. Options: Raw SQL, migrations, schema builder.

### Decision
Use Laravel migrations (Illuminate\Database\Schema) for all schema management.

### Rationale
1. **Versioning**: Track schema changes over time
2. **Rollback**: Revert changes if needed
3. **Consistency**: Same approach across team
4. **Automation**: Run migrations in CI/CD
5. **Readability**: Clear migration sequence

### Implementation
```php
// Migrations organized by domain
2026_11_06_000_create_companies_tables
2026_11_06_001_create_roles_permissions_tables
2026_11_06_002_create_property_tables
// ... etc
```

### Consequences
- ✅ Full version control
- ✅ Easy rollback
- ✅ Repeatable on different environments
- ⚠️ Large initial migrations (but grouped by domain)
- ⚠️ Requires discipline to name properly

### Alternatives Considered
1. **Raw SQL Files**: No automatic versioning ❌
2. **Doctrine ORM**: Overkill, Laravel has Schema ❌

---

## ADR-008: Company-Scoped Roles & Permissions

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Authorization (Phase 1)  

### Context
Real estate groups have different staff structures per company. Permissions must be per-company, not global.

### Decision
Implement roles and permissions at company level. Users can have different roles in different companies.

### Rationale
1. **Flexibility**: Different companies can have different structures
2. **Security**: Permissions isolated to company
3. **Scalability**: Supports multi-tenancy
4. **Real-World**: Matches actual org structures

### Implementation
```php
// Role is company-scoped
$role->company_id; // Not global

// User can have different roles per company
UserRole::where(['user_id' => $user, 'company_id' => $company]);

// Permission check includes company
$user->hasPermission('create_property', $companyId);
```

### Consequences
- ✅ Flexible permissions per company
- ✅ Supports complex org structures
- ⚠️ More complex than global roles
- ⚠️ Requires explicit company context
- ⚠️ Can't easily grant global permissions

### Alternatives Considered
1. **Global Roles**: Simpler but inflexible ❌
2. **ACL Lists**: Flexible but complex ⚠️

---

## ADR-009: Test-Driven Development

**Status**: Accepted  
**Date**: 2026-11-06  
**Scope**: Quality Assurance (Phase 7)  

### Context
System has critical business logic (accounting, permissions, payments). Bugs can cause financial loss.

### Decision
Write comprehensive feature tests for all critical paths. Minimum test coverage: permissions, accounting, API.

### Rationale
1. **Critical Logic**: Accounting errors = financial loss
2. **Regression Prevention**: Tests catch breaking changes
3. **Documentation**: Tests document expected behavior
4. **Confidence**: Safe refactoring with test coverage
5. **Quality**: Catches edge cases early

### Implementation
```php
// Feature tests for critical paths
- PermissionTest: Role/permission system
- AccountingTest: Journal posting, balance
- ApiTest: Endpoint auth, validation

// Run before commit
php artisan test --fail-on-risky
```

### Consequences
- ✅ Higher code quality
- ✅ Fewer bugs in production
- ✅ Safe refactoring
- ⚠️ Takes time to write tests
- ⚠️ Requires discipline to maintain
- ⚠️ False confidence if tests are poor

### Alternatives Considered
1. **No Tests**: Risk of bugs ❌
2. **Manual Testing Only**: Doesn't scale ❌
3. **End-to-End Only**: Slower feedback ⚠️

---

## Summary of Design Decisions

| ADR | Decision | Rationale |
|-----|----------|-----------|
| 001 | Double-Entry Accounting | Financial accuracy & audit trail |
| 002 | Multi-Company Architecture | Data isolation & compliance |
| 003 | NGN & Africa/Lagos | Built for Nigerian market |
| 004 | Livewire for Admin | Fast PHP development |
| 005 | Sanctum API Auth | Stateless, secure, simple |
| 006 | UUID for Public IDs | Security & privacy |
| 007 | Laravel Migrations | Versioning & repeatability |
| 008 | Company-Scoped Roles | Flexibility & security |
| 009 | Test-Driven Dev | Quality & confidence |

---

**Document Version**: 1.0  
**Last Updated**: November 6, 2026  
**Status**: Complete for Phases 0-4
