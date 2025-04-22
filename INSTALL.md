# Polyprep Installation Guide

This guide will help you set up the Polyprep educational website on your Raspberry Pi 4 Model B.

## Prerequisites

- Raspberry Pi 4 Model B with Raspbian OS installed
- Apache web server
- PHP 7.4 or higher
- MySQL database server
- phpMyAdmin (optional, for database management)

## Installation Steps

### 1. Set up LAMP Stack on Raspberry Pi

If you haven't already set up a LAMP (Linux, Apache, MySQL, PHP) stack on your Raspberry Pi, follow these steps:

```bash
# Update your system
sudo apt update
sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install PHP and required extensions
sudo apt install php php-mysql php-gd php-mbstring php-xml php-curl -y

# Install MySQL
sudo apt install mariadb-server -y

# Secure MySQL installation
sudo mysql_secure_installation

# Install phpMyAdmin (optional)
sudo apt install phpmyadmin -y
```

### 2. Configure Apache

```bash
# Enable required Apache modules
sudo a2enmod rewrite
sudo a2enmod headers

# Restart Apache
sudo systemctl restart apache2
```

### 3. Create Database

```bash
# Log in to MySQL
sudo mysql -u root -p

# Create database and user (in MySQL prompt)
CREATE DATABASE polyprep;
CREATE USER 'polyprep_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON polyprep.* TO 'polyprep_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Deploy Polyprep Files

1. Clone or copy the Polyprep files to your web server directory:

```bash
# Navigate to web server directory
cd /var/www/html/

# Create Polyprep directory
sudo mkdir Polyprep

# Set ownership
sudo chown -R pi:www-data Polyprep/

# Set permissions
sudo chmod -R 750 Polyprep/
```

2. Transfer all files to the Raspberry Pi (from your development machine):

```bash
# Using scp (from your development machine)
scp -r /path/to/Polyprep/* pi@raspberry_pi_ip:/var/www/html/Polyprep/
```

### 5. Configure Database Connection

Edit the database configuration file to match your MySQL settings:

```bash
# Edit the database configuration file
sudo nano /var/www/html/Polyprep/config/database.php
```

Update the following lines with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'polyprep_user');  // The user you created
define('DB_PASS', 'your_password');  // The password you set
define('DB_NAME', 'polyprep');       // The database name
```

### 6. Import Database Schema

```bash
# Import the database schema
mysql -u polyprep_user -p polyprep < /var/www/html/Polyprep/database/polyprep.sql
```

### 7. Set Up Uploads Directory

```bash
# Create uploads directory if it doesn't exist
sudo mkdir -p /var/www/html/Polyprep/uploads

# Set proper permissions for uploads directory
sudo chown -R www-data:www-data /var/www/html/Polyprep/uploads
sudo chmod -R 755 /var/www/html/Polyprep/uploads
```

### 8. Configure Virtual Host (Optional)

For a cleaner URL, you can set up a virtual host:

```bash
# Create a new virtual host configuration
sudo nano /etc/apache2/sites-available/polyprep.conf
```

Add the following configuration:

```apache
<VirtualHost *:80>
    ServerName polyprep.local
    DocumentRoot /var/www/html/Polyprep
    
    <Directory /var/www/html/Polyprep>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/polyprep_error.log
    CustomLog ${APACHE_LOG_DIR}/polyprep_access.log combined
</VirtualHost>
```

Enable the virtual host:

```bash
sudo a2ensite polyprep.conf
sudo systemctl reload apache2
```

Add the domain to your hosts file (on your client machines):

```
192.168.x.x  polyprep.local
```

### 9. Access the Website

Open a web browser and navigate to:

- If using virtual host: `http://polyprep.local`
- If not using virtual host: `http://raspberry_pi_ip/Polyprep`

### 10. Default Admin Login

Use the following credentials to log in as admin:

- Username: `admin`
- Password: `admin123`

**Important:** Change the admin password immediately after your first login for security reasons.

## Troubleshooting

### Permissions Issues

If you encounter permission issues with the uploads directory:

```bash
sudo chown -R www-data:www-data /var/www/html/Polyprep/uploads
sudo chmod -R 755 /var/www/html/Polyprep/uploads
```

### Apache Error Logs

Check Apache error logs if you encounter issues:

```bash
sudo tail -f /var/log/apache2/error.log
```

### PHP Error Reporting

To enable detailed PHP error reporting for debugging:

1. Edit the PHP configuration:

```bash
sudo nano /var/www/html/Polyprep/index.php
```

2. Add these lines at the top:

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

Remember to remove these lines in production.

## Security Recommendations

1. Change the default admin password immediately
2. Set up HTTPS using Let's Encrypt
3. Regularly update your Raspberry Pi OS and all installed packages
4. Configure a firewall (e.g., ufw) to restrict access
5. Set up regular database backups
