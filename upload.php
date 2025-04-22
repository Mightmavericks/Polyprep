<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

// Initialize variables
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $semesterId = isset($_POST['semester_id']) ? (int)$_POST['semester_id'] : 0;
    $subjectId = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
    $title = trim($_POST['title']);
    
    // Validate input
    if (empty($courseId) || empty($semesterId) || empty($subjectId) || empty($title)) {
        $error = 'Please fill in all required fields.';
    } elseif (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please select a PDF file to upload.';
    } else {
        // Get file information
        $file = $_FILES['pdf_file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        
        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Check if file is a PDF
        if ($fileExt !== 'pdf') {
            $error = 'Only PDF files are allowed.';
        } 
        // Check file size (max 10MB)
        elseif ($fileSize > 10 * 1024 * 1024) {
            $error = 'File size exceeds the 10MB limit.';
        } else {
            // Generate unique filename
            $newFileName = generateUniqueFilename($fileName, $_SESSION['user_id']);
            
            // Create upload directory path
            $uploadDir = 'uploads/' . $courseId . '/' . $semesterId . '/' . $subjectId . '/';
            
            // Create directory if it doesn't exist
            if (!createDirectoryIfNotExists($uploadDir)) {
                $error = 'Failed to create upload directory.';
            } else {
                // Set full file path
                $filePath = $uploadDir . $newFileName;
                
                // Move uploaded file to destination
                if (move_uploaded_file($fileTmpName, $filePath)) {
                    // Connect to database
                    $conn = getDbConnection();
                    
                    // Insert note information into database
                    $sql = "INSERT INTO notes (subject_id, user_id, title, filename, file_path, file_size) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iisssi", $subjectId, $_SESSION['user_id'], $title, $newFileName, $filePath, $fileSize);
                    
                    if ($stmt->execute()) {
                        $success = 'Note uploaded successfully! It will be available after admin approval.';
                    } else {
                        $error = 'Failed to upload note. Please try again.';
                        
                        // Remove uploaded file if database insertion fails
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                    
                    $stmt->close();
                    $conn->close();
                } else {
                    $error = 'Failed to upload file. Please try again.';
                }
            }
        }
    }
}

// Get all courses
$courses = getAllCourses();

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Upload Notes</h1>
    <p>Share your notes with other students.</p>
</div>

<!-- Breadcrumb -->
<?php echo getBreadcrumb(['Home' => 'index.php', 'Dashboard' => isAdmin() ? 'admin_dashboard.php' : 'user_dashboard.php', 'Upload Notes' => '']); ?>

<div class="upload-container">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <form id="upload-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course-dropdown">Course *</label>
            <select id="course-dropdown" name="course_id" class="form-control" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="semester-dropdown">Semester *</label>
            <select id="semester-dropdown" name="semester_id" class="form-control" required>
                <option value="">Select Semester</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="subject-dropdown">Subject *</label>
            <select id="subject-dropdown" name="subject_id" class="form-control" required>
                <option value="">Select Subject</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="pdf-file">PDF File *</label>
            <div class="file-input-wrapper">
                <input type="file" id="pdf-file" name="pdf_file" class="file-input" accept=".pdf" required>
                <label class="file-input-label" for="pdf-file">Choose PDF file...</label>
            </div>
            <small class="form-text text-muted">Maximum file size: 10MB. Only PDF files are allowed.</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Upload Note</button>
    </form>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
