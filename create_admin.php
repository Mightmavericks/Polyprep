<?php
// Include database configuration
require_once 'config/database.php';

// Admin credentials
$username = 'admin';
$password = 'admin123';

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Connect to database
$conn = getDbConnection();

// Check if admin already exists
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Admin exists, update password
    $sql = "UPDATE users SET password_hash = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $password_hash, $username);
    
    if ($stmt->execute()) {
        echo "Admin password updated successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Password Hash: " . $password_hash;
    } else {
        echo "Error updating admin password: " . $conn->error;
    }
} else {
    // Admin doesn't exist, create new admin
    $is_admin = 1;
    $sql = "INSERT INTO users (username, password_hash, is_admin) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $password_hash, $is_admin);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Password Hash: " . $password_hash;
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
}

$stmt->close();
$conn->close();

// Add a link to go back to login page
echo "<br><br><a href='login.php'>Go to Login Page</a>";
?>
