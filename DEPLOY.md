# Confinement & Wellness — Deployment Guide

> **VPS**: DigitalOcean Ubuntu 24.04 LTS (4 CPU / 8GB RAM / 160GB SSD)
> **Stack**: Nginx + PHP-FPM 8.3 + MySQL 8 + Redis + Let's Encrypt
> **Domain**: TBD (replace `yourdomain.com` throughout)

---

## 1. Initial Server Setup

```bash
# SSH into your VPS
ssh root@YOUR_SERVER_IP

# Update system
apt update && apt upgrade -y

# Create deploy user (don't run app as root)
adduser deploy
usermod -aG sudo deploy

# Setup SSH key for deploy user
mkdir -p /home/deploy/.ssh
cp ~/.ssh/authorized_keys /home/deploy/.ssh/
chown -R deploy:deploy /home/deploy/.ssh
chmod 700 /home/deploy/.ssh
chmod 600 /home/deploy/.ssh/authorized_keys

# Setup firewall
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable
```

---

## 2. Install PHP 8.3 + Extensions

```bash
apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update

apt install php8.3-fpm php8.3-cli php8.3-common php8.3-mysql \
    php8.3-xml php8.3-curl php8.3-mbstring php8.3-zip \
    php8.3-bcmath php8.3-gd php8.3-intl php8.3-readline \
    php8.3-redis php8.3-opcache -y

# Verify
php -v
systemctl status php8.3-fpm
```

### PHP-FPM Tuning

```bash
nano /etc/php/8.3/fpm/pool.d/www.conf
```

```ini
; Optimized for 8GB RAM
pm = dynamic
pm.max_children = 30
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 10
pm.max_requests = 500
```

```bash
systemctl restart php8.3-fpm
```

---

## 3. Install Nginx

```bash
apt install nginx -y
systemctl enable nginx
```

---

## 4. Install MySQL 8

```bash
apt install mysql-server -y
mysql_secure_installation

# Create database and user
mysql -u root -p
```

```sql
CREATE DATABASE confinement_wellness CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cw_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON confinement_wellness.* TO 'cw_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Install Redis

```bash
apt install redis-server -y

# Bind to localhost only
nano /etc/redis/redis.conf
# Set: supervised systemd
# Set: maxmemory 256mb
# Set: maxmemory-policy allkeys-lru

systemctl restart redis
systemctl enable redis

# Verify
redis-cli ping
# Should return PONG
```

---

## 6. Install Composer & Node.js

```bash
# Composer
cd /tmp
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Node.js 20 LTS (for Vite build)
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install nodejs -y

# Verify
composer --version
node -v
npm -v
```

---

## 7. Deploy Application

```bash
# Switch to deploy user
su - deploy

# Create web directory
sudo mkdir -p /var/www/confinement-wellness
sudo chown deploy:deploy /var/www/confinement-wellness

# Clone repository
cd /var/www/confinement-wellness
git clone YOUR_REPO_URL .

# Install PHP dependencies (production)
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm ci
npm run build

# Copy and configure environment
cp .env.example .env
nano .env
```

### Production `.env`

```env
APP_NAME="Confinement & Wellness"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=confinement_wellness
DB_USERNAME=cw_user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

FILESYSTEM_DISK=public
```

```bash
# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed initial data (first deploy only)
php artisan db:seed --force

# Storage link for uploads (SOP materials, profile photos)
php artisan storage:link

# Cache config, routes, views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R deploy:www-data /var/www/confinement-wellness
sudo chmod -R 775 storage bootstrap/cache
```

---

## 8. Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/confinement-wellness
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/confinement-wellness/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    # PWA manifest & service worker
    location /manifest.webmanifest {
        types { application/manifest+json webmanifest; }
        expires 1d;
    }
    location /sw.js {
        expires off;
        add_header Cache-Control "no-store, no-cache, must-revalidate";
    }

    # Static assets caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 32k;
        fastcgi_buffers 16 16k;
    }

    # Block access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Upload limit (SOP materials, profile photos)
    client_max_body_size 10M;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/confinement-wellness /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

# Test and reload
sudo nginx -t
sudo systemctl reload nginx
```

---

## 9. SSL with Let's Encrypt

```bash
apt install certbot python3-certbot-nginx -y

# Obtain certificate (make sure DNS A record points to server IP first)
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is set up automatically. Verify:
sudo certbot renew --dry-run
```

---

## 10. Queue Worker (Systemd)

```bash
sudo nano /etc/systemd/system/cw-queue.service
```

```ini
[Unit]
Description=C&W Queue Worker
After=redis.service mysql.service

[Service]
User=deploy
Group=www-data
WorkingDirectory=/var/www/confinement-wellness
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable cw-queue
sudo systemctl start cw-queue
sudo systemctl status cw-queue
```

---

## 11. Laravel Scheduler (Cron)

```bash
crontab -u deploy -e
```

```cron
* * * * * cd /var/www/confinement-wellness && php artisan schedule:run >> /dev/null 2>&1
```

---

## 12. OPcache Tuning

```bash
sudo nano /etc/php/8.3/fpm/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.revalidate_freq=0
```

> **Note**: `validate_timestamps=0` means OPcache won't check for file changes. You must run `php artisan opcache:clear` or restart PHP-FPM after each deploy.

```bash
sudo systemctl restart php8.3-fpm
```

---

## 13. Updating / Re-deploying

Run this every time you push new code:

```bash
cd /var/www/confinement-wellness

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Run new migrations
php artisan migrate --force

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.3-fpm
sudo systemctl restart cw-queue
```

### Quick Deploy Script (optional)

Save as `deploy.sh` at project root:

```bash
#!/bin/bash
set -e

echo ">>> Pulling latest code..."
git pull origin main

echo ">>> Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo ">>> Building frontend assets..."
npm ci && npm run build

echo ">>> Running migrations..."
php artisan migrate --force

echo ">>> Clearing and rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">>> Restarting services..."
sudo systemctl restart php8.3-fpm
sudo systemctl restart cw-queue

echo ">>> Deploy complete!"
```

```bash
chmod +x deploy.sh
# Usage: ./deploy.sh
```

---

## Test Accounts (after seeding)

| Role      | Email                        | Password   |
|-----------|------------------------------|------------|
| HQ Admin  | admin@confinement.com        | password   |
| Leader    | leader@confinement.com       | password   |
| Therapist | therapist1@confinement.com   | password   |
| Therapist | therapist2@confinement.com   | password   |
| Client    | client@confinement.com       | password   |
| Client    | nadia@example.com            | password   |

> **IMPORTANT**: Change all passwords immediately after first login on production.

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 502 Bad Gateway | `sudo systemctl restart php8.3-fpm` |
| Permission denied on storage | `sudo chmod -R 775 storage bootstrap/cache` |
| CSS/JS not loading | Run `npm run build` and check `php artisan storage:link` |
| Queue not processing | `sudo systemctl status cw-queue` and check logs |
| Blank page / 500 error | Check `storage/logs/laravel.log` |
| OPcache stale after deploy | `sudo systemctl restart php8.3-fpm` |
| Redis connection refused | `sudo systemctl start redis` |
| SSL cert expired | `sudo certbot renew` |
