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

- **Backend**: PHP 8.2 with MVC architecture
- **Database**: MariaDB 10.11
- **Frontend**: Bootstrap 5.3, FontAwesome, JavaScript
- **Deployment**: Docker + Docker Compose

---

## 🐳 Docker Setup (Step-by-Step)

### Prerequisites

**You only need Docker installed. Nothing else!**

- **Windows/Mac**: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
- **Linux**: See Step 1 below

---

### Step 1: Install Docker

#### **Windows/Mac:**

1. Download [Docker Desktop](https://www.docker.com/products/docker-desktop)
2. Run the installer
3. Launch Docker Desktop
4. Wait until you see the whale icon in your system tray
5. Docker is ready when the icon stops animating

#### **Linux:**

```bash
# Download and run the Docker installation script
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose plugin
sudo apt-get update
sudo apt-get install docker-compose-plugin

# Add your user to the docker group (avoid using sudo)
sudo usermod -aG docker $USER

# Activate the group changes
newgrp docker

# Verify installation
docker --version
docker compose version
```

---

### Step 2: Get the Code

```bash
# Clone the repository
git clone https://github.com/your-repo/SEL-Diagnostic-center.git

# Navigate into the project folder
cd SEL-Diagnostic-center

# Switch to docker-setup branch
git checkout docker-setup
```

---

### Step 3: Start the Application

```bash
# Start all services (app, database, phpMyAdmin)
docker-compose up -d
```

**What happens:**
- 🔄 Downloads Docker images (PHP, MariaDB, phpMyAdmin) - **only on first run**
- 🏗️ Builds the application container
- 🗄️ Creates the database
- 📥 Automatically imports all SQL data (schema + initial data + demo data)
- 🚀 Starts all services in the background

**First run**: 2-3 minutes (downloading images)
**Subsequent runs**: <10 seconds

---

### Step 4: Wait for Database Initialization

```bash
# Watch the logs to see when database is ready
docker-compose logs -f db
```

Look for this message:
```
sel_diagnostic_db | ready for connections
```

Then press `Ctrl+C` to exit logs.

**Or just wait 30 seconds** - that's usually enough!

---

### Step 5: Access the Application

Open your browser and go to:

#### **Main Application**
- URL: **http://localhost:8080**
- Username: **admin**
- Password: **password**

#### **phpMyAdmin (Database Management)**
- URL: **http://localhost:8081**
- Server: **db**
- Username: **root**
- Password: **root**

---

### Step 6: Verify Everything Works

1. Login with `admin` / `password`
2. You should see the dashboard
3. Check the navigation menu - all features should be accessible
4. Database should have sample data (invoices, patients, tests)

✅ **You're all set!**

---

## 📋 Daily Usage

### Start the Application
```bash
docker-compose up -d
```

### Stop the Application
```bash
docker-compose down
```

### Restart Services
```bash
docker-compose restart
```

### View Logs (if something goes wrong)
```bash
# All services
docker-compose logs -f

# Just the app
docker-compose logs -f app

# Just the database
docker-compose logs -f db
```

### Check Running Containers
```bash
docker ps
```

You should see 3 containers:
- `sel_diagnostic_app` (PHP/Apache)
- `sel_diagnostic_db` (MariaDB)
- `sel_phpmyadmin` (Database UI)

---

## 🔧 Troubleshooting

### ❌ Port 8080 Already in Use

**Error:** `Bind for 0.0.0.0:8080 failed: port is already allocated`

**Solution:**

1. Open `docker-compose.yml`
2. Find this line:
   ```yaml
   ports:
     - "8080:80"
   ```
3. Change `8080` to any free port (e.g., `9000`):
   ```yaml
   ports:
     - "9000:80"
   ```
4. Save and restart:
   ```bash
   docker-compose down
   docker-compose up -d
   ```
5. Access the app at `http://localhost:9000`

---

### ❌ Database Connection Failed

**Error:** Can't connect to database

**Solution:**

```bash
# Check database logs
docker-compose logs db

# Restart database service
docker-compose restart db

# If still not working, reset everything
docker-compose down -v
docker-compose up -d
```

---

### ❌ Login Not Working (admin/password fails)

**Cause:** Database not initialized properly

**Solution:**

```bash
# Stop and remove everything (including volumes)
docker-compose down -v

# Start fresh - database will re-import
docker-compose up -d

# Wait 30 seconds for initialization
```

---

### ❌ Changes to Code Not Showing

**Cause:** Browser cache or container needs restart

**Solution:**

```bash
# Restart the app container
docker-compose restart app

# Or hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)
```

---

### ❌ Docker Not Running

**Error:** `Cannot connect to the Docker daemon`

**Solution:**

- **Windows/Mac**: Launch Docker Desktop and wait for it to start
- **Linux**:
  ```bash
  sudo systemctl start docker
  ```

---

### 🔄 Start Fresh (Nuclear Option)

If everything is broken and you want to start over:

```bash
# Stop and remove all containers, networks, and volumes
docker-compose down -v

# Remove any leftover images
docker system prune -a

# Start fresh
docker-compose up -d
```

---

## 🎯 What's Included

### Services Running

1. **Application Container** (`sel_diagnostic_app`)
   - PHP 8.2 + Apache
   - All PHP extensions installed
   - Port: 8080

2. **Database Container** (`sel_diagnostic_db`)
   - MariaDB 10.11
   - Auto-imports `diagnostic_center.sql` on first run
   - Port: 3307 (if you need external access)

3. **phpMyAdmin Container** (`sel_phpmyadmin`)
   - Web-based database management
   - Port: 8081

### Data Persistence

Your data is stored in Docker volumes and **survives container restarts**:
- Database data: `db_data` volume
- Application files: Mounted from your project folder

**Even if you run `docker-compose down`, your data is safe!**

**To delete data**: `docker-compose down -v` (the `-v` flag removes volumes)

---

## 🗂️ Project Structure

```
SEL-Diagnostic-center/
├── Dockerfile                   # PHP/Apache container config
├── docker-compose.yml          # Services orchestration
├── .dockerignore               # Files to exclude from build
├── diagnostic_center.sql       # Combined database dump (auto-imported)
├── database/
│   ├── 1_schema.sql           # Database structure
│   ├── 2_initial_data.sql     # Essential data
│   └── 3_demo_data.sql        # Sample data
├── public/
│   └── index.php              # Application entry point
├── src/
│   └── Controllers/           # Application logic
├── views/                     # HTML templates
├── .env.example               # Environment variables template
└── README.md                  # This file
```

---

## 📝 Default Credentials

**Application Login:**
- Username: `admin`
- Password: `password`

**Database (phpMyAdmin):**
- Server: `db`
- Username: `root`
- Password: `root`
- Database: `diagnostic_center`

⚠️ **Change the admin password after first login!**

---

## 💻 Advanced Docker Commands

### Access Container Shell

```bash
# PHP container
docker exec -it sel_diagnostic_app bash

# Once inside, you can run PHP commands:
php -v
ls -la /var/www/html
```

### Access Database Directly

```bash
# MySQL command line
docker exec -it sel_diagnostic_db mysql -u root -proot diagnostic_center

# Once inside, run SQL:
SHOW TABLES;
SELECT * FROM users;
```

### View Container Resource Usage

```bash
docker stats
```

### Export Database Backup

```bash
docker exec sel_diagnostic_db mysqldump -u root -proot diagnostic_center > backup.sql
```

### Import Database Backup

```bash
cat backup.sql | docker exec -i sel_diagnostic_db mysql -u root -proot diagnostic_center
```

---

## 🤝 Contributing

1. Make changes to the code (they'll reflect immediately - volume is mounted)
2. Test your changes at `http://localhost:8080`
3. Commit and push as usual
4. No need to rebuild Docker unless you change `Dockerfile` or `docker-compose.yml`

---

## 📄 License

Proprietary software for SEL Diagnostic Center

---

## 🆘 Need Help?

1. **Check logs**: `docker-compose logs -f`
2. **Restart services**: `docker-compose restart`
3. **Start fresh**: `docker-compose down -v && docker-compose up -d`
4. **Contact the development team**

---

**SEL Diagnostic Center** - Modern Laboratory Management System 🏥
