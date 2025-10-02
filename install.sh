#!/bin/bash

###############################################################################
# SEL Diagnostic Center - Fully Automated Linux Installation Script
# Works on fresh or existing Ubuntu/Debian installations
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

# Error handler
error_exit() {
    print_error "$1"
    echo
    print_warning "Installation failed. You can try running the script again."
    print_warning "Or check the error message above for details."
    exit 1
}

# Banner
clear
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║   SEL Diagnostic Center - Automated Installer            ║
║   Ubuntu/Debian Linux - One Command Setup                ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please do NOT run this script as root or with sudo"
    print_warning "The script will ask for sudo password when needed"
    exit 1
fi

# Check if running on supported OS
if ! command -v apt &> /dev/null; then
    print_error "This script only supports Ubuntu/Debian-based distributions"
    exit 1
fi

# Detect Ubuntu version
if [ -f /etc/os-release ]; then
    . /etc/os-release
    print_success "Detected: $PRETTY_NAME"
else
    print_warning "Could not detect OS version, continuing anyway..."
fi
echo

# Ask for confirmation
echo -e "${YELLOW}This script will install:${NC}"
echo "  • PHP 8.2 with extensions"
echo "  • MariaDB database server"
echo "  • Node.js 18.x LTS"
echo "  • Git"
echo "  • Composer (PHP package manager)"
echo "  • npm dependencies"
echo "  • Configure database automatically"
echo
read -p "Continue with installation? (y/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "Installation cancelled by user"
    exit 0
fi
echo

print_step "Starting automated installation..."
echo

# Update system
print_step "Step 1/11: Updating system packages..."
sudo apt update -qq || error_exit "Failed to update package list"
sudo apt upgrade -y -qq || print_warning "Some packages could not be upgraded, continuing..."
print_success "System updated"
echo

# Install prerequisites
print_step "Step 2/11: Installing prerequisites..."
sudo apt install -y software-properties-common curl wget gnupg2 ca-certificates lsb-release apt-transport-https || error_exit "Failed to install prerequisites"
print_success "Prerequisites installed"
echo

# Install PHP 8.2
print_step "Step 3/11: Installing PHP 8.2 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y || error_exit "Failed to add PHP repository"
sudo apt update -qq

sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl \
    || error_exit "Failed to install PHP"

PHP_VERSION=$(php -v | head -n 1)
print_success "PHP installed: $PHP_VERSION"
echo

# Install MariaDB
print_step "Step 4/11: Installing MariaDB..."
export DEBIAN_FRONTEND=noninteractive
sudo debconf-set-selections <<< 'mariadb-server mysql-server/root_password password'
sudo debconf-set-selections <<< 'mariadb-server mysql-server/root_password_again password'
sudo apt install -y mariadb-server mariadb-client || error_exit "Failed to install MariaDB"
sudo systemctl start mariadb || error_exit "Failed to start MariaDB"
sudo systemctl enable mariadb
print_success "MariaDB installed and running"
echo

# Install Node.js
print_step "Step 5/11: Installing Node.js 18.x..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash - || error_exit "Failed to add Node.js repository"
sudo apt install -y nodejs || error_exit "Failed to install Node.js"
NODE_VERSION=$(node -v)
NPM_VERSION=$(npm -v)
print_success "Node.js installed: $NODE_VERSION"
print_success "npm installed: $NPM_VERSION"
echo

# Install Git
print_step "Step 6/11: Installing Git..."
sudo apt install -y git || error_exit "Failed to install Git"
GIT_VERSION=$(git --version)
print_success "$GIT_VERSION"
echo

# Install Composer
print_step "Step 7/11: Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php || error_exit "Failed to download Composer"
    sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet || error_exit "Failed to install Composer"
    rm /tmp/composer-setup.php
    print_success "Composer installed"
else
    print_success "Composer already installed"
fi
COMPOSER_VERSION=$(composer --version --no-ansi 2>/dev/null | head -n 1)
print_success "$COMPOSER_VERSION"
echo

# Install npm dependencies
print_step "Step 8/11: Installing npm dependencies..."
npm install || error_exit "Failed to install npm dependencies"
print_success "npm dependencies installed"
echo

# Configure environment
print_step "Step 9/11: Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env || error_exit "Failed to create .env file"

    # Auto-configure database settings
    sed -i 's/^DB_HOST=.*/DB_HOST=localhost/' .env
    sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
    sed -i 's/^DB_NAME=.*/DB_NAME=diagnostic_center/' .env
    sed -i 's/^DB_USER=.*/DB_USER=diagnostic_user/' .env
    sed -i 's/^DB_PASS=.*/DB_PASS=DiagnosticPass2024!/' .env

    print_success "Environment file created and configured (.env)"
else
    print_warning "Environment file already exists, skipping..."
fi
echo

# Create storage directories
print_step "Step 10/11: Setting up directories and permissions..."
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

# Automated Database Setup
print_step "Step 11/11: Setting up database automatically..."

# Create database and user
print_step "Creating database and user..."
sudo mysql -e "DROP DATABASE IF EXISTS diagnostic_center;" 2>/dev/null || true
sudo mysql -e "DROP USER IF EXISTS 'diagnostic_user'@'localhost';" 2>/dev/null || true
sudo mysql -e "CREATE DATABASE diagnostic_center CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || error_exit "Failed to create database"
sudo mysql -e "CREATE USER 'diagnostic_user'@'localhost' IDENTIFIED BY 'DiagnosticPass2024!';" || error_exit "Failed to create database user"
sudo mysql -e "GRANT ALL PRIVILEGES ON diagnostic_center.* TO 'diagnostic_user'@'localhost';" || error_exit "Failed to grant privileges"
sudo mysql -e "FLUSH PRIVILEGES;" || error_exit "Failed to flush privileges"
print_success "Database and user created"

# Import database
print_step "Importing database schema and data..."
if [ -f diagnostic_center.sql ]; then
    mysql -u diagnostic_user -pDiagnosticPass2024! diagnostic_center < diagnostic_center.sql || error_exit "Failed to import database"
    print_success "Database imported successfully"
elif [ -f database/1_schema.sql ] && [ -f database/2_initial_data.sql ]; then
    mysql -u diagnostic_user -pDiagnosticPass2024! diagnostic_center < database/1_schema.sql || error_exit "Failed to import schema"
    mysql -u diagnostic_user -pDiagnosticPass2024! diagnostic_center < database/2_initial_data.sql || error_exit "Failed to import initial data"
    if [ -f database/3_demo_data.sql ]; then
        mysql -u diagnostic_user -pDiagnosticPass2024! diagnostic_center < database/3_demo_data.sql || print_warning "Demo data import failed, continuing..."
    fi
    print_success "Database imported successfully"
else
    error_exit "Database SQL files not found"
fi

# Verify database
TABLE_COUNT=$(mysql -u diagnostic_user -pDiagnosticPass2024! diagnostic_center -e "SHOW TABLES;" | wc -l)
if [ "$TABLE_COUNT" -gt 1 ]; then
    print_success "Database verified: $((TABLE_COUNT - 1)) tables created"
else
    error_exit "Database verification failed"
fi
echo

# Installation complete
echo
echo -e "${GREEN}"
cat << "EOF"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║          🎉 Installation Complete! 🎉                     ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

print_success "All components installed and configured successfully!"
echo
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✓ PHP 8.2${NC} with all extensions"
echo -e "${GREEN}✓ MariaDB${NC} configured and running"
echo -e "${GREEN}✓ Node.js${NC} $NODE_VERSION"
echo -e "${GREEN}✓ Database${NC} imported and ready"
echo -e "${GREEN}✓ Permissions${NC} configured correctly"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo

echo -e "${YELLOW}📝 Database Credentials:${NC}"
echo "   Database: diagnostic_center"
echo "   Username: diagnostic_user"
echo "   Password: DiagnosticPass2024!"
echo "   (stored in .env file)"
echo

echo -e "${YELLOW}🚀 To start the application:${NC}"
echo
echo -e "${GREEN}   npm run dev${NC}"
echo
echo "   Then open your browser to: ${BLUE}http://localhost:8000${NC}"
echo

echo -e "${YELLOW}🔐 Login Credentials:${NC}"
echo "   Username: ${GREEN}admin${NC}"
echo "   Password: ${GREEN}password${NC}"
echo

print_warning "⚠️  IMPORTANT: Change the admin password after first login!"
echo

echo -e "${BLUE}💡 Useful Commands:${NC}"
echo "   Start app:      npm run dev"
echo "   Stop app:       Ctrl+C"
echo "   Check MySQL:    sudo systemctl status mariadb"
echo "   View logs:      tail -f storage/logs/app.log"
echo

echo -e "${GREEN}Installation completed successfully! Happy coding! 🚀${NC}"
echo
