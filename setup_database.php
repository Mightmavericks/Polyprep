<?php
// Database configuration
require_once 'config/database.php';

// Function to check if database exists
function databaseExists($host, $user, $pass, $dbName) {
    try {
        $conn = new mysqli($host, $user, $pass);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");
        $exists = $result->num_rows > 0;
        
        $conn->close();
        return $exists;
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error checking database: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Function to create database and import schema
function createDatabase($host, $user, $pass, $dbName) {
    try {
        // Create connection
        $conn = new mysqli($host, $user, $pass);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Create database
        $sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($sql)) {
            throw new Exception("Error creating database: " . $conn->error);
        }
        
        echo "<p style='color: green;'>Database created successfully!</p>";
        
        // Close connection
        $conn->close();
        
        // Import schema from SQL file
        $sqlFile = file_get_contents('database/polyprep.sql');
        
        // Create new connection with database selected
        $conn = new mysqli($host, $user, $pass, $dbName);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Split SQL file into separate queries
        $queries = explode(';', $sqlFile);
        
        // Execute each query
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if (!$conn->query($query . ';')) {
                    echo "<p style='color: orange;'>Warning: " . $conn->error . "</p>";
                }
            }
        }
        
        echo "<p style='color: green;'>Database schema imported successfully!</p>";
        
        // Close connection
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error setting up database: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Function to create admin user
function createAdminUser($host, $user, $pass, $dbName) {
    try {
        // Create connection
        $conn = new mysqli($host, $user, $pass, $dbName);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Check if admin user exists
        $result = $conn->query("SELECT id FROM users WHERE username = 'admin'");
        
        if ($result->num_rows > 0) {
            // Update admin password
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password_hash = '$passwordHash', is_admin = 1 WHERE username = 'admin'";
            
            if ($conn->query($sql)) {
                echo "<p style='color: green;'>Admin user updated successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error updating admin user: " . $conn->error . "</p>";
            }
        } else {
            // Create admin user
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password_hash, is_admin) VALUES ('admin', '$passwordHash', 1)";
            
            if ($conn->query($sql)) {
                echo "<p style='color: green;'>Admin user created successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error creating admin user: " . $conn->error . "</p>";
            }
        }
        
        // Close connection
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error setting up admin user: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Main execution
echo "<h1>Polyprep Database Setup</h1>";

// Check if database exists
if (databaseExists(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
    echo "<p>Database '" . DB_NAME . "' already exists.</p>";
    
    // Ensure admin user exists
    createAdminUser(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} else {
    echo "<p>Database '" . DB_NAME . "' does not exist. Creating...</p>";
    
    // Create database and import schema
    if (createDatabase(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
        // Create admin user
        createAdminUser(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
}

echo "<p>Admin credentials:</p>";
echo "<ul>";
echo "<li>Username: admin</li>";
echo "<li>Password: admin123</li>";
echo "</ul>";

echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>
