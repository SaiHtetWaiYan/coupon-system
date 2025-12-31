#!/bin/bash

# =============================================================================
# MySQL Setup Script for Laravel Coupon System
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Database credentials - CHANGE THESE!
DB_NAME="coupon_system"
DB_USER="coupon_user"
DB_PASS="your_secure_password_here"  # Change this!

echo -e "${YELLOW}Setting up MySQL database...${NC}"

# Create database and user
mysql -u root << EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}MySQL setup completed!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "${YELLOW}Database credentials for your .env file:${NC}"
echo ""
echo "DB_CONNECTION=mysql"
echo "DB_HOST=127.0.0.1"
echo "DB_PORT=3306"
echo "DB_DATABASE=${DB_NAME}"
echo "DB_USERNAME=${DB_USER}"
echo "DB_PASSWORD=${DB_PASS}"
echo ""
echo -e "${RED}IMPORTANT: Change the password in this script before running!${NC}"
