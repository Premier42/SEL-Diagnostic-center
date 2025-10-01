# SEL Diagnostic Center - Laboratory Management System

A modern, feature-rich laboratory management system built with PHP and MySQL, designed specifically for diagnostic centers and pathology laboratories.

## 🚀 Features

### Core Features
- **Modern UI/UX**: Professional gradient design with Bootstrap 5
- **Responsive Design**: Mobile-first approach with touch-friendly interfaces
- **Real-time Dashboard**: Live statistics and performance metrics
- **Secure Authentication**: CSRF protection and session management
- **Bengali Localization**: Support for local language and currency (৳)

### Medical Operations
- **Invoice Management**: Create, track, and manage patient invoices with PDF generation
- **Test Management**: Comprehensive laboratory test catalog with edit functionality
- **Patient Records**: Complete patient information management
- **Payment Tracking**: Support for multiple payment methods with real-time updates
- **Report Generation**: Professional test reports with lab tech data entry
- **Doctor Management**: Maintain referring physician database

### Administrative Features
- **User Management**: Role-based access control (Admin, Staff, Technician)
- **Audit Logging**: Complete activity tracking with 30+ log entries
- **SMS Notifications**: Bangladesh SMS integration via SMS.NET.BD
- **Inventory Management**: Track reagents, consumables, and lab supplies
- **Lab Tech Interface**: Data entry for test results and reports
- **Search & Filtering**: Advanced search across all modules
- **Data Export**: CSV export capabilities
- **Performance Analytics**: Real-time system metrics

## 🛠 Technology Stack

- **Backend**: PHP 8.4+ with modern OOP architecture
- **Database**: MySQL 8.0+ with UTF-8mb4 support
- **Frontend**: Bootstrap 5.3, FontAwesome 6.4, vanilla JavaScript
- **Architecture**: Clean MVC pattern with service layer
- **Security**: CSRF protection, input validation, XSS prevention

## 📁 Project Structure

```
├── src/                    # Application source code
│   └── Controllers/        # Request handlers
│       ├── AuthController.php
│       ├── DashboardController.php
│       ├── InvoiceController.php
│       ├── TestController.php
│       ├── ReportController.php
│       ├── UserController.php
│       ├── DoctorController.php
│       ├── AuditController.php
│       ├── InventoryController.php
│       └── SmsController.php
├── views/                 # View templates
│   ├── auth/              # Authentication views
│   ├── dashboard/         # Dashboard components
│   ├── invoices/          # Invoice management (with PDF)
│   ├── tests/             # Test management (CRUD)
│   ├── reports/           # Lab reports and data entry
│   ├── users/             # User management
│   ├── doctors/           # Doctor management
│   ├── audit/             # Audit log viewer
│   ├── inventory/         # Inventory management
│   ├── sms/               # SMS dashboard
│   └── errors/            # Error pages
├── public/                # Web root directory
│   ├── index.php          # Application entry point
│   └── js/                # JavaScript files
│       └── phone-formatter.js  # Bangladesh phone auto-formatter
├── config/                # Configuration files
├── database/              # Database schemas and seed data
│   ├── core_schema.sql
│   ├── medical_schema.sql
│   ├── invoice_schema.sql
│   ├── sms_schema.sql
│   ├── audit_schema.sql
│   ├── inventory_schema.sql
│   └── seed_data.sql
├── .env                   # Environment configuration (gitignored)
├── .env.example           # Environment template
├── .gitignore             # Git ignore rules
├── bootstrap.php          # Application bootstrap
└── README.md              # This file
```

## ⚡ Quick Start

### Prerequisites
- PHP 8.4 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SEL-Diagnostic-center
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

3. **Set up database**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

   # Import schemas in order
   mysql -u root -p pathology_lab < database/core_schema.sql
   mysql -u root -p pathology_lab < database/medical_schema.sql
   mysql -u root -p pathology_lab < database/invoice_schema.sql
   mysql -u root -p pathology_lab < database/sms_schema.sql
   mysql -u root -p pathology_lab < database/audit_schema.sql
   mysql -u root -p pathology_lab < database/inventory_schema.sql
   mysql -u root -p pathology_lab < database/seed_data.sql
   ```

4. **Start the application**
   ```bash
   # Development server
   php -S localhost:8000 -t public

   # Or configure Apache/Nginx to point to public/ directory
   ```

5. **Configure SMS (Optional)**
   - Sign up at https://sms.net.bd/signup/ for free credits
   - Get your API key from the dashboard
   - Update `SMS_API_KEY` in `.env` file
   - See `SMS_PROVIDER_SETUP.md` for detailed instructions

6. **Access the application**
   - Open `http://localhost:8000` in your browser
   - Default login: `admin` / `admin123`

## 🔧 Configuration

### Environment Variables (.env)
```env
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=pathology_lab
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Application Settings
APP_NAME="SEL Diagnostic Center"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

# Security Settings
APP_KEY=your-secret-key-here
SESSION_LIFETIME=120
```

### Database Configuration
The system uses MySQL with the following key tables:
- `users` - User accounts and authentication
- `invoices` - Patient invoices and billing
- `tests` - Laboratory test catalog
- `doctors` - Referring physician information
- `invoice_tests` - Test-invoice relationships
- `test_reports` - Lab reports and results
- `test_results` - Individual test parameter results
- `sms_logs` - SMS notification history
- `audit_logs` - System activity tracking
- `inventory_items` - Lab supplies and consumables

## 🎯 Usage Guide

### Dashboard
- **Statistics Overview**: Total invoices, tests, revenue, and growth metrics
- **Recent Activity**: Latest invoices and system activity
- **Quick Actions**: Fast access to common operations
- **Performance Charts**: Visual representation of key metrics

### Invoice Management
- **Create Invoices**: Add patient details and select tests
- **Track Payments**: Monitor payment status and amounts
- **Search & Filter**: Find invoices by patient, date, or status
- **Print Invoices**: Professional invoice printing

### Test Management (Admin Only)
- **Add Tests**: Create new laboratory tests with pricing
- **Categorize**: Organize tests by medical categories
- **Manage Pricing**: Update test costs and descriptions
- **Track Usage**: Monitor test popularity and revenue

## 🧪 Testing

### Manual Testing
The application has been thoroughly tested for:
- ✅ Authentication and session management
- ✅ Database connectivity and data integrity
- ✅ Invoice CRUD operations
- ✅ Responsive design and UI/UX
- ✅ Error handling and security
- ✅ API endpoints functionality

### Automated Testing
Run the Python unit test suite:
```bash
python3 test_api.py
```

The test suite covers:
- API endpoint functionality
- Authentication flows
- Database operations
- Error handling
- Security measures

## 🔒 Security Features

- **CSRF Protection**: All forms include security tokens
- **Input Validation**: Comprehensive server-side validation
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Output escaping and sanitization
- **Session Security**: Secure session configuration with regeneration
- **Role-based Access**: Middleware-based authorization system

## 🌐 API Endpoints

### Authentication
- `GET /` - Login page / Dashboard
- `POST /` - Login authentication
- `GET /logout` - User logout

### Dashboard
- `GET /dashboard` - Main dashboard
- `GET /api/dashboard-stats` - Dashboard statistics (JSON)

### Invoices
- `GET /invoices` - Invoice listing
- `GET /invoices/create` - Create invoice form
- `POST /invoices/store` - Store new invoice
- `GET /invoices/{id}` - View specific invoice
- `GET /invoices/{id}/pdf` - Generate PDF invoice
- `POST /invoices/{id}/update-payment` - Update payment status

### Tests
- `GET /tests` - Test listing
- `GET /tests/create` - Create test form (Admin)
- `POST /tests/store` - Store new test (Admin)
- `GET /tests/{code}` - View specific test
- `GET /tests/{code}/edit` - Edit test form (Admin)
- `POST /tests/{code}/update` - Update test (Admin)

### Reports
- `GET /reports` - Report listing
- `GET /reports/{id}` - View report details
- `GET /reports/{id}/edit` - Lab tech data entry form
- `POST /reports/{id}/update` - Update report with results

### Users
- `GET /users` - User listing (Admin)
- `GET /users/create` - Create user form (Admin)
- `POST /users/store` - Store new user (Admin)
- `DELETE /users/{id}` - Delete user (Admin)

### Audit Logs
- `GET /audit` - Audit log viewer (Admin)

### Inventory
- `GET /inventory` - Inventory management (Admin)

### SMS
- `GET /sms` - SMS dashboard (Admin)
- `POST /sms/send` - Send SMS notification (Admin)

## 📊 Performance

The system is optimized for performance with:
- **Database Indexing**: Optimized queries with proper indexes
- **Pagination**: Large datasets are paginated for faster loading
- **Caching**: Session-based caching for frequently accessed data
- **Minified Assets**: Compressed CSS and JavaScript
- **Lazy Loading**: Images and non-critical content loaded on demand

## 🛡 Maintenance

### Regular Tasks
- Monitor application logs in `logs/` directory
- Backup database regularly
- Update dependencies when security patches are available
- Review user access and permissions periodically

### Monitoring
- Check dashboard statistics for system health
- Monitor database performance and storage usage
- Review audit logs for unusual activity
- Ensure backup systems are functioning

## 🤝 Contributing

1. Follow PSR-4 autoloading standards
2. Use proper namespacing and class organization
3. Implement comprehensive input validation
4. Include proper error handling and logging
5. Add CSRF protection to all forms
6. Write unit tests for new functionality
7. Update documentation for new features

## 📝 License

This project is proprietary software developed for SEL Diagnostic Center.

## 🆘 Support

For technical support or feature requests:
1. Check the application logs in `logs/` directory
2. Review this documentation
3. Contact the development team

---

**SEL Diagnostic Center Laboratory Management System** - Streamlining pathology operations with modern technology.