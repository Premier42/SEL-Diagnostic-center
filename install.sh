#!/bin/bash

###############################################################################
# SEL Diagnostic Center - Linux Installation Script
# Automated setup for Ubuntu/Debian-based Linux distributions
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Print colored messages
print_step() {
    echo -e "${BLUE}==>${NC} ${1}"
}

print_success() {
    echo -e "${GREEN}✓${NC} ${1}"
}

print_error() {
    echo -e "${RED}✗${NC} ${1}"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} ${1}"
}

# Banner
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║   SEL Diagnostic Center - Installation Script            ║
║   For Ubuntu/Debian-based Linux Distributions            ║
╚═══════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please do not run this script as root or with sudo"
    print_warning "The script will ask for sudo password when needed"
    exit 1
fi

# Check if running on supported OS
if ! command -v apt &> /dev/null; then
    print_error "This script only supports Ubuntu/Debian-based distributions"
    exit 1
fi

print_step "Starting installation process..."
echo

# Update system
print_step "Step 1/10: Updating system packages..."
sudo apt update -qq
sudo apt upgrade -y -qq
print_success "System updated"
echo

# Install PHP 8.2
print_step "Step 2/10: Installing PHP 8.2 and extensions..."
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update -qq

sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl

PHP_VERSION=$(php -v | head -n 1)
print_success "PHP installed: $PHP_VERSION"
echo

# Install MariaDB
print_step "Step 3/10: Installing MariaDB..."
sudo apt install -y mariadb-server mariadb-client
sudo systemctl start mariadb
sudo systemctl enable mariadb
print_success "MariaDB installed and running"
echo

# Install Node.js
print_step "Step 4/10: Installing Node.js 18.x..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
NODE_VERSION=$(node -v)
NPM_VERSION=$(npm -v)
print_success "Node.js installed: $NODE_VERSION"
print_success "npm installed: $NPM_VERSION"
echo

# Install Git
print_step "Step 5/10: Installing Git..."
sudo apt install -y git
GIT_VERSION=$(git --version)
print_success "$GIT_VERSION"
echo

# Install Composer
print_step "Step 6/10: Installing Composer..."
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet
rm /tmp/composer-setup.php
COMPOSER_VERSION=$(composer --version --no-ansi | head -n 1)
print_success "Composer installed: $COMPOSER_VERSION"
echo

# Install npm dependencies
print_step "Step 7/10: Installing npm dependencies..."
npm install
print_success "npm dependencies installed"
echo

# Configure environment
print_step "Step 8/10: Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_success "Environment file created (.env)"
else
    print_warning "Environment file already exists, skipping..."
fi
echo

# Create storage directories
print_step "Step 9/10: Setting up directories and permissions..."
mkdir -p storage/logs
mkdir -p public/uploads
chmod -R 775 storage
chmod -R 775 public/uploads

# Set ownership to current user and www-data group
if getent group www-data > /dev/null 2>&1; then
    sudo chown -R $USER:www-data storage
    sudo chown -R $USER:www-data public/uploads
    print_success "Permissions set (user: $USER, group: www-data)"
else
    chmod -R 777 storage
    chmod -R 777 public/uploads
    print_warning "www-data group not found, using 777 permissions"
fi
echo

# Database setup
print_step "Step 10/10: Database setup..."
echo
print_warning "Database setup requires manual configuration"
echo
echo "Please run the following commands to set up the database:"
echo
echo -e "${YELLOW}1. Login to MySQL:${NC}"
echo "   sudo mysql -u root"
echo
echo -e "${YELLOW}2. Create database and user:${NC}"
echo "   CREATE DATABASE diagnostic_center CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "   CREATE USER 'diagnostic_user'@'localhost' IDENTIFIED BY 'your_password';"
echo "   GRANT ALL PRIVILEGES ON diagnostic_center.* TO 'diagnostic_user'@'localhost';"
echo "   FLUSH PRIVILEGES;"
echo "   EXIT;"
echo
echo -e "${YELLOW}3. Import database:${NC}"
echo "   mysql -u diagnostic_user -p diagnostic_center < diagnostic_center.sql"
echo
echo -e "${YELLOW}4. Update .env file with database credentials:${NC}"
echo "   nano .env"
echo
echo "   Set these values:"
echo "   DB_HOST=localhost"
echo "   DB_PORT=3306"
echo "   DB_NAME=diagnostic_center"
echo "   DB_USER=diagnostic_user"
echo "   DB_PASS=your_password"
echo

# Installation complete
echo
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║   Installation Complete! 🎉                               ║
╚═══════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

print_success "All dependencies installed successfully!"
echo
echo -e "${BLUE}Next steps:${NC}"
echo "  1. Complete database setup (see instructions above)"
echo "  2. Configure .env file with your database credentials"
echo "  3. Start the application with: npm run dev"
echo "  4. Access the app at: http://localhost:8000"
echo "  5. Login with username: admin, password: password"
echo
print_warning "Remember to change the default admin password after first login!"
echo
echo -e "${GREEN}Happy coding! 🚀${NC}"
