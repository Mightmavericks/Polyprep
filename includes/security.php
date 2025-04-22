<?php
/**
 * Security Helper Functions
 */

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Add CSRF token to forms
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Verify CSRF token
function verifyCSRFToken() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        setAlert('Invalid CSRF token. Please try again.', 'danger');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    return true;
}

// Sanitize output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Rate limiting for login attempts
function checkLoginRateLimit($username) {
    // Initialize session storage for rate limiting if not exists
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    // Clean up old attempts (older than 1 hour)
    $now = time();
    foreach ($_SESSION['login_attempts'] as $user => $attempts) {
        foreach ($attempts as $index => $timestamp) {
            if ($now - $timestamp > 3600) {
                unset($_SESSION['login_attempts'][$user][$index]);
            }
        }
        
        // Remove user if no attempts left
        if (empty($_SESSION['login_attempts'][$user])) {
            unset($_SESSION['login_attempts'][$user]);
        }
    }
    
    // Check if user has too many attempts
    if (isset($_SESSION['login_attempts'][$username]) && count($_SESSION['login_attempts'][$username]) >= 5) {
        return false; // Too many attempts
    }
    
    return true; // Rate limit not exceeded
}

// Record failed login attempt
function recordFailedLoginAttempt($username) {
    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = [];
    }
    
    $_SESSION['login_attempts'][$username][] = time();
}

// Set secure headers
function setSecureHeaders() {
    // X-XSS-Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // X-Content-Type-Options
    header('X-Content-Type-Options: nosniff');
    
    // X-Frame-Options
    header('X-Frame-Options: SAMEORIGIN');
    
    // Content-Security-Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");
    
    // Strict-Transport-Security (only in production with HTTPS)
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    
    // Referrer-Policy
    header('Referrer-Policy: same-origin');
    
    // Feature-Policy
    header("Feature-Policy: geolocation 'none'; microphone 'none'; camera 'none'");
}

// Log security events
function logSecurityEvent($event, $details = []) {
    $logDir = __DIR__ . '/../logs';
    
    // Create logs directory if it doesn't exist
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $user = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
    $detailsStr = !empty($details) ? ' | ' . json_encode($details) : '';
    
    error_log("[{$timestamp}] [{$ip}] [{$user}] {$event}{$detailsStr}\n", 3, $logFile);
}

// Validate file upload
function validateFileUpload($file, $allowedTypes = ['application/pdf'], $maxSize = 10485760) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'message' => 'File upload failed.'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['valid' => false, 'message' => 'File size exceeds the limit.'];
    }
    
    // Check file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileType = $finfo->file($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['valid' => false, 'message' => 'Invalid file type.'];
    }
    
    // Additional check for PDF files
    if (in_array('application/pdf', $allowedTypes)) {
        // Check if file is actually a PDF
        $f = fopen($file['tmp_name'], 'rb');
        $header = fread($f, 4);
        fclose($f);
        
        if ($header !== '%PDF') {
            return ['valid' => false, 'message' => 'Invalid PDF file.'];
        }
    }
    
    return ['valid' => true, 'message' => 'File is valid.'];
}
?>
