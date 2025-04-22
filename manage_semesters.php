<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require admin access
requireAdmin();

// Initialize variables
$error = '';
$success = '';
$semesterName = '';
$selectedCourseId = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an add or delete operation
    if (isset($_POST['add_semester'])) {
        // Add new semester
        $semesterName = trim($_POST['semester_name']);
        $selectedCourseId = (int)$_POST['course_id'];
        
        // Validate input
        if (empty($semesterName) || empty($selectedCourseId)) {
            $error = 'Please enter a semester name and select a course.';
        } else {
            // Connect to database
            $conn = getDbConnection();
            
            // Check if semester already exists for this course
            $sql = "SELECT id FROM semesters WHERE name = ? AND course_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $semesterName, $selectedCourseId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Semester already exists for this course.';
            } else {
                // Insert new semester
                $sql = "INSERT INTO semesters (course_id, name) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $selectedCourseId, $semesterName);
                
                if ($stmt->execute()) {
                    $success = 'Semester added successfully!';
                    $semesterName = ''; // Clear form
                } else {
                    $error = 'Failed to add semester. Please try again.';
                }
            }
            
            $stmt->close();
            $conn->close();
        }
    } elseif (isset($_POST['delete_semester'])) {
        // Delete semester
        $semesterId = (int)$_POST['semester_id'];
        
        if ($semesterId > 0) {
            // Connect to database
            $conn = getDbConnection();
            
            // Delete semester
            $sql = "DELETE FROM semesters WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $semesterId);
            
            if ($stmt->execute()) {
                $success = 'Semester deleted successfully!';
            } else {
                $error = 'Failed to delete semester. Please try again.';
            }
            
            $stmt->close();
            $conn->close();
        } else {
            $error = 'Invalid semester ID.';
        }
    }
}

// Get all courses
$courses = getAllCourses();

// Get all semesters with course names
$conn = getDbConnection();
$sql = "SELECT s.id, s.name, s.created_at, c.name AS course_name, c.id AS course_id 
        FROM semesters s 
        JOIN courses c ON s.course_id = c.id 
        ORDER BY c.name, s.name";
$result = $conn->query($sql);

$semesters = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $semesters[] = $row;
    }
}
$conn->close();

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Manage Semesters</h1>
</div>

<!-- Breadcrumb -->
<?php echo getBreadcrumb(['Home' => 'index.php', 'Admin Dashboard' => 'admin_dashboard.php', 'Manage Semesters' => '']); ?>

<div class="card">
    <div class="card-header">
        <h2>Add New Semester</h2>
    </div>
    <div class="card-body">
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
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="course_id">Course</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo ($selectedCourseId == $course['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester_name">Semester Name</label>
                <input type="text" id="semester_name" name="semester_name" class="form-control" value="<?php echo htmlspecialchars($semesterName); ?>" required>
            </div>
            
            <button type="submit" name="add_semester" class="btn btn-primary">Add Semester</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Existing Semesters</h2>
    </div>
    <div class="card-body">
        <?php if (empty($semesters)): ?>
            <p>No semesters available.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Semester Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semesters as $semester): ?>
                            <tr>
                                <td><?php echo $semester['id']; ?></td>
                                <td><?php echo htmlspecialchars($semester['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($semester['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($semester['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                        <input type="hidden" name="semester_id" value="<?php echo $semester['id']; ?>">
                                        <button type="submit" name="delete_semester" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this semester? This will also delete all associated subjects and notes.')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
