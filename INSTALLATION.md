# Installation Guide - Zantex Homes

## Prerequisites

- PHP 8.1 or higher
- MySQL 8.0 or higher (or MariaDB 10.5+)
- Node.js 18 or higher
- Composer 2.5 or higher
- Git

## Step 1: Clone & Install Dependencies

```bash
# Navigate to project directory
cd zantex-homes-build

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

## Step 2: Environment Configuration

```bash
# Copy example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Edit .env with your configuration:

```env
# Application
APP_NAME="Zantex Homes"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Africa/Lagos
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zantex_homes
DB_USERNAME=root
DB_PASSWORD=your_password

# Mail (if configured)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# Paystack (for payments)
PAYSTACK_PUBLIC_KEY=pk_test_xxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxx

# Session & Queue
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_DRIVER=database
```

## Step 3: Database Setup

```bash
# Create database (if not exists)
# On Windows PowerShell:
# mysql -u root -p -e "CREATE DATABASE zantex_homes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Or on macOS/Linux:
# mysql -u root -p -e 'CREATE DATABASE zantex_homes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed
```

## Step 4: Build Frontend Assets

```bash
# Development (with watch mode)
npm run dev

# Or production build
npm run build
```

## Step 5: Start Services

Open three terminal windows in the project directory:

### Terminal 1: Development Server
```bash
php artisan serve
# Server runs at http://localhost:8000
```

### Terminal 2: Queue Worker (for background jobs)
```bash
php artisan queue:work --queue=default
```

### Terminal 3: Scheduler (for scheduled tasks)
```bash
# For development testing, run this in watch mode:
php artisan schedule:work

# For production, add to crontab:
# * * * * * cd /path/to/zantex-homes && php artisan schedule:run >> /dev/null 2>&1
```

## Step 6: Login & Verify

1. Open http://localhost:8000 in your browser
2. Click "Login" on the public homepage
3. Use demo credentials:
   - **Email**: admin@demo.zantex.local
   - **Password**: password

4. You should see the admin dashboard

## Verification Steps

```bash
# Run tests to verify installation
php artisan test

# Verify database connection
php artisan tinker
# Then type: DB::connection()->getPdo()
# Should show connection details

# Check migrations
php artisan migrate:status

# Verify job queue (create a test job)
php artisan tinker
> dispatch(new \App\Jobs\SendEmailJob('test@example.com', 'Test', 'Test message', 'User', 1));
> exit
# Check database: SELECT * FROM jobs;
```

## Initial Setup

### 1. Create Company
1. Go to Admin > Companies
2. Click "Create Company"
3. Fill in company details (name, email, phone)
4. Set company timezone to Africa/Lagos
5. Save

### 2. Create Users with Roles
1. Go to Admin > Users
2. Click "Create User"
3. Assign roles: Tenant, Landlord, Investor, or Agent
4. Assign to company
5. Save

### 3. Create Properties
1. Go to Admin > Properties
2. Click "Create Property"
3. Fill property details
4. Assign landlord
5. Create units within property
6. Save

### 4. Create Leases
1. Go to Admin > Leases
2. Select property and unit
3. Select tenant
4. Set lease dates and monthly rent amount
5. Save

### 5. Generate Rent Invoices
1. Go to Admin > Rent Invoices
2. System auto-generates monthly invoices based on leases
3. Verify invoices for all active leases

## Production Deployment

### Server Setup (Ubuntu 20.04 LTS)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1
sudo apt install php8.1 php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl -y

# Install MySQL
sudo apt install mysql-server -y

# Install Node.js & npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### Application Deployment

```bash
# Clone repository
git clone <repository-url> /var/www/zantex-homes
cd /var/www/zantex-homes

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --omit=dev

# Configure
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder

# Build assets
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/zantex-homes
sudo chmod -R 755 /var/www/zantex-homes/storage
```

### Systemd Services

Create `/etc/systemd/system/zantex-queue.service`:
```
[Unit]
Description=Zantex Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/zantex-homes
ExecStart=/usr/bin/php artisan queue:work --queue=default,notifications
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Create `/etc/systemd/system/zantex-schedule.service`:
```
[Unit]
Description=Zantex Scheduler
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/zantex-homes
ExecStart=/usr/bin/php artisan schedule:work
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable zantex-queue zantex-schedule
sudo systemctl start zantex-queue zantex-schedule
```

### Nginx Configuration

Create `/etc/nginx/sites-available/zantex`:
```nginx
server {
    listen 80;
    server_name your-domain.com;

    root /var/www/zantex-homes/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/zantex /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## Troubleshooting

### Queue Jobs Not Processing
```bash
# Check if queue worker is running
ps aux | grep queue:work

# Restart queue worker
sudo systemctl restart zantex-queue

# View queue status
php artisan queue:work --queue=default --verbose
```

### Scheduler Not Running
```bash
# Verify scheduler is running
ps aux | grep schedule:work

# Check log files
tail -f storage/logs/laravel.log
```

### Database Connection Error
```bash
# Test database connection
php artisan tinker
> DB::connection()->getPdo()

# If fails, verify .env DATABASE_* settings
```

### Permission Denied Errors
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/zantex-homes
sudo chmod -R 755 /var/www/zantex-homes/storage
sudo chmod -R 755 /var/www/zantex-homes/bootstrap/cache
```

### Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Support

For issues or questions:
1. Check `storage/logs/laravel.log` for error messages
2. Review this installation guide
3. Verify all services are running (server, queue, scheduler)
4. Run tests: `php artisan test`
