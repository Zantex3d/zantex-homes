# Zantex Homes - Quick Start Guide

## ⚡ TL;DR - 5 Minutes to Running

### Prerequisites
- PHP 8.2+, Composer, Node.js, MySQL 8.0

### Setup
```bash
cd zantex-homes-build

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Start services (open 4 terminals)
Terminal 1: php artisan serve                 # Dev server at http://localhost:8000
Terminal 2: php artisan queue:work             # Background jobs
Terminal 3: php artisan schedule:work          # Scheduler
Terminal 4: npm run dev                        # Frontend assets
```

### Login Credentials
- **Email**: admin@demo.zantex.local
- **Password**: password

### Access Portals
- **Admin**: http://localhost:8000/admin
- **Tenant**: http://localhost:8000/tenant
- **Landlord**: http://localhost:8000/landlord
- **Investor**: http://localhost:8000/investor
- **Agent**: http://localhost:8000/agent
- **Public**: http://localhost:8000

---

## 📚 Documentation Files

| Document | Purpose | Read Time |
|----------|---------|-----------|
| **INSTALLATION.md** | Step-by-step setup (local & production) | 15 min |
| **FEATURES.md** | Complete feature checklist | 10 min |
| **ARCHITECTURE_DECISIONS.md** | Design rationale (10+ ADRs) | 10 min |
| **PROJECT_COMPLETION_CHECKLIST.md** | All phases verified | 5 min |
| **COMPLETION_SUMMARY.md** | Executive summary | 5 min |

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter PermissionTests

# With coverage
php artisan test --coverage
```

---

## 🏗️ Project Structure

```
app/
├── Http/Controllers/
│   ├── Public/               (6 controllers - public website)
│   ├── Tenant/               (6 controllers - tenant portal)
│   ├── Landlord/             (7 controllers - landlord portal)
│   ├── Investor/             (8 controllers - investor portal)
│   ├── Agent/                (7 controllers - agent portal)
│   └── Webhook/              (1 controller - Paystack)
├── Jobs/                     (6 background jobs)
├── Models/                   (136 models)
└── Http/Middleware/          (4 role-based middleware)

routes/
├── web.php                   (main routing)
├── api.php                   (API + webhooks)
├── tenant.php                (tenant portal routes)
├── landlord.php              (landlord portal routes)
├── investor.php              (investor portal routes)
├── agent.php                 (agent portal routes)
└── console.php               (scheduler configuration)

resources/views/
├── layouts/
│   ├── public.blade.php      (public website layout)
│   └── app.blade.php         (authenticated layout)
├── public/                   (public website views)
├── tenant/                   (tenant portal views)
├── landlord/                 (landlord portal views)
├── investor/                 (investor portal views)
└── agent/                    (agent portal views)

database/
├── migrations/               (200+ tables)
└── seeders/                  (demo data)

tests/
└── Feature/                  (20+ tests)
```

---

## 🚀 Production Deployment

See **INSTALLATION.md** section 6+ for:
- Ubuntu 20.04 setup
- Nginx configuration
- SSL/HTTPS setup
- Systemd services
- Supervisor for queue workers
- Log rotation

TL;DR:
```bash
# On production server
sudo apt-get update
sudo apt-get install php8.2-fpm mysql-server nginx nodejs

# Deploy
git clone <repo> /var/www/zantex-homes
cd /var/www/zantex-homes
composer install --no-dev
npm install && npm run build

php artisan migrate --force
php artisan queue:work --daemon
php artisan schedule:work
```

---

## 🔧 Configuration

All settings in **.env** (copy from .env.example):

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zantex_homes
DB_USERNAME=root

# Queue & Cache
QUEUE_CONNECTION=database    # Use 'redis' for production
CACHE_DRIVER=database         # Use 'redis' for production

# Paystack Webhook
PAYSTACK_PUBLIC_KEY=pk_test_...
PAYSTACK_SECRET_KEY=sk_test_...
WEBHOOK_SECRET=your_secret_here

# Mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# App
APP_TIMEZONE=Africa/Lagos
APP_CURRENCY=NGN
```

---

## 📊 What You Get

### 5 Customer Portals
✅ **Public Website**: Properties, projects, contact
✅ **Tenant Portal**: Lease, rent, maintenance
✅ **Landlord Portal**: Properties, collections, statements
✅ **Investor Portal**: Agreements, distributions, ROI
✅ **Agent Portal**: Leads, viewings, commissions

### Core Features
✅ 136 Eloquent models
✅ 200+ database tables
✅ Double-entry accounting
✅ Role-based access control
✅ Paystack webhook integration
✅ Background job scheduling
✅ Email notifications
✅ File uploads
✅ API with versioning
✅ Admin dashboard with Livewire

### Infrastructure
✅ Queue system (database/Redis)
✅ Scheduler (cron-compatible)
✅ Caching layer
✅ Error logging
✅ Audit trail
✅ Webhook receiver
✅ Multi-company isolation

---

## 📞 Common Commands

```bash
# Database
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Rollback migrations
php artisan migrate:refresh      # Fresh migrate
php artisan db:seed              # Seed demo data

# Queue
php artisan queue:work           # Start queue worker
php artisan queue:work --verbose # With debug output
php artisan queue:retry all      # Retry failed jobs

# Scheduler
php artisan schedule:work        # Start scheduler
php artisan schedule:run         # Run once (for cron)

# Debugging
php artisan tinker               # Interactive shell
php artisan migrate:refresh --seed --force  # Reset DB

# Testing
php artisan test                 # Run all tests
php artisan test --profile=all   # Show performance
php artisan dusk                 # Browser tests (if installed)

# Production
php artisan config:cache         # Cache config (production)
php artisan route:cache          # Cache routes (production)
php artisan view:cache           # Cache views (production)
php artisan optimize             # All optimizations
```

---

## 🐛 Troubleshooting

### Queue not working
```bash
# Check jobs table
php artisan migrate

# Check config
cat .env | grep QUEUE

# Restart worker
php artisan queue:work
```

### Scheduler not running
```bash
# Add to crontab
* * * * * cd /path/to/zantex-homes && php artisan schedule:run >> /dev/null 2>&1

# Or run manually
php artisan schedule:work
```

### Webhook not receiving
```bash
# Check endpoint is public (no auth required)
# curl -X POST http://localhost:8000/api/webhook/paystack

# Verify PAYSTACK_SECRET_KEY in .env
# Check WebhookEvent table for logs
php artisan tinker
> WebhookEvent::latest()->limit(5)->get();
```

### Storage permissions
```bash
# Make writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📖 Full Documentation

Read these in order:
1. **INSTALLATION.md** - Setup guide
2. **FEATURES.md** - What's included
3. **ARCHITECTURE_DECISIONS.md** - How it works
4. **README.md** - Overview & troubleshooting

---

## ✅ Verification Checklist

After setup, verify:
- [ ] `php artisan serve` runs without errors
- [ ] Database migrated: `php artisan migrate:status`
- [ ] Can login: admin@demo.zantex.local / password
- [ ] Public site loads: http://localhost:8000
- [ ] Tenant portal accessible: /tenant
- [ ] All tests pass: `php artisan test`
- [ ] Queue working: `php artisan queue:work` starts
- [ ] Scheduler working: `php artisan schedule:work` starts

---

## 🎯 Next Steps

1. ✅ Run local setup (5 minutes)
2. ✅ Review INSTALLATION.md (15 minutes)
3. ✅ Explore portals with demo data (10 minutes)
4. ✅ Configure Paystack keys
5. ✅ Deploy to staging
6. ✅ Run production deployment steps
7. ✅ Monitor logs and metrics

---

**Status**: Production-Ready
**Version**: 1.0.0
**Last Updated**: November 6, 2024
