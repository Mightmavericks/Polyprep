<?php
// Set HTTP response code
http_response_code(404);

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">404 - Page Not Found</h1>
</div>

<div class="card">
    <div class="card-body">
        <p>The page you are looking for does not exist or has been moved.</p>
        <a href="index.php" class="btn btn-primary">Go to Homepage</a>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
