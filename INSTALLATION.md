# Installation Guide - Part 1: Core System & Authentication

## Overview
This package contains the core foundation files for the Pathology Laboratory Management System. As the project lead, you will integrate all team contributions into this base system.

## What's Included

### Core Files
- `index.php` - Main entry point with authentication
- `logout.php` - Enhanced logout with audit logging
- `.env` - Environment configuration template
- `README.md` - Complete project documentation

### API Endpoints
- `api/dashboard-stats.php` - Dashboard statistics
- `api/system-alerts.php` - System health monitoring

### Configuration
- `config/app.php` - Application configuration
- `config/database.php` - Database connection setup

### Authentication & Security
- `middleware/auth.php` - Authentication middleware
- `src/` - Core classes (Models, Services, Controllers)

### UI Components
- `views/dashboard.php` - Main dashboard interface
- `views/login.php` - Modern login interface
- `includes/header.php` - Common header component
- `includes/footer.php` - Common footer component
- `assets/js/dashboard.js` - Dashboard JavaScript

### Database
- `database/core_schema.sql` - Core database schema

## Installation Steps

### 1. Setup Project Directory
```bash
# Extract this package to your web server directory
unzip part1_core_system.zip
cd part1_core_system
```

### 2. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE pathology_lab"

# Import core schema
mysql -u root -p pathology_lab < database/core_schema.sql
```

### 3. Configure Environment
```bash
# Copy and edit environment file
cp .env .env.local
nano .env.local
```

Update database credentials:
```
DB_HOST=localhost
DB_NAME=pathology_lab
DB_USER=your_username
DB_PASS=your_password
```

### 4. Set Permissions
```bash
chmod 755 -R .
mkdir -p storage/{logs,uploads,sessions}
chmod 777 storage/{logs,uploads,sessions}
```

### 5. Test Core System
- Access via web browser: `http://your-domain/`
- Login with: `admin` / `password`
- Verify dashboard loads with statistics

## Team Integration Process

### When Team Members Complete Their Parts:

1. **Receive their ZIP files**
2. **Extract to temporary directories**
3. **Copy their files to main project:**
   ```bash
   # Example for Part 2 (Patient & Invoice Management)
   cp -r part2_temp/views/invoices/ views/
   cp -r part2_temp/api/invoices.php api/
   cp -r part2_temp/assets/js/invoices.js assets/js/
   ```

4. **Import their database schemas:**
   ```bash
   mysql -u root -p pathology_lab < part2_temp/database/invoice_schema.sql
   ```

5. **Test integration**
6. **Update navigation in `views/dashboard.php`**

## Default Credentials
- **Username**: admin
- **Password**: password

## Core Features Ready
- ✅ User authentication system
- ✅ Session management with security
- ✅ Dashboard with real-time statistics
- ✅ System health monitoring
- ✅ Audit logging foundation
- ✅ Responsive UI framework
- ✅ API structure for modules

## Next Steps
1. Test the core system functionality
2. Coordinate with team members on their progress
3. Prepare for module integration
4. Review and merge team contributions
5. Perform final testing and deployment

## Support
- Check `README.md` for complete documentation
- Review API endpoints for integration points
- Test database connections and permissions
- Verify all core features work before integration

## Project Lead Responsibilities
- Maintain code quality standards
- Coordinate team integration
- Resolve merge conflicts
- Ensure consistent UI/UX
- Manage database schema changes
- Lead final testing and deployment
