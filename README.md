# SEL Diagnostic Center - Laboratory Management System

A modern laboratory management system built with PHP and MySQL for diagnostic centers and pathology laboratories.

## 🚀 Features

- **Invoice & Patient Management**: Create invoices, track payments, manage patient records
- **Test Management**: 35+ laboratory tests with parameters and normal ranges
- **Report Generation**: Lab tech data entry, professional PDF reports
- **User Management**: Role-based access (Admin, Staff, Technician)
- **SMS Notifications**: Bangladesh SMS integration (SMS.NET.BD)
- **Inventory Management**: Track reagents, consumables, and lab supplies
- **Audit Logging**: Complete activity tracking
- **Doctor Management**: Maintain referring physician database
- **Bengali Support**: Local language and currency (৳)

## 🛠 Technology Stack

- **Backend**: PHP 8.1+ with MVC architecture
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Frontend**: Bootstrap 5.3, FontAwesome, JavaScript
- **Build Tool**: Vite 5 (hot reload, fast builds)

---

## ⚡ Quick Start - TL;DR

### 🎯 One-Command Setup (Easiest!)

**Copy and paste this into your terminal:**

```bash
git clone https://github.com/your-repo/SEL-Diagnostic-center.git && cd SEL-Diagnostic-center && git checkout native-linux-setup && chmod +x install.sh && ./install.sh
```

**Then start the app:**
```bash
npm run dev
```

**Open:** http://localhost:8000
**Login:** admin / password

✅ **Done!** Everything installs automatically!

---

### 📦 Already Have the Repo?

```bash
cd SEL-Diagnostic-center
git checkout native-linux-setup
./install.sh
npm run dev
```

---

## 🐧 Full Linux Native Setup Guide

This guide provides detailed information about the automated installation process.

### What Gets Installed Automatically

The `install.sh` script installs everything you need:

- ✅ **PHP 8.2** with all required extensions
- ✅ **MariaDB 10.x** database server
- ✅ **Node.js 18.x** LTS with npm
- ✅ **Git** version control
- ✅ **Composer** PHP package manager
- ✅ **Database** automatically configured and imported
- ✅ **Permissions** set correctly

### Supported Linux Distributions

- Ubuntu 20.04+
- Ubuntu 22.04+ ⭐ Recommended
- Debian 11+
- Linux Mint 20+
- Pop!_OS 20.04+
- Other Debian-based distros

Works on **fresh or existing** installations!

---

## 📖 Detailed Installation Steps

### Method 1: Automated Installer (Recommended)

#### Step 1: Clone Repository

```bash
git clone https://github.com/your-repo/SEL-Diagnostic-center.git
cd SEL-Diagnostic-center
git checkout native-linux-setup
```

#### Step 2: Run Installer

```bash
./install.sh
```

The installer will:
1. Ask for confirmation
2. Request your sudo password
3. Install all dependencies (PHP, MariaDB, Node.js, etc.)
4. Configure the database automatically
5. Import all data
6. Set up permissions

**Time:** 10-15 minutes (depending on internet speed)

#### Step 3: Start the Application

```bash
npm run dev
```

#### Step 4: Access the App

Open http://localhost:8000

**Login:**
- Username: `admin`
- Password: `password`

✅ **You're done!**

---

### Method 2: Manual Installation (Advanced)

If you prefer to install components manually:

#### Step 1: Update System

```bash
sudo apt update
sudo apt upgrade -y
```

#### Step 2: Install PHP 8.2

```bash
# Add PHP repository (if needed for latest version)
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and required extensions
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl

# Verify PHP installation
php -v
```

You should see something like:
```
PHP 8.2.x (cli) ...
```

---

### Step 3: Install MySQL/MariaDB

**Choose one:**

#### Option A: MySQL 8.0

```bash
# Install MySQL server
sudo apt install -y mysql-server

# Start MySQL service
sudo systemctl start mysql
sudo systemctl enable mysql

# Secure MySQL installation (set root password)
sudo mysql_secure_installation
```

Follow the prompts:
- Set root password: **YES** (choose a strong password)
- Remove anonymous users: **YES**
- Disallow root login remotely: **YES**
- Remove test database: **YES**
- Reload privilege tables: **YES**

#### Option B: MariaDB 10.6+ (Recommended)

```bash
# Install MariaDB server
sudo apt install -y mariadb-server mariadb-client

# Start MariaDB service
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Secure MariaDB installation
sudo mysql_secure_installation
```

Same prompts as MySQL above.

---

### Step 4: Install Node.js and npm

```bash
# Install Node.js 18.x LTS
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verify installation
node -v
npm -v
```

You should see:
```
v18.x.x
9.x.x
```

---

### Step 5: Install Git

```bash
# Install Git
sudo apt install -y git

# Verify installation
git --version
```

---

### Step 6: Install Composer (PHP Package Manager)

```bash
# Download Composer installer
curl -sS https://getcomposer.org/installer -o composer-setup.php

# Install Composer globally
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Remove installer
rm composer-setup.php

# Verify installation
composer --version
```

---

### Step 7: Clone the Repository

```bash
# Navigate to your preferred directory
cd ~

# Clone the repository
git clone https://github.com/your-repo/SEL-Diagnostic-center.git

# Enter the project directory
cd SEL-Diagnostic-center

# Switch to native Linux setup branch
git checkout native-linux-setup
```

---

### Step 8: Install Node Dependencies

```bash
# Install npm packages
npm install
```

This installs Vite and other frontend dependencies.

---

### Step 9: Configure Environment

```bash
# Copy environment example file
cp .env.example .env

# Edit the .env file with your database credentials
nano .env
```

Update these values:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=diagnostic_center
DB_USER=root
DB_PASS=your_mysql_password_here
```

**Save and exit:** Press `Ctrl+X`, then `Y`, then `Enter`

---

### Step 10: Create Database

```bash
# Login to MySQL/MariaDB
sudo mysql -u root -p
```

Enter your root password, then run these SQL commands:

```sql
-- Create database
CREATE DATABASE diagnostic_center CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create a dedicated user (recommended for security)
CREATE USER 'diagnostic_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON diagnostic_center.* TO 'diagnostic_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

**If you created a dedicated user**, update your `.env` file:
```env
DB_USER=diagnostic_user
DB_PASS=strong_password_here
```

---

### Step 11: Import Database

```bash
# Import the combined SQL file
mysql -u root -p diagnostic_center < diagnostic_center.sql

# Or if using dedicated user:
mysql -u diagnostic_user -p diagnostic_center < diagnostic_center.sql
```

Enter the password when prompted.

**Verify import:**
```bash
mysql -u root -p -e "USE diagnostic_center; SHOW TABLES;"
```

You should see a list of tables (users, invoices, tests, etc.).

---

### Step 12: Set Permissions

```bash
# Make sure the web server can write to storage directories
mkdir -p storage/logs
chmod -R 775 storage
chmod -R 775 public/uploads

# Set ownership (replace 'yourusername' with your actual username)
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data public/uploads
```

---

### Step 13: Start the Application

```bash
# Start the development server
npm run dev
```

This command starts:
- **Vite dev server** (port 5173) - for hot reload
- **PHP built-in server** (port 8000) - for the application

You should see output like:
```
> SEL-Diagnostic-center@1.0.0 dev
> concurrently "npm run vite" "php -S localhost:8000 -t public"

[0] VITE v5.x.x ready in X ms
[0] ➜ Local: http://localhost:5173/
[1] PHP 8.2.x Development Server started
```

---

### Step 14: Access the Application

Open your browser and go to:

**Main Application:**
- URL: **http://localhost:8000**
- Username: **admin**
- Password: **password**

✅ **You're all set!**

---

## 📋 Daily Usage

### Start the Application

```bash
cd ~/SEL-Diagnostic-center
npm run dev
```

Keep the terminal open while working.

### Stop the Application

Press `Ctrl+C` in the terminal where `npm run dev` is running.

### Check MySQL Service Status

```bash
# Check if MySQL/MariaDB is running
sudo systemctl status mysql
# or
sudo systemctl status mariadb

# Start if stopped
sudo systemctl start mysql

# Restart if needed
sudo systemctl restart mysql
```

---

## 🔧 Troubleshooting

### ❌ "Connection refused" or "Can't connect to database"

**Solution:**

```bash
# Check if MySQL is running
sudo systemctl status mysql

# If not running, start it
sudo systemctl start mysql

# Check database credentials in .env
cat .env | grep DB_

# Test database connection
mysql -u root -p -e "USE diagnostic_center; SELECT COUNT(*) FROM users;"
```

---

### ❌ "Permission denied" errors

**Solution:**

```bash
# Fix storage permissions
chmod -R 775 storage
chmod -R 775 public/uploads

# Fix ownership
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data public/uploads
```

---

### ❌ PHP extensions missing

**Error:** "extension not found" or "Class not found"

**Solution:**

```bash
# Install missing PHP extensions
sudo apt install -y php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip \
    php8.2-gd php8.2-bcmath php8.2-mysql php8.2-intl

# Restart PHP if using PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

### ❌ Port 8000 already in use

**Solution:**

```bash
# Check what's using port 8000
sudo lsof -i :8000

# Kill the process (replace PID with actual process ID)
kill -9 PID

# Or use a different port
php -S localhost:9000 -t public
```

---

### ❌ npm install fails

**Solution:**

```bash
# Clear npm cache
npm cache clean --force

# Remove node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

---

### ❌ "Access denied for user" MySQL error

**Solution:**

```bash
# Reset MySQL root password if forgotten
sudo mysql

# Then run:
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'new_password';
FLUSH PRIVILEGES;
EXIT;

# Update .env with new password
nano .env
```

---

### 🔄 Reset Database (Start Fresh)

```bash
# Backup first (optional)
mysqldump -u root -p diagnostic_center > backup_$(date +%Y%m%d).sql

# Drop and recreate database
mysql -u root -p -e "DROP DATABASE diagnostic_center;"
mysql -u root -p -e "CREATE DATABASE diagnostic_center CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Re-import
mysql -u root -p diagnostic_center < diagnostic_center.sql
```

---

## 🎯 Production Deployment (Optional)

### Install and Configure Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Install PHP-FPM (FastCGI Process Manager)
sudo apt install -y php8.2-fpm

# Create Nginx site configuration
sudo nano /etc/nginx/sites-available/diagnostic-center
```

Add this configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /home/yourusername/SEL-Diagnostic-center/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

```bash
# Enable the site
sudo ln -s /etc/nginx/sites-available/diagnostic-center /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# Set proper permissions
sudo chown -R www-data:www-data /home/yourusername/SEL-Diagnostic-center/storage
sudo chown -R www-data:www-data /home/yourusername/SEL-Diagnostic-center/public/uploads
```

Access via: **http://your-server-ip** or **http://your-domain.com**

---

## 🔒 Security Recommendations

### 1. Secure MySQL

```bash
# Only allow local connections
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Make sure this line exists:
```
bind-address = 127.0.0.1
```

### 2. Enable Firewall

```bash
# Install UFW (if not installed)
sudo apt install -y ufw

# Allow SSH
sudo ufw allow ssh

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable
```

### 3. Change Default Credentials

After first login:
1. Go to User Management
2. Change admin password
3. Create separate user accounts for staff

### 4. Keep System Updated

```bash
# Update system regularly
sudo apt update && sudo apt upgrade -y
```

---

## 🗂️ Project Structure

```
SEL-Diagnostic-center/
├── public/
│   ├── index.php              # Application entry point
│   ├── uploads/               # User uploaded files
│   └── assets/                # CSS, JS, images
├── src/
│   ├── Controllers/           # Application logic
│   ├── Models/                # Database models
│   └── Core/                  # Core classes
├── views/                     # HTML templates
├── storage/
│   └── logs/                  # Application logs
├── database/
│   ├── 1_schema.sql          # Database structure
│   ├── 2_initial_data.sql    # Essential data
│   └── 3_demo_data.sql       # Sample data
├── diagnostic_center.sql      # Combined database file
├── .env.example               # Environment template
├── .env                       # Your configuration (DO NOT COMMIT)
├── package.json               # Node dependencies
├── vite.config.js             # Vite configuration
└── README.md                  # This file
```

---

## 📝 Default Credentials

**Application Login:**
- Username: `admin`
- Password: `password`

**Database:**
- Host: `localhost`
- Port: `3306`
- Database: `diagnostic_center`
- Username: `root` (or your custom user)
- Password: (set during MySQL installation)

⚠️ **Change the admin password immediately after first login!**

---

## 💻 Useful Commands

### View Application Logs

```bash
# Real-time log viewing
tail -f storage/logs/app.log
```

### Database Backup

```bash
# Create backup
mysqldump -u root -p diagnostic_center > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore from backup
mysql -u root -p diagnostic_center < backup_file.sql
```

### Check PHP Configuration

```bash
# View PHP info
php -i | grep -E 'Configuration File|extension_dir|error_log'

# List installed PHP modules
php -m
```

### Monitor System Resources

```bash
# Check disk space
df -h

# Check memory usage
free -h

# Check CPU and memory by process
top
```

---

## 🤝 Contributing

1. Make changes to the code
2. Test locally with `npm run dev`
3. Commit changes: `git add . && git commit -m "Description"`
4. Push to repository: `git push`

---

## 📄 License

Proprietary software for SEL Diagnostic Center

---

## 🆘 Need Help?

1. **Check logs**: `tail -f storage/logs/app.log`
2. **Verify services**: `sudo systemctl status mysql`
3. **Test database**: `mysql -u root -p diagnostic_center`
4. **Contact the development team**

---

## 📚 Additional Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MariaDB Documentation](https://mariadb.org/documentation/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Vite Documentation](https://vitejs.dev/guide/)

---

**SEL Diagnostic Center** - Modern Laboratory Management System 🏥
