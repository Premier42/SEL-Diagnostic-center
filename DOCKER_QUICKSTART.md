# 🐳 Docker Quick Start - For Teammates

## Why Docker?
- ❌ No more XAMPP installation issues
- ❌ No more PHP version conflicts
- ❌ No more database setup headaches
- ✅ Works the same on Windows, Mac, and Linux
- ✅ Everything starts with ONE command

## 🚀 Setup (5 Minutes)

### 1. Install Docker

**Windows/Mac:**
- Download [Docker Desktop](https://www.docker.com/products/docker-desktop)
- Install and run it
- Wait for Docker to start (you'll see a whale icon)

**Linux:**
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
newgrp docker
```

### 2. Get the Code
```bash
git clone <repo-url>
cd SEL-Diagnostic-center
git checkout docker-setup
```

### 3. Start Everything
```bash
docker-compose up -d
```

That's it! Wait 30 seconds, then open:
- **App**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

**Login**: admin / password

## 📝 Daily Usage

```bash
# Start the app
docker-compose up -d

# Stop the app
docker-compose down

# View logs if something breaks
docker-compose logs -f

# Restart everything
docker-compose restart
```

## 🔧 Troubleshooting

### Port 8080 already in use?
Edit `docker-compose.yml` and change `"8080:80"` to `"9000:80"` (or any free port)

### Database not working?
```bash
docker-compose down -v
docker-compose up -d
```

### Want to start fresh?
```bash
docker-compose down -v  # Deletes everything
docker-compose up -d    # Fresh start
```

## 💡 Pro Tips

1. **Code changes are instant** - No need to restart Docker!
2. **Database data persists** - Even after stopping containers
3. **Need a shell?** `docker exec -it sel_diagnostic_app bash`
4. **Check what's running:** `docker ps`

## ❓ Common Questions

**Q: Do I need XAMPP?**
A: Nope! Docker replaces it completely.

**Q: Do I need to install PHP or MySQL?**
A: Nope! Everything runs in containers.

**Q: Will this mess up my computer?**
A: Nope! Docker is isolated. Uninstall Docker to remove everything.

**Q: Can I still use XAMPP if I want?**
A: Yes! Switch to `main` branch for traditional setup.

## 🆘 Still Having Issues?

1. Make sure Docker Desktop is running (whale icon visible)
2. Check logs: `docker-compose logs -f`
3. Try: `docker-compose down -v && docker-compose up -d`
4. Ask the team for help!

---

**Welcome to easy setup!** 🎉
