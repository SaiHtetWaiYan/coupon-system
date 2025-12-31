# Deployment Guide - DigitalOcean VPS

This guide covers deploying the Coupon System to a DigitalOcean droplet with automated CI/CD.

## Prerequisites

- DigitalOcean Droplet (Ubuntu 24.04 LTS)
- GitHub repository with this project
- SSH key added to both GitHub and your droplet

## Step 1: Configure GitHub Secrets

Go to your GitHub repository → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Add these secrets:

| Secret Name | Value |
|-------------|-------|
| `SERVER_IP` | `178.128.98.27` |
| `SSH_USER` | `root` |
| `SSH_PRIVATE_KEY` | Your Mac's private SSH key (see below) |

### Get your SSH private key:

```bash
# On your Mac, copy the private key
cat ~/.ssh/id_rsa
# Or if using ed25519:
cat ~/.ssh/id_ed25519
```

Copy the entire output (including `-----BEGIN ... KEY-----` and `-----END ... KEY-----`) and paste it as the `SSH_PRIVATE_KEY` secret.

## Step 2: Initial Server Setup

SSH into your droplet and run the server setup:

```bash
# SSH into your droplet
ssh root@178.128.98.27

# Create the web directory
mkdir -p /var/www/coupon-system
cd /var/www/coupon-system

# Clone the repository (using deploy key)
git clone git@github.com:YOUR_USERNAME/coupon-system.git .

# Or download just the scripts folder first
# Then run server setup
chmod +x scripts/server-setup.sh
sudo bash scripts/server-setup.sh
```

## Step 3: Configure MySQL

```bash
# Edit the MySQL setup script with your secure password
nano scripts/mysql-setup.sh
# Change: DB_PASS="your_secure_password_here"

# Run MySQL setup
sudo bash scripts/mysql-setup.sh
```

## Step 4: Configure Nginx

```bash
# Copy Nginx configuration
sudo cp scripts/nginx.conf /etc/nginx/sites-available/coupon-system

# Enable the site
sudo ln -s /etc/nginx/sites-available/coupon-system /etc/nginx/sites-enabled/

# Remove default site
sudo rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
sudo nginx -t && sudo systemctl reload nginx
```

## Step 5: Configure Environment

```bash
cd /var/www/coupon-system

# Copy production environment template
cp scripts/.env.production .env

# Edit with your actual values
nano .env

# Update these values:
# - APP_KEY (will be generated)
# - DB_PASSWORD (your MySQL password)
# - Any other settings as needed
```

## Step 6: First Deployment

```bash
# Run the first deployment script
sudo bash scripts/first-deploy.sh
```

## Step 7: Verify Deployment

Visit `http://178.128.98.27` in your browser.

## Automatic Deployments

After initial setup, every push to the `main` branch will:

1. Run tests
2. Build assets
3. Deploy to your server automatically

## Manual Deployment

You can also trigger a deployment manually:
1. Go to GitHub → Actions → Deploy to DigitalOcean
2. Click "Run workflow"

## Useful Commands

```bash
# View application logs
tail -f /var/www/coupon-system/storage/logs/laravel.log

# View queue worker logs
tail -f /var/www/coupon-system/storage/logs/queue.log

# Restart queue workers
sudo supervisorctl restart coupon-queue:*

# Clear all caches
cd /var/www/coupon-system
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/coupon-system
sudo chmod -R 775 /var/www/coupon-system/storage
sudo chmod -R 775 /var/www/coupon-system/bootstrap/cache
```

### 500 Error
```bash
# Check Laravel logs
tail -100 /var/www/coupon-system/storage/logs/laravel.log

# Check Nginx logs
tail -100 /var/log/nginx/error.log
```

### Database Connection Issues
```bash
# Test MySQL connection
mysql -u coupon_user -p -e "SHOW DATABASES;"
```

### Chromium Issues (for coupon images)
```bash
# Verify Chromium is installed
which chromium-browser
# Should output: /usr/bin/chromium-browser

# Test Chromium
chromium-browser --version
```

## File Structure

```
scripts/
├── server-setup.sh      # Initial server setup (PHP, MySQL, Nginx, etc.)
├── mysql-setup.sh       # MySQL database and user creation
├── nginx.conf           # Nginx server configuration
├── .env.production      # Production environment template
├── supervisor.conf      # Supervisor config for queue workers
└── first-deploy.sh      # First deployment setup script
```
