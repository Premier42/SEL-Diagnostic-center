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
- **Invoice Management**: Create, track, and manage patient invoices
- **Test Management**: Comprehensive laboratory test catalog
- **Patient Records**: Complete patient information management
- **Payment Tracking**: Support for multiple payment methods
- **Report Generation**: Professional test reports and results

### Administrative Features
- **User Management**: Role-based access control (Admin, Staff, Technician)
- **Audit Logging**: Complete activity tracking and monitoring
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
│   ├── Controllers/        # Request handlers
│   ├── Models/            # Data models
│   ├── Services/          # Business logic
│   └── Core/              # Core framework components
├── views/                 # View templates
│   ├── auth/              # Authentication views
│   ├── dashboard/         # Dashboard components
│   ├── invoices/          # Invoice management
│   ├── tests/             # Test management
│   └── errors/            # Error pages
├── public/                # Web root directory
│   └── index.php         # Application entry point
├── config/                # Configuration files
├── database/              # Database schemas and migrations
├── assets/                # Static assets (CSS, JS, images)
├── .env                   # Environment configuration
├── bootstrap.php          # Application bootstrap
└── test_api.py           # Python unit tests
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

   # Import schema
   mysql -u root -p pathology_lab < database/core_schema.sql
   ```

4. **Start the application**
   ```bash
   # Development server
   php -S localhost:8000 -t public

   # Or configure Apache/Nginx to point to public/ directory
   ```

5. **Access the application**
   - Open `http://localhost:8000` in your browser
   - Default login: `admin@example.com` / `admin123`

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

### Tests
- `GET /tests` - Test listing
- `GET /tests/create` - Create test form (Admin)
- `POST /tests/store` - Store new test (Admin)
- `GET /tests/{code}` - View specific test

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