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

- **PHP 8.2** with all extensions
- **MariaDB 10.x** database server
- **Node.js 18.x** LTS
- **Git** version control
- **Composer** PHP package manager
- **npm packages** for Vite and frontend tools

---

## Fresh Ubuntu?

Works on freshly installed Ubuntu/Debian! No prerequisites needed.

The installer will ask for:
1. Your sudo password (for system installations)
2. Confirmation to proceed

Everything else is automatic!

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
