<?php
// Turn off error reporting for this file
error_reporting(0);
ini_set('display_errors', 0);

// Set content type to JSON
header('Content-Type: application/json');

// Direct database connection to avoid any potential errors from includes
$host = 'localhost';
$user = 'polyprep';
$pass = 'E7a74c6464774@9s';
$dbName = 'polyprep';

// Check if semester_id is provided
if (!isset($_GET['semester_id']) || empty($_GET['semester_id'])) {
    echo json_encode([]);
    exit;
}

// Get semester ID
$semesterId = (int)$_GET['semester_id'];

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $dbName);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
    // Get subjects for the selected semester
    $sql = "SELECT * FROM subjects WHERE semester_id = ? ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $semesterId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
    }
    
    $stmt->close();
    $conn->close();
    
    // Return subjects as JSON
    echo json_encode($subjects);
    
} catch (Exception $e) {
    // Return error with HTTP 500 status
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
