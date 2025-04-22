<?php
// Set HTTP response code
http_response_code(500);

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">500 - Internal Server Error</h1>
</div>

<div class="card">
    <div class="card-body">
        <p>Something went wrong on our end. Please try again later.</p>
        <a href="index.php" class="btn btn-primary">Go to Homepage</a>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
