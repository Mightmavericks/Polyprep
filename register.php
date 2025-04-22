<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'config/database.php';

// Redirect if already logged in
redirectIfLoggedIn();

// Initialize variables
$username = '';
$error = '';

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all fields.';
    } elseif (!isValidUsername($username)) {
        $error = 'Username must be 3-20 characters and can only contain letters, numbers, and underscores.';
    } elseif (!isStrongPassword($password)) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Connect to database
        $conn = getDbConnection();
        
        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Username already exists. Please choose a different one.';
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $passwordHash);
            
            if ($stmt->execute()) {
                // Registration successful
                $_SESSION['alert'] = 'Registration successful! You can now log in.';
                $_SESSION['alert_type'] = 'success';
                header('Location: login.php');
                exit;
            } else {
                $error = 'Registration failed. Please try again later.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Include header
include_once 'includes/header.php';
?>

<div class="auth-container">
    <h2 class="auth-title">Register for Polyprep</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
            <small class="form-text text-muted">3-20 characters, letters, numbers, and underscores only.</small>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <small class="form-text text-muted">At least 8 characters long.</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    
    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
