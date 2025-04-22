<?php
// Turn off error reporting for API endpoints
error_reporting(0);
ini_set('display_errors', 0);

// Set content type to JSON
header('Content-Type: application/json');

// Database credentials
$host = 'localhost';
$user = 'polyprep';
$pass = 'E7a74c6464774@9s';
$dbName = 'polyprep';

// API security functions
function verifyAPIRequest() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    
    // Add CSRF protection
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }
    }
    
    return true;
}

// Log errors to file instead of displaying them
function logAPIError($message, $context = []) {
    $logDir = __DIR__ . '/../logs';
    
    // Create logs directory if it doesn't exist
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/api_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
    
    error_log("[{$timestamp}] {$message}{$contextStr}\n", 3, $logFile);
}
?>
