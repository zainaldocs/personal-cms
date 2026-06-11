#!/bin/bash
set -e

echo "=== STARTING AUTOMATED DEPLOYMENT OF PERSONAL CMS ==="

# 1. Update packages
echo "--> Updating package indexes..."
apt-get update -y

# 2. Install Apache, MySQL, PHP, and extensions
echo "--> Installing LAMP stack..."
apt-get install -y apache2 mysql-server php libapache2-mod-php php-mysql php-gd php-mbstring php-xml php-curl php-zip git

# Enable services
systemctl enable apache2
systemctl enable mysql

# Start services
systemctl start apache2 || true
systemctl start mysql || true

# 3. Configure MySQL Database
echo "--> Setting up MySQL Database and User..."
# Create database and user if not exists
mysql -u root -e "CREATE DATABASE IF NOT EXISTS personal_cms DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE USER IF NOT EXISTS 'cms_user'@'localhost' IDENTIFIED BY '@sshzainalZ22';"
mysql -u root -e "GRANT ALL PRIVILEGES ON personal_cms.* TO 'cms_user'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

# 4. Clean web root and clone repo
echo "--> Preparing web directory and cloning source code..."
rm -rf /var/www/html/*
git clone https://github.com/zainaldocs/personal-cms.git /var/www/html

# 5. Set correct permissions
echo "--> Configuring permissions for Apache..."
chown -R www-data:www-data /var/www/html
find /var/www/html -type d -exec chmod 755 {} \;
find /var/www/html -type f -exec chmod 644 {} \;

# Ensure uploads directory is writeable
mkdir -p /var/www/html/assets/images
chown -R www-data:www-data /var/www/html/assets/images
chmod -R 775 /var/www/html/assets/images

# 6. Update database config
echo "--> Writing database settings to config.php..."
CONFIG_FILE="/var/www/html/includes/config.php"

# Replace credentials using sed
sed -i "s/define('DB_USER', 'root');/define('DB_USER', 'cms_user');/g" $CONFIG_FILE
sed -i "s/define('DB_PASS', '');/define('DB_PASS', '@sshzainalZ22');/g" $CONFIG_FILE

# 7. Import Database Schema
echo "--> Importing SQL schema..."
mysql -u cms_user -p'@sshzainalZ22' personal_cms < /var/www/html/schema.sql

# 8. Enable Apache configurations
echo "--> Finalizing Web Server configurations..."
a2enmod rewrite
systemctl restart apache2

echo "=== DEPLOYMENT COMPLETED SUCCESSFULLY! ==="
echo "Your site is now live on your VPS IP address!"
