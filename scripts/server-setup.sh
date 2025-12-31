#!/bin/bash

# =============================================================================
# Server Setup Script for Laravel Coupon System
# Ubuntu 24.04 LTS with PHP 8.3, MySQL 8, Nginx, Chromium
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting server setup...${NC}"

# Update system
echo -e "${YELLOW}Updating system packages...${NC}"
apt update && apt upgrade -y

# Install essential packages
echo -e "${YELLOW}Installing essential packages...${NC}"
apt install -y \
    software-properties-common \
    curl \
    wget \
    git \
    unzip \
    zip \
    acl \
    supervisor \
    ufw

# Add PHP repository
echo -e "${YELLOW}Adding PHP repository...${NC}"
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.3 and extensions
echo -e "${YELLOW}Installing PHP 8.3 and extensions...${NC}"
apt install -y \
    php8.3-fpm \
    php8.3-cli \
    php8.3-common \
    php8.3-mysql \
    php8.3-pgsql \
    php8.3-sqlite3 \
    php8.3-xml \
    php8.3-curl \
    php8.3-gd \
    php8.3-imagick \
    php8.3-mbstring \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-intl \
    php8.3-readline \
    php8.3-soap \
    php8.3-redis

# Install Nginx
echo -e "${YELLOW}Installing Nginx...${NC}"
apt install -y nginx

# Install MySQL 8
echo -e "${YELLOW}Installing MySQL 8...${NC}"
apt install -y mysql-server

# Start and enable MySQL
systemctl start mysql
systemctl enable mysql

# Install Chromium for OpenGraph image generation
echo -e "${YELLOW}Installing Chromium browser...${NC}"
apt install -y chromium-browser

# Install Node.js 20 LTS
echo -e "${YELLOW}Installing Node.js 20...${NC}"
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Install Composer
echo -e "${YELLOW}Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create web directory
echo -e "${YELLOW}Creating web directory...${NC}"
mkdir -p /var/www/coupon-system
chown -R www-data:www-data /var/www/coupon-system

# Configure PHP-FPM
echo -e "${YELLOW}Configuring PHP-FPM...${NC}"
cat > /etc/php/8.3/fpm/pool.d/www.conf << 'EOF'
[www]
user = www-data
group = www-data
listen = /run/php/php8.3-fpm.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 25
pm.start_servers = 5
pm.min_spare_servers = 2
pm.max_spare_servers = 10
pm.max_requests = 500
php_admin_value[memory_limit] = 256M
php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 50M
php_admin_value[max_execution_time] = 300
EOF

# Configure PHP CLI
echo -e "${YELLOW}Configuring PHP settings...${NC}"
sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.3/cli/php.ini
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/' /etc/php/8.3/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 50M/' /etc/php/8.3/fpm/php.ini

# Configure firewall
echo -e "${YELLOW}Configuring firewall...${NC}"
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# Restart services
echo -e "${YELLOW}Restarting services...${NC}"
systemctl restart php8.3-fpm
systemctl restart nginx
systemctl restart mysql

# Enable services on boot
systemctl enable php8.3-fpm
systemctl enable nginx
systemctl enable mysql

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Server setup completed!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Run the MySQL setup script: sudo bash scripts/mysql-setup.sh"
echo "2. Copy Nginx config: sudo cp scripts/nginx.conf /etc/nginx/sites-available/coupon-system"
echo "3. Enable Nginx site: sudo ln -s /etc/nginx/sites-available/coupon-system /etc/nginx/sites-enabled/"
echo "4. Remove default site: sudo rm /etc/nginx/sites-enabled/default"
echo "5. Test Nginx: sudo nginx -t && sudo systemctl reload nginx"
echo "6. Configure your .env file in /var/www/coupon-system/"
echo ""
echo -e "${YELLOW}Chromium path for .env:${NC}"
echo "CHROME_PATH=/usr/bin/chromium-browser"
echo ""
echo -e "${YELLOW}PHP version:${NC}"
php -v
echo ""
echo -e "${YELLOW}Node version:${NC}"
node -v
echo ""
echo -e "${YELLOW}Composer version:${NC}"
composer --version
