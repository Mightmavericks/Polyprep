<?php
/**
 * Authentication Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if current user is admin
 * 
 * @return bool True if user is admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Redirect to login page if user is not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['alert'] = 'Please log in to access this page.';
        $_SESSION['alert_type'] = 'warning';
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirect to user dashboard if user is not admin
 */
function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        $_SESSION['alert'] = 'You do not have permission to access this page.';
        $_SESSION['alert_type'] = 'danger';
        header('Location: user_dashboard.php');
        exit;
    }
}

/**
 * Redirect to dashboard if user is already logged in
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: user_dashboard.php');
        }
        exit;
    }
}

/**
 * Set a flash message to be displayed on the next page
 * 
 * @param string $message The message to display
 * @param string $type The type of alert (success, danger, warning, info)
 */
function setAlert($message, $type = 'info') {
    $_SESSION['alert'] = $message;
    $_SESSION['alert_type'] = $type;
}

/**
 * Sanitize user input to prevent XSS attacks
 * 
 * @param string $input The input to sanitize
 * @return string The sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate username
 * 
 * @param string $username The username to validate
 * @return bool True if username is valid, false otherwise
 */
function isValidUsername($username) {
    // Username must be 3-20 characters and contain only alphanumeric characters and underscores
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

/**
 * Validate password strength
 * 
 * @param string $password The password to validate
 * @return bool True if password is strong enough, false otherwise
 */
function isStrongPassword($password) {
    // Password must be at least 8 characters
    return strlen($password) >= 8;
}
