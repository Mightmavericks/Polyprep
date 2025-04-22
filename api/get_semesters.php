<?php
// Include centralized config file
require_once 'config.php';

// Verify API request
verifyAPIRequest();

// Check if course_id is provided
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    echo json_encode([]);
    exit;
}

// Get course ID
$courseId = (int)$_GET['course_id'];

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $dbName);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
    // Get semesters for the selected course
    $sql = "SELECT * FROM semesters WHERE course_id = ? ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $semesters = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $semesters[] = $row;
        }
    }
    
    $stmt->close();
    $conn->close();
    
    // Return semesters as JSON
    echo json_encode($semesters);
    
} catch (Exception $e) {
    // Return error with HTTP 500 status
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
