# Part 1: Core System & Authentication (Project Lead)

This package contains the core system files for the Pathology Laboratory Management System. As the project lead, you are responsible for the foundation and integration of all team contributions.

## Project Structure (Group Project)

This project is divided into 4 parts for collaborative development:

### Part 1: Core System & Authentication (Project Lead)
- Authentication system and session management
- Database configuration and core utilities
- Main dashboard and navigation
- System configuration and middleware

### Part 2: Patient & Invoice Management
- Invoice creation and management
- Patient data handling
- Payment processing and tracking
- Billing system integration

### Part 3: Medical Operations
- Laboratory test management
- Doctor profiles and referrals
- Test reports and results
- Medical data processing

### Part 4: System Management & Utilities
- User management and roles
- Inventory/consumables tracking
- Audit logging system
- SMS notifications and data export

## Features

### Core Features
- **Modern UI**: Bootstrap 5 with responsive design
- **Role-based Access**: Admin, Staff, Technician roles
- **Session Management**: Secure authentication system
- **Audit Trail**: Complete activity logging
- **Bangladesh Localization**: Currency (৳), phone format (+880)

### Medical Features
- **Invoice Management**: Create invoices with multiple tests
- **Test Management**: Laboratory test catalog with parameters
- **Doctor Management**: Referral tracking and profiles
- **Report Management**: Test results and verification
- **Result Entry**: Parameter-based result system

### Administrative Features
- **User Management**: Role-based user administration
- **Inventory Management**: Stock tracking with alerts
- **SMS Notifications**: Patient notifications via Textbelt
- **Data Export**: CSV export for all modules
- **System Monitoring**: Real-time alerts and statistics

## Technology Stack

- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL 5.7+ with UTF-8 support
- **Frontend**: Bootstrap 5.3.2, FontAwesome 6.4
- **JavaScript**: ES6+ with Fetch API
- **SMS Integration**: Textbelt API (free tier)
- **Architecture**: MVC pattern with service layer
│   ├── Controllers/       # Request handlers
│   ├── Models/           # Data layer
│   ├── Services/         # Business logic
│   └── Middleware/       # Request middleware
├── views/                # View templates
│   ├── layouts/
│   ├── auth/
│   ├── admin/
│   └── staff/
├── public/               # Public web root
│   └── index.php        # Application entry point
├── config/               # Legacy configuration (deprecated)
├── assets/               # Static assets
├── logs/                 # Application logs
├── .env                  # Environment configuration
├── .htaccess            # Apache configuration
├── bootstrap.php        # Application bootstrap
└── composer.json        # Dependencies
```

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd NPL
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

4. **Set up database**
   - Create a MySQL database named `diagnostic_center`
   - Import your existing database schema
   - Update database credentials in `.env`

5. **Configure web server**
   - Point document root to the `NPL` directory
   - Ensure Apache mod_rewrite is enabled
   - The `.htaccess` file will handle routing

## Configuration

Edit the `.env` file to configure:

- Database connection
- Application settings
- Security settings
- Logging preferences

## Usage

### Access the Application

- **Login**: Navigate to `/NPL/` or `/NPL/login`
- **Admin Dashboard**: `/NPL/admin/dashboard`
- **Staff Dashboard**: `/NPL/staff/dashboard`

### Key Features

1. **Invoice Management**
   - Create new invoices with patient details
   - Add multiple tests to invoices
   - Track payment status
   - Search and filter invoices

2. **Test Management** (Admin only)
   - Add new tests with parameters
   - Organize tests by categories
   - Set pricing and descriptions

3. **Report Generation**
   - Generate reports for completed tests
   - Add test results and parameters
   - Verify and approve reports

4. **User Management** (Admin only)
   - Create staff and admin accounts
   - Manage user roles and permissions

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Output escaping and sanitization
- **Session Security**: Secure session configuration
- **Role-based Access Control**: Middleware-based authorization

## Development

### Adding New Features

1. **Models**: Extend the `Model` base class in `src/Models/`
2. **Controllers**: Extend `BaseController` in `src/Controllers/`
3. **Services**: Create business logic classes in `src/Services/`
4. **Views**: Add templates in `views/` directory
5. **Routes**: Register routes in `bootstrap.php`

### Code Standards

- Follow PSR-4 autoloading standards
- Use proper namespacing
- Implement validation for all inputs
- Include error handling
- Add CSRF protection to forms
- Use the service layer for business logic

## Migration from Legacy Code

The legacy codebase has been completely refactored:

- **Old structure**: Mixed PHP/HTML files with inline database queries
- **New structure**: Clean MVC with separation of concerns
- **Database layer**: From direct PDO to Model abstraction
- **Validation**: From inline checks to dedicated Validator class
- **Error handling**: From basic error display to comprehensive logging
- **Security**: Enhanced with CSRF, input validation, and sanitization

## Troubleshooting

1. **Database Connection Issues**
   - Check `.env` database credentials
   - Ensure MySQL service is running
   - Verify database exists

2. **Permission Errors**
   - Ensure `logs/` directory is writable
   - Check file permissions on uploaded files

3. **Routing Issues**
   - Verify Apache mod_rewrite is enabled
   - Check `.htaccess` file exists and is readable

## Support

For issues or questions, please refer to the application logs in the `logs/` directory or contact the development team.
