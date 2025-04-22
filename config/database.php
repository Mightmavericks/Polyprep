<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection settings for the Polyprep application.
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'polyprep');        // Default XAMPP MySQL username
define('DB_PASS', 'E7a74c6464774@9s');            // Default XAMPP MySQL password (empty)
define('DB_NAME', 'polyprep');    // Database name

/**
 * Get database connection
 * 
 * @return mysqli Database connection object
 */
function getDbConnection() {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        // For API calls, return JSON error
        if (strpos($_SERVER['PHP_SELF'], '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
            exit;
        }
        
        // For regular pages, show error message
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
    return $conn;
}
