# 🚀 Rule 50 30 20 - DigitalOcean Deployment Guide

This guide will walk you through deploying the Rule 50 30 20 budgeting app on DigitalOcean without Docker, using a traditional LEMP stack (Linux, Nginx, MySQL, PHP).

## 📋 Prerequisites

- DigitalOcean account
- Domain name (optional, but recommended)
- Basic knowledge of Linux command line
- SSH access to your server

## 🖥️ Server Requirements

### Minimum Requirements
- **Droplet Size**: 2GB RAM, 1 vCPU, 50GB SSD
- **OS**: Ubuntu 22.04 LTS
- **Storage**: 50GB minimum

### Recommended for Production
- **Droplet Size**: 4GB RAM, 2 vCPU, 80GB SSD
- **OS**: Ubuntu 22.04 LTS
- **Storage**: 80GB minimum

## 🏗️ Step 1: Create DigitalOcean Droplet

1. **Log into DigitalOcean**
   - Go to [DigitalOcean](https://digitalocean.com)
   - Sign in to your account

2. **Create a New Droplet**
   - Click "Create" → "Droplets"
   - Choose **Ubuntu 22.04 LTS**
   - Select your preferred size (minimum 2GB RAM)
   - Choose a datacenter region close to your users
   - Add SSH keys for secure access
   - Give your droplet a name (e.g., `rule-50-30-20-app`)

3. **Wait for Creation**
   - Wait 2-3 minutes for the droplet to be created
   - Note down the IP address

## 🔧 Step 2: Initial Server Setup

### Connect to Your Server
```bash
ssh root@YOUR_SERVER_IP
```

### Update System Packages
```bash
apt update && apt upgrade -y
```

### Create a Non-Root User
```bash
adduser rule503020
usermod -aG sudo rule503020
su - rule503020
```

## 🛠️ Step 3: Install Required Software

### Install Nginx
```bash
sudo apt install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```

### Install MySQL
```bash
sudo apt install mysql-server -y
sudo systemctl start mysql
sudo systemctl enable mysql
sudo mysql_secure_installation
```

### Install PHP 8.2 and Extensions
```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-cli -y
```

### Install Composer
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### Install Node.js 18
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### Install Redis (Optional but Recommended)
```bash
sudo apt install redis-server -y
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

## 🗄️ Step 4: Database Setup

### Create Database and User
```bash
sudo mysql -u root -p
```

In MySQL console:
```sql
CREATE DATABASE rule503020_budgeting;
CREATE USER 'rule503020_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON rule503020_budgeting.* TO 'rule503020_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 📁 Step 5: Deploy Application Code

### Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/your-username/503020-budgeting.git rule-50-30-20
sudo chown -R rule503020:rule503020 /var/www/rule-50-30-20
cd /var/www/rule-50-30-20
```

### Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### Set Up Environment
```bash
cp .env.example .env
nano .env
```

Update the following in `.env`:
```env
APP_NAME="Rule 50 30 20"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rule503020_budgeting
DB_USERNAME=rule503020_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Generate Application Key
```bash
php artisan key:generate
```

### Run Database Migrations
```bash
php artisan migrate --force
php artisan db:seed --force
```

### Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/rule-50-30-20
sudo chmod -R 755 /var/www/rule-50-30-20
sudo chmod -R 775 /var/www/rule-50-30-20/storage
sudo chmod -R 775 /var/www/rule-50-30-20/bootstrap/cache
```

## 🌐 Step 6: Configure Nginx

### Create Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/rule-50-30-20
```

Add the following configuration:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/rule-50-30-20/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Handle PHP files
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
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
}
```

### Enable the Site
```bash
sudo ln -s /etc/nginx/sites-available/rule-50-30-20 /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔒 Step 7: SSL Certificate with Let's Encrypt

### Install Certbot
```bash
sudo apt install certbot python3-certbot-nginx -y
```

### Obtain SSL Certificate
```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### Test Auto-Renewal
```bash
sudo certbot renew --dry-run
```

## ⚡ Step 8: Optimize for Production

### Configure PHP-FPM
```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

Update these settings:
```ini
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 50M
post_max_size = 50M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2
```

### Restart PHP-FPM
```bash
sudo systemctl restart php8.2-fpm
```

### Optimize Laravel
```bash
cd /var/www/rule-50-30-20
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔄 Step 9: Set Up Process Management

### Install Supervisor
```bash
sudo apt install supervisor -y
```

### Create Queue Worker Configuration
```bash
sudo nano /etc/supervisor/conf.d/rule-50-30-20-worker.conf
```

Add:
```ini
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
```

### Start Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start rule-50-30-20-worker:*
```

## 📊 Step 10: Set Up Monitoring and Logs

### Install Fail2Ban
```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### Set Up Log Rotation
```bash
sudo nano /etc/logrotate.d/rule-50-30-20
```

Add:
```
/var/www/rule-50-30-20/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

## 🚀 Step 11: Final Steps

### Test the Application
1. Visit `https://your-domain.com`
2. Register a new account
3. Complete the onboarding process
4. Test all major features

### Set Up Backups
```bash
sudo nano /etc/cron.daily/tamba-backup
```

Add:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u rule503020_user -p'your_secure_password' rule503020_budgeting > /var/backups/rule503020_budgeting_$DATE.sql
tar -czf /var/backups/rule503020_budgeting_files_$DATE.tar.gz /var/www/rule-50-30-20
find /var/backups -name "rule503020_budgeting_*" -mtime +7 -delete
```

```bash
sudo chmod +x /etc/cron.daily/tamba-backup
```

## 🔧 Step 12: Deployment Script

Create a deployment script for easy updates:

```bash
nano /var/www/rule-50-30-20/deploy.sh
```

Add:
```bash
#!/bin/bash
set -e

echo "🚀 Deploying Rule 50 30 20..."

# Navigate to project directory
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
```

```bash
chmod +x /var/www/rule-50-30-20/deploy.sh
```

## 🛡️ Security Checklist

- [ ] Firewall configured (UFW)
- [ ] SSH key authentication only
- [ ] Regular security updates
- [ ] Database secured with strong password
- [ ] SSL certificate installed
- [ ] Fail2Ban configured
- [ ] Regular backups scheduled
- [ ] File permissions set correctly

## 🔍 Troubleshooting

### Common Issues

1. **502 Bad Gateway**
   ```bash
   sudo systemctl status php8.2-fpm
   sudo systemctl restart php8.2-fpm
   ```

2. **Permission Denied**
   ```bash
   sudo chown -R www-data:www-data /var/www/rule-50-30-20
   sudo chmod -R 775 /var/www/rule-50-30-20/storage
   ```

3. **Database Connection Error**
   ```bash
   sudo systemctl status mysql
   mysql -u rule503020_user -p rule503020_budgeting
   ```

4. **Queue Workers Not Running**
   ```bash
   sudo supervisorctl status
   sudo supervisorctl restart rule-50-30-20-worker:*
   ```

### Useful Commands

```bash
# Check application logs
tail -f /var/www/rule-50-30-20/storage/logs/laravel.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Restart all services
sudo systemctl restart nginx php8.2-fpm mysql redis-server
```

## 📈 Performance Optimization

### Enable Redis for Sessions and Cache
Update `.env`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Configure OPcache
```bash
sudo nano /etc/php/8.2/fpm/conf.d/10-opcache.ini
```

Add:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

## 🎉 Congratulations!

Your Rule 50 30 20 is now successfully deployed on DigitalOcean! 

### Next Steps:
1. Set up monitoring (e.g., New Relic, DataDog)
2. Configure CDN for static assets
3. Set up automated backups
4. Monitor server performance
5. Keep the application updated

### Support:
- Check application logs for errors
- Monitor server resources
- Keep dependencies updated
- Regular security audits

---

**Built with ❤️ by the Rule 50 30 20 team**