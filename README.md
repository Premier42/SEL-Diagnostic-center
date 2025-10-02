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

## 📦 Installation

### Prerequisites
- PHP 7.4+ (with MySQL extension)
- MySQL 5.7+
- XAMPP (recommended) or any web server

---

## Method 1: Using XAMPP (Recommended for Windows)

### Step 1: Clone the Repository
```bash
git clone https://github.com/your-repo/SEL-Diagnostic-center.git
cd SEL-Diagnostic-center
```

### Step 2: Setup Environment
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

### Step 3: Start XAMPP
1. Open **XAMPP Control Panel**
2. Start **Apache** and **MySQL** modules
3. Wait until both show green "Running" status

### Step 4: Create Database
1. Open browser → Go to `http://localhost/phpmyadmin`
2. Click **"New"** in left sidebar
3. Database name: `pathology_lab`
4. Collation: Select `utf8mb4_unicode_ci`
5. Click **"Create"**

### Step 5: Import Database Files
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

### Step 6: Move Project to XAMPP
Move your project folder to XAMPP's htdocs:
```bash
# Windows
move SEL-Diagnostic-center C:\xampp\htdocs\

# Or just copy the folder manually to C:\xampp\htdocs\
```

### Step 7: Run the Application
1. Open browser
2. Go to: `http://localhost/SEL-Diagnostic-center/public`
3. Login with default credentials:
   - **Username**: `admin`
   - **Password**: `password`

✅ **Done! You should now see the dashboard.**

---

## Method 2: Using Terminal (Linux/Mac/Windows)

### Step 1: Clone the Repository
```bash
git clone https://github.com/your-repo/SEL-Diagnostic-center.git
cd SEL-Diagnostic-center
```

### Step 2: Setup Environment
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

### Step 3: Create Database and Import Files

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

**For Linux/Mac:**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import files in order
mysql -u root -p pathology_lab < database/1_schema.sql
mysql -u root -p pathology_lab < database/2_initial_data.sql
mysql -u root -p pathology_lab < database/3_demo_data.sql
```

### Step 4: Run the Application

**Option A: PHP Built-in Server (Quick Testing)**
```bash
php -S localhost:8000 -t public
```
Then open: `http://localhost:8000`

**Option B: XAMPP/Apache**
- Move project to `C:\xampp\htdocs\` (Windows) or `/var/www/html/` (Linux)
- Open: `http://localhost/SEL-Diagnostic-center/public`

### Step 5: Login
- **Username**: `admin`
- **Password**: `password`

✅ **Done! You should now see the dashboard.**

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
