# 🚀 Setup Guide - Choose Your Method

This project now has **two different setup methods** on separate branches. Choose the one that fits your needs!

---

## 🐳 Docker Setup (Easiest - Recommended for Beginners)

**Branch:** `docker-setup`

### ✅ Best For:
- Teammates who want the **easiest setup**
- No experience with Linux/servers
- Want to avoid installing PHP, MySQL, Node.js manually
- Windows/Mac users
- Quick development environment

### 🎯 Features:
- ✅ **One command setup**: `docker-compose up -d`
- ✅ No XAMPP needed
- ✅ No PHP/MySQL installation needed
- ✅ Works on Windows, Mac, and Linux
- ✅ Consistent environment for everyone
- ✅ Includes phpMyAdmin (database UI)
- ✅ Auto-imports database on first run

### 📖 Setup Steps:
1. Install Docker Desktop
2. Clone the repo and checkout `docker-setup` branch
3. Run `docker-compose up -d`
4. Open http://localhost:8080
5. Login: admin/password

**Time to setup:** ~5 minutes (first time)

### 📚 Documentation:
- See README.md on `docker-setup` branch
- See DOCKER_QUICKSTART.md for teammate guide

---

## 🐧 Native Linux Setup (For Production & Learning)

**Branch:** `native-linux-setup`

### ✅ Best For:
- **Production deployments**
- Learning Linux server administration
- Users who want full control
- Cloud servers (AWS, DigitalOcean, etc.)
- Performance-critical environments
- Users who don't want Docker overhead

### 🎯 Features:
- ✅ Direct installation on Linux (Ubuntu/Debian)
- ✅ Full control over all services
- ✅ Production-ready with Nginx
- ✅ Better performance (no container overhead)
- ✅ Automated installer script included
- ✅ Security best practices included

### 📖 Setup Steps:
1. Use Ubuntu/Debian-based Linux
2. Clone the repo and checkout `native-linux-setup` branch
3. Run `./install.sh` (installs everything)
4. Follow database setup instructions
5. Run `npm run dev`
6. Open http://localhost:8000
7. Login: admin/password

**Time to setup:** ~15-20 minutes (first time)

### 📚 Documentation:
- See README.md on `native-linux-setup` branch
- Automated script: `./install.sh`

---

## 🔄 How to Switch Between Branches

### Switch to Docker Setup:
```bash
git checkout docker-setup
# Then follow README.md instructions
```

### Switch to Native Linux Setup:
```bash
git checkout native-linux-setup
# Then follow README.md instructions
```

---

## 📊 Comparison Table

| Feature | Docker Setup | Native Linux Setup |
|---------|-------------|-------------------|
| **Ease of setup** | ⭐⭐⭐⭐⭐ Very Easy | ⭐⭐⭐ Moderate |
| **Setup time** | 5 minutes | 15-20 minutes |
| **Prerequisites** | Docker only | PHP, MySQL, Node.js, etc. |
| **OS Support** | Windows, Mac, Linux | Linux only |
| **Production ready** | ⭐⭐⭐ Good | ⭐⭐⭐⭐⭐ Excellent |
| **Performance** | ⭐⭐⭐⭐ Good | ⭐⭐⭐⭐⭐ Excellent |
| **Learning curve** | Low | Medium |
| **Customization** | ⭐⭐⭐ Limited | ⭐⭐⭐⭐⭐ Full control |
| **For beginners** | ✅ Perfect | ⚠️ Requires Linux knowledge |
| **For production** | ✅ Good | ✅ Better |

---

## 🎯 Recommendations

### For Development Team:
- **If you're struggling with XAMPP:** Use `docker-setup`
- **If you're on Windows/Mac:** Use `docker-setup`
- **If you're new to programming:** Use `docker-setup`
- **If you want to learn quickly:** Use `docker-setup`

### For Production Deployment:
- **If deploying to a server:** Use `native-linux-setup`
- **If you need best performance:** Use `native-linux-setup`
- **If you want full control:** Use `native-linux-setup`

### For Learning:
- **Want to learn Docker:** Use `docker-setup`
- **Want to learn Linux server admin:** Use `native-linux-setup`

---

## 📝 Default Credentials (Same for Both)

**Application Login:**
- Username: `admin`
- Password: `password`

**Access URLs:**

| Setup Type | Application | phpMyAdmin |
|-----------|------------|------------|
| Docker | http://localhost:8080 | http://localhost:8081 |
| Native | http://localhost:8000 | Install separately |

---

## 🆘 Need Help?

### Docker Setup Issues:
1. Make sure Docker Desktop is running
2. Run: `docker-compose logs -f`
3. Try: `docker-compose down -v && docker-compose up -d`

### Native Linux Setup Issues:
1. Check logs: `tail -f storage/logs/app.log`
2. Check MySQL: `sudo systemctl status mysql`
3. Re-run installer: `./install.sh`

---

## 🌟 Quick Start Commands

### Docker Setup:
```bash
git clone <repo-url>
cd SEL-Diagnostic-center
git checkout docker-setup
docker-compose up -d
# Open http://localhost:8080
```

### Native Linux Setup:
```bash
git clone <repo-url>
cd SEL-Diagnostic-center
git checkout native-linux-setup
./install.sh
# Follow database setup instructions
npm run dev
# Open http://localhost:8000
```

---

**Choose the method that works best for you and your team! Both are fully supported.** 🚀
