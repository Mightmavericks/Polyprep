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

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if note_id and action are provided
if (!isset($_POST['note_id']) || empty($_POST['note_id']) || !isset($_POST['action']) || empty($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Get note ID and action
$noteId = (int)$_POST['note_id'];
$action = $_POST['action'];

// Validate action
if ($action !== 'approve' && $action !== 'reject') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $dbName);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
    // Check if user is admin
    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit;
    }
    
    if ($action === 'approve') {
        // Approve the note
        $sql = "UPDATE notes SET is_approved = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $noteId);
        $success = $stmt->execute();
        $stmt->close();
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Note approved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve note: ' . $conn->error]);
        }
    } else {
        // Get file path before deleting
        $stmt = $conn->prepare("SELECT file_path FROM notes WHERE id = ?");
        $stmt->bind_param("i", $noteId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $filePath = $row['file_path'];
            
            // Delete the file if it exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $stmt->close();
        
        // Delete the note from database
        $sql = "DELETE FROM notes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $noteId);
        $success = $stmt->execute();
        $stmt->close();
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Note rejected successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reject note: ' . $conn->error]);
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    // Return error with HTTP 500 status
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
