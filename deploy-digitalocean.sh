#!/bin/bash

# Rule 50 30 20 - DigitalOcean Deployment Script
set -e

echo "🚀 Starting Rule 50 30 20 deployment on DigitalOcean..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Configuration
DOMAIN=${1:-"your-domain.com"}
DB_PASSWORD=${2:-"$(openssl rand -base64 32)"}
APP_KEY=${3:-"base64:$(openssl rand -base64 32)"}

print_step "Deploying to domain: $DOMAIN"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please do not run this script as root. Use a sudo user instead."
    exit 1
fi

# Update system
print_step "Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install required packages
print_step "Installing required packages..."
sudo apt install -y nginx mysql-server software-properties-common curl wget git unzip

# Install PHP 8.2
print_step "Installing PHP 8.2 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-cli

# Install Composer
print_step "Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# Install Node.js 18
print_step "Installing Node.js 18..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
fi

# Install Redis
print_step "Installing Redis..."
sudo apt install -y redis-server

# Configure MySQL
print_step "Configuring MySQL..."
sudo mysql_secure_installation <<EOF

y
$DB_PASSWORD
$DB_PASSWORD
y
y
y
y
EOF

# Create database and user
print_step "Creating database and user..."
sudo mysql -u root -p$DB_PASSWORD <<EOF
CREATE DATABASE IF NOT EXISTS rule503020_budgeting;
CREATE USER IF NOT EXISTS 'rule503020_user'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON rule503020_budgeting.* TO 'rule503020_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Create project directory
print_step "Setting up project directory..."
sudo mkdir -p /var/www
cd /var/www

# Clone repository (you'll need to update this with your actual repository URL)
print_step "Cloning repository..."
if [ ! -d "rule-50-30-20" ]; then
    sudo git clone https://github.com/your-username/503020-budgeting.git rule-50-30-20
fi

sudo chown -R $USER:www-data /var/www/rule-50-30-20
cd /var/www/rule-50-30-20

# Install dependencies
print_step "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

print_step "Installing Node.js dependencies..."
npm install

# Build frontend assets
print_step "Building frontend assets..."
npm run build

# Set up environment
print_step "Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Update .env file
cat > .env <<EOF
APP_NAME="Rule 50 30 20"
APP_ENV=production
APP_KEY=$APP_KEY
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://$DOMAIN

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rule503020_budgeting
DB_USERNAME=rule503020_user
DB_PASSWORD=$DB_PASSWORD

SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@$DOMAIN"
MAIL_FROM_NAME="Rule 50 30 20"

VITE_APP_NAME="Rule 50 30 20"
EOF

# Set permissions
print_step "Setting permissions..."
sudo chown -R www-data:www-data /var/www/rule-50-30-20
sudo chmod -R 755 /var/www/rule-50-30-20
sudo chmod -R 775 /var/www/rule-50-30-20/storage
sudo chmod -R 775 /var/www/rule-50-30-20/bootstrap/cache

# Run database migrations
print_step "Running database migrations..."
php artisan migrate --force
php artisan db:seed --force

# Optimize Laravel
print_step "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configure Nginx
print_step "Configuring Nginx..."
sudo tee /etc/nginx/sites-available/rule-50-30-20 > /dev/null <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;
    root /var/www/rule-50-30-20/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    # Handle Laravel routes
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Handle PHP files
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
}
EOF

# Enable the site
sudo ln -sf /etc/nginx/sites-available/rule-50-30-20 /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Install Certbot for SSL
print_step "Installing SSL certificate..."
sudo apt install -y certbot python3-certbot-nginx

# Start and enable services
print_step "Starting services..."
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
sudo systemctl start mysql
sudo systemctl enable mysql
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Install Supervisor for queue workers
print_step "Setting up queue workers..."
sudo apt install -y supervisor

sudo tee /etc/supervisor/conf.d/rule-50-30-20-worker.conf > /dev/null <<EOF
[program:rule-50-30-20-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/rule-50-30-20/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/rule-50-30-20/storage/logs/worker.log
stopwaitsecs=3600
EOF

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start rule-50-30-20-worker:*

# Install Fail2Ban for security
print_step "Setting up security..."
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban

# Configure firewall
print_step "Configuring firewall..."
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Set up log rotation
print_step "Setting up log rotation..."
sudo tee /etc/logrotate.d/rule-50-30-20 > /dev/null <<EOF
/var/www/rule-50-30-20/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
EOF

# Create deployment script
print_step "Creating deployment script..."
tee /var/www/rule-50-30-20/deploy.sh > /dev/null <<EOF
#!/bin/bash
set -e

echo "🚀 Deploying Rule 50 30 20..."

cd /var/www/rule-50-30-20

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/rule-50-30-20
sudo chmod -R 755 /var/www/rule-50-30-20
sudo chmod -R 775 /var/www/rule-50-30-20/storage
sudo chmod -R 775 /var/www/rule-50-30-20/bootstrap/cache

# Restart services
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart rule-50-30-20-worker:*

echo "✅ Deployment completed successfully!"
EOF

chmod +x /var/www/rule-50-30-20/deploy.sh

# Final status check
print_step "Checking service status..."
sudo systemctl status nginx --no-pager -l
sudo systemctl status php8.2-fpm --no-pager -l
sudo systemctl status mysql --no-pager -l
sudo systemctl status redis-server --no-pager -l

print_status "✅ DigitalOcean deployment completed successfully!"
print_status "🌐 Your application is now available at: http://$DOMAIN"
print_status "🔒 To enable SSL, run: sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"

echo ""
print_status "📋 Important Information:"
echo "   - Database Password: $DB_PASSWORD"
echo "   - Application Key: $APP_KEY"
echo "   - Project Location: /var/www/rule-50-30-20"
echo "   - Deployment Script: /var/www/rule-50-30-20/deploy.sh"

echo ""
print_status "🔧 Useful Commands:"
echo "   - View logs: tail -f /var/www/rule-50-30-20/storage/logs/laravel.log"
echo "   - Restart services: sudo systemctl restart nginx php8.2-fpm mysql redis-server"
echo "   - Update application: cd /var/www/rule-50-30-20 && ./deploy.sh"

print_warning "🔒 Security Reminders:"
echo "   - Set up SSL certificate with: sudo certbot --nginx -d $DOMAIN"
echo "   - Configure regular backups"
echo "   - Monitor server resources"
echo "   - Keep system updated"

print_status "🎉 Rule 50 30 20 is now live on DigitalOcean!"