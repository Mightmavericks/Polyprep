<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'config/database.php';

// Redirect if already logged in
redirectIfLoggedIn();

// Initialize variables
$username = '';
$error = '';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Connect to database
        $conn = getDbConnection();
        
        // Prepare SQL statement
        $sql = "SELECT id, username, password_hash, is_admin FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, start a new session
                session_start();
                
                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                
                // Set success message
                $_SESSION['alert'] = 'Login successful. Welcome back, ' . $user['username'] . '!';
                $_SESSION['alert_type'] = 'success';
                
                // Redirect to appropriate dashboard
                if ($user['is_admin']) {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: user_dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Include header
include_once 'includes/header.php';
?>

<div class="auth-container">
    <h2 class="auth-title">Login to Polyprep</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form id="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    
    <p class="mt-3">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
