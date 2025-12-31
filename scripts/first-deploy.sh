#!/bin/bash

# =============================================================================
# First Deploy Script - Run after initial rsync deployment
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

APP_DIR="/var/www/coupon-system"

echo -e "${GREEN}Running first deployment setup...${NC}"

cd $APP_DIR

# Set permissions
echo -e "${YELLOW}Setting permissions...${NC}"
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 storage bootstrap/cache

# Create storage directories if they don't exist
mkdir -p storage/logs
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/app/public

# Set storage permissions
chmod -R 775 storage
chown -R www-data:www-data storage

# Copy production .env if .env doesn't exist
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file from production template...${NC}"
    cp scripts/.env.production .env
    echo -e "${RED}IMPORTANT: Edit /var/www/coupon-system/.env with your actual credentials!${NC}"
fi

# Generate application key
echo -e "${YELLOW}Generating application key...${NC}"
php artisan key:generate --force

# Run migrations
echo -e "${YELLOW}Running database migrations...${NC}"
php artisan migrate --force

# Create storage symlink
echo -e "${YELLOW}Creating storage symlink...${NC}"
php artisan storage:link --force

# Cache configuration
echo -e "${YELLOW}Caching configuration...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup Supervisor
echo -e "${YELLOW}Setting up Supervisor...${NC}"
cp scripts/supervisor.conf /etc/supervisor/conf.d/coupon-system.conf
supervisorctl reread
supervisorctl update
supervisorctl start coupon-queue:*

# Restart services
echo -e "${YELLOW}Restarting services...${NC}"
systemctl restart php8.3-fpm
systemctl restart nginx

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}First deployment completed!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "${YELLOW}Your application should now be accessible at:${NC}"
echo "http://178.128.98.27"
echo ""
echo -e "${YELLOW}Don't forget to:${NC}"
echo "1. Edit .env with your actual database password"
echo "2. Run: php artisan migrate --force (if not done)"
echo "3. Create admin user: php artisan tinker"
echo ""
