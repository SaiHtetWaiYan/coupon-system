# Coupon System - Coupon Campaign Management System

A Laravel-based coupon campaign management system with real-time updates powered by Laravel Reverb.

## Features

- **Coupon Campaign Management**: Create, edit, and manage coupon campaigns
- **Coupon Generation**: Automatically generate unique coupon codes
- **Coupon Redemption**: Users can redeem coupons through a dedicated interface
- **Real-time Updates**: Live updates using Laravel Reverb WebSockets
- **User Online Status**: Track user online/offline status in real-time
- **Admin Dashboard**: Comprehensive dashboard with live statistics

## Requirements

- PHP 8.5+
- Laravel 12
- Node.js & NPM
- MySQL/PostgreSQL

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd coupon-system

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build
```

## Real-time Features

This application uses Laravel Reverb for real-time WebSocket functionality.

### Configuration

Add the following to your `.env` file:

```env
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="your-domain.test"
REVERB_PORT=6001
REVERB_SCHEME=https
```

### Starting Services

```bash
# Start Reverb WebSocket server on port 6001
php artisan reverb:start --port=6001

# Start queue worker (required for broadcasting)
php artisan queue:work

# Start development server (optional)
npm run dev
```

### Real-time Events

#### CouponRedeemed Event
Broadcasts when a coupon is redeemed, updating:
- Dashboard statistics (unused/used coupons, redeemed value)
- Campaign coupon list (status, used_at, redeemed_by)
- Disable button visibility

**Channel**: `coupons`
**Event**: `coupon.redeemed`

**Payload**:
```json
{
    "coupon_id": 1,
    "coupon_code": "ABC12345",
    "coupon_value": 500,
    "campaign_id": 1,
    "status": "used",
    "used_at": "2024-01-01T12:00:00.000Z",
    "redeemed_by": {
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

#### User Presence Channel
Tracks online/offline status for admin user management.

**Channel**: `presence-users`

### Files Structure

```
app/
├── Events/
│   └── CouponRedeemed.php          # Coupon redemption broadcast event
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── DashboardController.php
│   │   └── CouponRedemptionController.php
│   └── Middleware/
│       └── UpdateLastSeen.php       # Tracks user last activity
└── Models/
    ├── Coupon.php
    ├── CouponCampaign.php
    └── User.php

resources/
├── js/
│   ├── app.js                       # Alpine.js + Echo setup
│   └── echo.js                      # Laravel Echo configuration
└── views/
    └── admin/
        ├── dashboard.blade.php      # Real-time dashboard
        └── coupon-campaigns/
            └── show.blade.php       # Real-time coupon list

routes/
└── channels.php                     # Broadcast channel authorization
```

## Admin Dashboard

The admin dashboard displays real-time statistics:

- **Total Campaigns**: Number of coupon campaigns
- **Active Campaigns**: Currently active campaigns
- **Total Coupons**: All coupons across campaigns
- **Unused Coupons**: Available coupons (updates in real-time)
- **Used Coupons**: Redeemed coupons (updates in real-time)
- **Expired Coupons**: Expired coupons
- **Total Campaign Value**: Sum of all campaign values
- **Redeemed Value**: Total value redeemed (updates in real-time)

## User Online Status

Admin can see user online/offline status with:
- Green dot + "Online" for currently connected users
- Gray dot + "Last seen X ago" for offline users

This uses:
- Presence channel for real-time online tracking
- `last_seen_at` database column for fallback

## Server Setup (Production)

### Server Requirements

- Ubuntu 22.04+ or similar Linux distribution
- PHP 8.5+ with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL, SQLite/MySQL
- Composer 2.x
- Node.js 18+ & NPM
- Nginx or Apache
- Supervisor (for process management)
- SSL Certificate (Let's Encrypt recommended)

### 1. Install PHP & Extensions

```bash
sudo apt update
sudo apt install php8.3-fpm php8.3-cli php8.3-common php8.3-mysql php8.3-sqlite3 \
    php8.3-xml php8.3-curl php8.3-mbstring php8.3-zip php8.3-bcmath php8.3-gd
```

### 2. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Install Node.js

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 4. Clone & Setup Application

```bash
cd /var/www
git clone <repository-url> coupon-system
cd coupon-system

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure .env with production values
nano .env

# Run migrations
php artisan migrate --force

# Set permissions
sudo chown -R www-data:www-data /var/www/coupon-system
sudo chmod -R 755 /var/www/coupon-system
sudo chmod -R 775 /var/www/coupon-system/storage
sudo chmod -R 775 /var/www/coupon-system/bootstrap/cache
```

### 5. Nginx Configuration

Create `/etc/nginx/sites-available/coupon-system`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/coupon-system/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# WebSocket Proxy for Reverb
server {
    listen 6001 ssl http2;
    server_name your-domain.com;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
        proxy_send_timeout 60s;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/coupon-system /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 7. Supervisor Configuration

Install Supervisor:

```bash
sudo apt install supervisor
```

Create `/etc/supervisor/conf.d/coupon-system.conf`:

```ini
[program:coupon-reverb]
process_name=%(program_name)s
command=php /var/www/coupon-system/artisan reverb:start --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/coupon-system/storage/logs/reverb.log
stopwaitsecs=3600

[program:coupon-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/coupon-system/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/coupon-system/storage/logs/queue.log
stopwaitsecs=3600
```

Start Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

### 8. Cron Job (Scheduler)

Add to crontab (`crontab -e`):

```cron
* * * * * cd /var/www/coupon-system && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Production Environment Variables

Update `.env` for production:

```env
APP_NAME="Coupon System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

REVERB_HOST="your-domain.com"
REVERB_PORT=6001
REVERB_SCHEME=https

VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 10. Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
```

### Useful Commands

```bash
# Check Supervisor status
sudo supervisorctl status

# Restart Reverb
sudo supervisorctl restart coupon-reverb

# Restart Queue Workers
sudo supervisorctl restart coupon-queue:*

# View logs
tail -f /var/www/coupon-system/storage/logs/laravel.log
tail -f /var/www/coupon-system/storage/logs/reverb.log
tail -f /var/www/coupon-system/storage/logs/queue.log

# Clear all caches
php artisan optimize:clear
```

## License

This project is proprietary software.
