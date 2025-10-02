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

- **Backend**: PHP 7.4+ with MVC architecture
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3, FontAwesome, JavaScript
- **Build Tool**: Vite 5 (hot reload, fast builds)

## ⚡ Quick Start (TL;DR)

```bash
# Clone and install
git clone <repo-url>
cd SEL-Diagnostic-center
npm install

# Setup database
cp .env.example .env
# Edit .env with your DB credentials
# Create database and import SQL files (see detailed steps below)

# Run the app
npm run dev

# Open http://localhost:8000
# Login: admin / password
```

## 📦 Installation

### Prerequisites
- PHP 7.4+ (with MySQL extension)
- MySQL 5.7+
- Node.js 18+ and npm
- XAMPP (recommended) or any web server

---

## Method 1: Using XAMPP (Recommended for Windows)

### Step 1: Clone the Repository
```bash
git clone https://github.com/your-repo/SEL-Diagnostic-center.git
cd SEL-Diagnostic-center
```

### Step 2: Install Dependencies
```bash
npm install
```

### Step 3: Setup Environment
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

Edit `.env` file and set database credentials:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=pathology_lab
DB_USER=root
DB_PASS=
```
> **Note**: XAMPP's default MySQL has no password (leave `DB_PASS` empty)

### Step 4: Start XAMPP
1. Open **XAMPP Control Panel**
2. Start **Apache** and **MySQL** modules
3. Wait until both show green "Running" status

### Step 5: Create Database
1. Open browser → Go to `http://localhost/phpmyadmin`
2. Click **"New"** in left sidebar
3. Database name: `pathology_lab`
4. Collation: Select `utf8mb4_unicode_ci`
5. Click **"Create"**

### Step 6: Import Database Files
1. Click on **`pathology_lab`** database (left sidebar)
2. Click **"Import"** tab
3. Import these files **in exact order** (one at a time):

   **File 1:** `1_schema.sql`
   - Click "Choose File" → Select `database/1_schema.sql` → Click "Import"
   - ✅ Wait for success message

   **File 2:** `2_initial_data.sql`
   - Click "Choose File" → Select `database/2_initial_data.sql` → Click "Import"
   - ✅ Wait for success message

   **File 3 (Optional):** `3_demo_data.sql`
   - Only import if you want sample data (invoices, patients, reports)
   - Click "Choose File" → Select `database/3_demo_data.sql` → Click "Import"
   - ✅ Wait for success message

### Step 7: Move Project to XAMPP
Move your project folder to XAMPP's htdocs directory:

**Windows:**
```bash
move SEL-Diagnostic-center C:\xampp\htdocs\
# Or copy manually to: C:\xampp\htdocs\
```

**Linux:**
```bash
sudo mv SEL-Diagnostic-center /opt/lampp/htdocs/
# Or copy manually to: /opt/lampp/htdocs/
```

**Mac:**
```bash
sudo mv SEL-Diagnostic-center /Applications/XAMPP/htdocs/
# Or copy manually to: /Applications/XAMPP/htdocs/
```

### Step 8: Run the Application

Start the development server:
```bash
npm run dev
```

This command will:
- Start Vite dev server (port 5173) for hot reload
- Start PHP server (port 8000)
- Both run concurrently

**Open browser:**
- Go to: `http://localhost:8000`

**Login:**
- **Username**: `admin`
- **Password**: `password`

✅ **Done! You should now see the dashboard with hot reload support!**

---

## Method 2: Using Terminal (Linux/Mac/Windows)

### Step 1: Clone the Repository
```bash
git clone https://github.com/your-repo/SEL-Diagnostic-center.git
cd SEL-Diagnostic-center
```

### Step 2: Install Dependencies
```bash
npm install
```

### Step 3: Setup Environment
```bash
cp .env.example .env
```

Edit `.env` and configure database:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=pathology_lab
DB_USER=root
DB_PASS=your_password
```

### Step 4: Create Database and Import Files

**For Windows (XAMPP):**
```bash
# Create database
C:\xampp\mysql\bin\mysql -u root -p -e "CREATE DATABASE pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import files in order
C:\xampp\mysql\bin\mysql -u root -p pathology_lab < database\1_schema.sql
C:\xampp\mysql\bin\mysql -u root -p pathology_lab < database\2_initial_data.sql
C:\xampp\mysql\bin\mysql -u root -p pathology_lab < database\3_demo_data.sql
```
> Press Enter when asked for password (XAMPP default has no password)

**For Linux (XAMPP/LAMPP):**
```bash
# Create database
/opt/lampp/bin/mysql -u root -p -e "CREATE DATABASE pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import files in order
/opt/lampp/bin/mysql -u root -p pathology_lab < database/1_schema.sql
/opt/lampp/bin/mysql -u root -p pathology_lab < database/2_initial_data.sql
/opt/lampp/bin/mysql -u root -p pathology_lab < database/3_demo_data.sql
```
> XAMPP on Linux is installed in `/opt/lampp/`

**For Mac (XAMPP):**
```bash
# Create database
/Applications/XAMPP/bin/mysql -u root -p -e "CREATE DATABASE pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import files in order
/Applications/XAMPP/bin/mysql -u root -p pathology_lab < database/1_schema.sql
/Applications/XAMPP/bin/mysql -u root -p pathology_lab < database/2_initial_data.sql
/Applications/XAMPP/bin/mysql -u root -p pathology_lab < database/3_demo_data.sql
```

**For Linux/Mac (Native MySQL - not XAMPP):**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import files in order
mysql -u root -p pathology_lab < database/1_schema.sql
mysql -u root -p pathology_lab < database/2_initial_data.sql
mysql -u root -p pathology_lab < database/3_demo_data.sql
```

### Step 5: Run the Application

**Option A: Vite Dev Server (Recommended - with hot reload)**
```bash
npm run dev
```
This starts both Vite (port 5173) and PHP (port 8000) servers.

Then open: `http://localhost:8000`

**Option B: PHP Server Only (without Vite)**
```bash
php -S localhost:8000 -t public
```
Then open: `http://localhost:8000`

**Option C: XAMPP/Apache**

Move project to XAMPP directory:
- **Windows**: `C:\xampp\htdocs\`
- **Linux**: `/opt/lampp/htdocs/`
- **Mac**: `/Applications/XAMPP/htdocs/`

Then run: `npm run dev` or just access `http://localhost/SEL-Diagnostic-center/public`

### Step 6: Login
- **Username**: `admin`
- **Password**: `password`

✅ **Done! You should now see the dashboard.**

> 💡 **Tip**: Use `npm run dev` for the best development experience with hot reload!

---

## 📊 Database Files Explained

| File | Description | Required? |
|------|-------------|-----------|
| `1_schema.sql` | Creates all database tables | ✅ Yes |
| `2_initial_data.sql` | Admin user, tests catalog, system config | ✅ Yes |
| `3_demo_data.sql` | Sample invoices, patients, reports | ⚠️ Optional |

---

## 🔧 Troubleshooting

### ❌ "System temporarily unavailable" Error

**Cause**: Database not configured properly

**Solution**:
1. ✅ Check `.env` file has correct database credentials
2. ✅ Verify MySQL is running (XAMPP Control Panel)
3. ✅ Confirm database `pathology_lab` exists in phpMyAdmin
4. ✅ Make sure all 3 SQL files imported successfully

**Test Database Connection:**
```bash
# Windows
C:\xampp\mysql\bin\mysql -u root -p pathology_lab -e "SHOW TABLES;"

# Linux/Mac
mysql -u root -p pathology_lab -e "SHOW TABLES;"
```
You should see 15+ tables listed.

### ❌ Import Failed / Foreign Key Error

**Solution**: Drop database and start over
```sql
DROP DATABASE pathology_lab;
CREATE DATABASE pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Then re-import files in exact order: 1 → 2 → 3

### ❌ Page Not Found / 404 Error

**Solution**: Make sure you're accessing the `/public` directory:
- ✅ `http://localhost/SEL-Diagnostic-center/public`
- ❌ `http://localhost/SEL-Diagnostic-center` (won't work)

### ❌ Can't Login with admin/password

**Cause**: Database not imported or initial_data.sql skipped

**Solution**: Re-import `database/2_initial_data.sql`

### ❌ Using XAMPP with MariaDB instead of MySQL

**If your XAMPP came with MariaDB**, don't worry - it's fully compatible! MariaDB is a drop-in replacement for MySQL.

**Check which one you have:**
```bash
# Windows
C:\xampp\mysql\bin\mysql --version

# Linux
/opt/lampp/bin/mysql --version

# You'll see either "MySQL" or "MariaDB" in the output
```

**All commands work the same:**
- phpMyAdmin works identically
- SQL import commands are the same
- `.env` configuration is identical (use same DB settings)
- No code changes needed

**Note**: MariaDB is actually MySQL-compatible and often faster. Your app will work perfectly! ✅

---

## 📝 Default Login Credentials

After installation, use these credentials:

- **Username**: `admin`
- **Password**: `password`

⚠️ **Change the password immediately after first login!**

---

## 🎯 Project Structure

```
SEL-Diagnostic-center/
├── database/
│   ├── 1_schema.sql          # Database structure
│   ├── 2_initial_data.sql    # Essential data
│   └── 3_demo_data.sql       # Sample data (optional)
├── public/
│   └── index.php             # Entry point
├── src/
│   └── Controllers/          # Application logic
├── views/                    # HTML templates
├── .env.example              # Environment template
├── .gitignore
├── bootstrap.php             # App initialization
└── README.md
```

---

## 🤝 Contributing

1. Follow PSR-4 standards
2. Add CSRF protection to all forms
3. Validate all inputs
4. Update documentation

---

## 📄 License

Proprietary software for SEL Diagnostic Center

---

## 🆘 Need Help?

1. Check logs: `storage/logs/app.log`
2. Verify database connection
3. Review this README
4. Contact development team

---

**SEL Diagnostic Center** - Modern Laboratory Management System
