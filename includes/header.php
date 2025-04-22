<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polyprep - Educational Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Add favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="navbar-brand">Polyprep</a>
            <button class="navbar-toggle" id="navbar-toggle" aria-label="Toggle navigation">
                <span class="navbar-toggle-icon"></span>
            </button>
            <div class="navbar-collapse" id="navbar-collapse">
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a href="<?php echo $isAdmin ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="nav-link">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="upload.php" class="nav-link">Upload Notes</a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Logout (<?php echo htmlspecialchars($username); ?>)</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="login.php" class="nav-link">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="register.php" class="nav-link">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    
    <div class="container">
        <!-- Alerts container for JavaScript notifications -->
        <div id="alerts-container"></div>
        
        <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert_type']; ?>">
                <?php echo $_SESSION['alert']; ?>
            </div>
            <?php 
            // Clear the alert after displaying
            unset($_SESSION['alert']);
            unset($_SESSION['alert_type']);
            ?>
        <?php endif; ?>
