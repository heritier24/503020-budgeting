# Rule 50 30 20

A comprehensive personal budgeting application built with Laravel 10, Vue.js 3, and TypeScript. This app helps users master the 50/30/20 budgeting rule, track expenses, set financial goals, and monitor loan payments.

## 🌟 Features

### Core Budgeting
- **50/30/20 Budget Rule**: Automatically allocates income into Needs (50%), Wants (30%), and Savings (20%)
- **Income Tracking**: Multiple income sources with expected monthly amounts
- **Expense Categorization**: Organized spending by categories (Housing, Groceries, Entertainment, etc.)
- **Real-time Budget Monitoring**: Visual indicators for budget usage and remaining amounts

### Financial Management
- **Transaction Management**: Add, edit, and categorize income and expenses
- **Loan Tracking**: Monitor active loans with monthly payment calculations
- **Financial Goals**: Set and track savings goals and debt payoff targets
- **Analytics Dashboard**: Comprehensive spending trends and budget health insights

### User Experience
- **Onboarding Wizard**: Guided setup for new users
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Data Export**: Export financial data for external analysis
- **Secure Authentication**: Laravel Breeze authentication with email verification

## 🚀 Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0 or higher
- Redis (optional, for caching)

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd 503020-budgeting
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rule503020_budgeting
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   Open your browser and navigate to `http://localhost:8000`

### Development Commands

```bash
# Start development with hot reload
npm run dev

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint

# Type checking
npm run type-check
```

## 🌐 DigitalOcean Deployment

### Quick DigitalOcean Setup

1. **Create a DigitalOcean Droplet**
   - Choose Ubuntu 22.04 LTS
   - Minimum 2GB RAM, 1 vCPU, 50GB SSD
   - Add your SSH key

2. **Clone and navigate to the project**
   ```bash
   git clone <repository-url>
   cd 503020-budgeting
   ```

3. **Run the automated deployment script**
   ```bash
   ./deploy-digitalocean.sh your-domain.com
   ```

The application will be automatically configured and available at `https://your-domain.com`

### Manual DigitalOcean Deployment

For detailed step-by-step instructions, see [DEPLOYMENT-DIGITALOCEAN.md](DEPLOYMENT-DIGITALOCEAN.md)

This includes:
- Complete LEMP stack setup (Linux, Nginx, MySQL, PHP)
- SSL certificate configuration with Let's Encrypt
- Security hardening and optimization
- Database setup and migrations
- Performance optimization

## 📁 Project Structure

```
503020-budgeting/
├── app/
│   ├── Http/Controllers/     # API and web controllers
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Providers/           # Service providers
├── database/
│   ├── migrations/          # Database schema migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── js/                 # Vue.js frontend
│   │   ├── Components/     # Reusable Vue components
│   │   ├── Pages/          # Page components
│   │   └── Layouts/        # Layout components
│   └── css/                # Stylesheets
├── routes/                 # Application routes
├── deploy-digitalocean.sh  # DigitalOcean deployment script
└── DEPLOYMENT-DIGITALOCEAN.md # Detailed deployment guide
```

## 🔧 Configuration

### Environment Variables

Key environment variables to configure:

```env
# Application
APP_NAME="Rule 50 30 20"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rule503020_budgeting
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 🎯 Usage Guide

### First Time Setup

1. **Register an account** at the landing page
2. **Complete onboarding** by setting up:
   - Budget configuration (50/30/20 percentages)
   - Income sources
   - Initial categories
3. **Start tracking** your finances

### Managing Your Budget

1. **Add Income Sources**
   - Go to Income section
   - Add salary, freelance, or other income sources
   - Set expected monthly amounts

2. **Track Expenses**
   - Use the quick-add buttons on dashboard
   - Categorize expenses properly
   - Monitor budget usage with visual indicators

3. **Set Financial Goals**
   - Create savings goals (emergency fund, vacation, etc.)
   - Set debt payoff targets
   - Track progress with visual progress bars

4. **Monitor Loans**
   - Add active loans with monthly payments
   - Track remaining balance and payment history
   - Budget calculations account for loan payments

### Analytics & Insights

- **Dashboard Overview**: Real-time budget status and quick actions
- **Analytics Page**: Detailed spending trends and budget health
- **Export Data**: Download financial data for external analysis

## 🔒 Security Features

- **CSRF Protection**: All forms protected against CSRF attacks
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Input sanitization and output escaping
- **Authentication**: Secure user authentication with Laravel Breeze
- **Rate Limiting**: API endpoints protected against abuse
- **Secure Headers**: Security headers configured in Nginx
- **SSL/HTTPS**: Automatic SSL certificate setup with Let's Encrypt

## 🚀 Performance Optimizations

- **OPcache**: PHP bytecode caching enabled
- **Asset Optimization**: Minified CSS/JS in production
- **Database Indexing**: Optimized database queries
- **Redis Caching**: High-performance caching and sessions
- **CDN Ready**: Static assets optimized for CDN delivery

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Run frontend tests
npm run test

# Run all tests
composer test
```

## 📊 API Documentation

The application provides RESTful APIs for:

- **Authentication**: Login, register, password reset
- **Dashboard**: Budget overview and statistics
- **Transactions**: CRUD operations for financial transactions
- **Categories**: Manage expense/income categories
- **Goals**: Financial goal management
- **Loans**: Loan tracking and payments
- **Analytics**: Spending trends and insights

API endpoints are prefixed with `/api/` and require authentication.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the troubleshooting section below

## 🔧 Troubleshooting

### Common Issues

1. **Build Errors**
   ```bash
   # Clear npm cache
   npm cache clean --force
   # Reinstall dependencies
   rm -rf node_modules package-lock.json
   npm install
   ```

2. **Database Connection Issues**
   - Verify database credentials in `.env`
   - Ensure MySQL service is running
   - Check database exists

3. **Permission Issues**
   ```bash
   # Fix storage permissions
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

4. **Server Issues**
   ```bash
   # Check service status
   sudo systemctl status nginx php8.2-fpm mysql redis-server
   # Restart services
   sudo systemctl restart nginx php8.2-fpm mysql redis-server
   ```

### Performance Issues

- Enable OPcache in production
- Use Redis for caching and sessions
- Optimize database queries
- Enable gzip compression
- Use CDN for static assets

---

**Built with ❤️ by the Rule 50 30 20 team**