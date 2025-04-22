# Securing Database Credentials

For maximum security, follow these steps to move your database credentials outside the web root:

## 1. Create a Secure Config File

Create a directory outside your web root:

```bash
# On your Raspberry Pi
sudo mkdir -p /etc/polyprep
```

Create a secure config file:

```bash
sudo nano /etc/polyprep/config.php
```

Add this content:

```php
<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'polyprep');
define('DB_PASS', 'E7a74c6464774@9s');  // Your actual password
define('DB_NAME', 'polyprep');

// Security settings
define('ENCRYPTION_KEY', ''); // Generate a random key with: openssl rand -hex 32
?>
```

Set proper permissions:

```bash
sudo chown www-data:www-data /etc/polyprep/config.php
sudo chmod 600 /etc/polyprep/config.php
```

## 2. Update Your Application

Modify your `config/database.php` file to use the secure config:

```php
<?php
// Include secure config file
require_once '/etc/polyprep/config.php';

/**
 * Get database connection
 * 
 * @return mysqli Database connection object
 */
function getDbConnection() {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Rest of the function remains the same...
}
?>
```

## 3. Update API Config

Modify your `api/config.php` to use the secure config:

```php
<?php
// Include secure config file
require_once '/etc/polyprep/config.php';

// Rest of the file remains the same...
?>
```

## Security Benefits

1. Credentials are stored outside the web root, so they can't be accessed via the web
2. File permissions are set to be readable only by the web server
3. Single source of truth for credentials, making updates easier
4. Additional protection against source code disclosure vulnerabilities
