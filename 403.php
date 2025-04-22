<?php
// Set HTTP response code
http_response_code(403);

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">403 - Forbidden</h1>
</div>

<div class="card">
    <div class="card-body">
        <p>You don't have permission to access this resource.</p>
        <a href="index.php" class="btn btn-primary">Go to Homepage</a>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
