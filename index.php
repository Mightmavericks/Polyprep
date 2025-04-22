<?php
// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Welcome to Polyprep</h1>
    <p>A mobile-first educational platform for sharing and accessing course materials.</p>
</div>

<div class="card">
    <div class="card-body">
        <h2>About Polyprep</h2>
        <p>Polyprep is an educational website designed to help students access and share course notes and materials. Our platform allows users to:</p>
        
        <ul>
            <li>Browse notes organized by course, semester, and subject</li>
            <li>Upload and share your own notes with others</li>
            <li>Access materials from any device with our mobile-friendly interface</li>
        </ul>
        
        <p>To get started, please <a href="login.php">login</a> or <a href="register.php">register</a> for an account.</p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo $_SESSION['is_admin'] ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="btn btn-primary">Go to Dashboard</a>
        <?php else: ?>
            <div>
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Featured Notes</h2>
    </div>
    <div class="card-body">
        <p>Our platform hosts a wide variety of educational materials across different courses and subjects.</p>
        
        <div class="notes-grid">
            <!-- These would normally be populated from the database -->
            <div class="card note-card">
                <div class="note-thumbnail">
                    <img src="assets/images/pdf-icon.png" alt="PDF Icon">
                </div>
                <div class="note-info">
                    <h3 class="note-title">Introduction to Programming</h3>
                    <div class="note-meta">Computer Science • Semester 1</div>
                </div>
            </div>
            
            <div class="card note-card">
                <div class="note-thumbnail">
                    <img src="assets/images/pdf-icon.png" alt="PDF Icon">
                </div>
                <div class="note-info">
                    <h3 class="note-title">Data Structures</h3>
                    <div class="note-meta">Computer Science • Semester 2</div>
                </div>
            </div>
            
            <div class="card note-card">
                <div class="note-thumbnail">
                    <img src="assets/images/pdf-icon.png" alt="PDF Icon">
                </div>
                <div class="note-info">
                    <h3 class="note-title">Digital Logic Design</h3>
                    <div class="note-meta">Computer Science • Semester 1</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
