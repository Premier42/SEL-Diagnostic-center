# 🚀 Quick Start - Ubuntu/Debian Linux

## One-Command Setup (Easiest!)

Copy and paste this ONE command into your terminal:

```bash
git clone https://github.com/your-repo/SEL-Diagnostic-center.git && cd SEL-Diagnostic-center && git checkout native-linux-setup && chmod +x install.sh && ./install.sh
```

**That's it!** The script will:
- ✅ Install all required software (PHP, MariaDB, Node.js, Git, Composer)
- ✅ Configure the database automatically
- ✅ Import all data
- ✅ Set up permissions
- ✅ Everything ready to run!

Then just run:
```bash
npm run dev
```

Open **http://localhost:8000** and login with:
- Username: **admin**
- Password: **password**

---

## Already Have the Repo?

If you already cloned the repository:

```bash
cd SEL-Diagnostic-center
git checkout native-linux-setup
./install.sh
```

Then:
```bash
npm run dev
```

---

## What Gets Installed?

**System Tools:**
- build-essential, curl, wget, git, unzip, python3

**Web Stack:**
- **PHP 8.2** with PDO, MySQL, mbstring, XML, cURL, ZIP, GD, bcmath, intl, soap, opcache
- **MariaDB 10.x** database server
- **Node.js 18.x** LTS with npm
- **Composer** PHP package manager

**Application:**
- All npm dependencies (Vite, frontend tools)
- Database automatically created and imported
- Permissions configured

---

## Fresh Ubuntu?

✅ Works on **brand new Ubuntu** with NOTHING installed!
✅ Works on existing Ubuntu installations too!

The installer will:
1. Detect your Ubuntu version
2. Ask for confirmation to proceed
3. Request your sudo password (for system installations)
4. Install everything automatically
5. Verify all components are working

**No manual configuration needed!**

---

## What Happens Next?

After installation completes:

1. Run `npm run dev` to start the app
2. Open http://localhost:8000
3. Login with admin/password
4. Change the default password
5. Start using the app!

---

## Troubleshooting

**Installation failed?**
- Run the script again: `./install.sh`
- Check you're on Ubuntu/Debian
- Make sure you have internet connection

**Can't run `npm run dev`?**
- Make sure installation completed successfully
- Try: `sudo systemctl start mariadb`
- Check: `node -v` and `php -v`

**Still having issues?**
- Check README.md for detailed troubleshooting
- Contact the development team

---

**Need more details?** See the full [README.md](README.md) for comprehensive documentation.
