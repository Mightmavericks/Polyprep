<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require admin access
requireAdmin();

// Initialize variables
$error = '';
$success = '';
$courseName = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an add or delete operation
    if (isset($_POST['add_course'])) {
        // Add new course
        $courseName = trim($_POST['course_name']);
        
        // Validate input
        if (empty($courseName)) {
            $error = 'Please enter a course name.';
        } else {
            // Connect to database
            $conn = getDbConnection();
            
            // Check if course already exists
            $sql = "SELECT id FROM courses WHERE name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $courseName);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Course already exists.';
            } else {
                // Insert new course
                $sql = "INSERT INTO courses (name) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $courseName);
                
                if ($stmt->execute()) {
                    $success = 'Course added successfully!';
                    $courseName = ''; // Clear form
                } else {
                    $error = 'Failed to add course. Please try again.';
                }
            }
            
            $stmt->close();
            $conn->close();
        }
    } elseif (isset($_POST['delete_course'])) {
        // Delete course
        $courseId = (int)$_POST['course_id'];
        
        if ($courseId > 0) {
            // Connect to database
            $conn = getDbConnection();
            
            // Delete course
            $sql = "DELETE FROM courses WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $courseId);
            
            if ($stmt->execute()) {
                $success = 'Course deleted successfully!';
            } else {
                $error = 'Failed to delete course. Please try again.';
            }
            
            $stmt->close();
            $conn->close();
        } else {
            $error = 'Invalid course ID.';
        }
    }
}

// Get all courses
$courses = getAllCourses();

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Manage Courses</h1>
</div>

<!-- Breadcrumb -->
<?php echo getBreadcrumb(['Home' => 'index.php', 'Admin Dashboard' => 'admin_dashboard.php', 'Manage Courses' => '']); ?>

<div class="card">
    <div class="card-header">
        <h2>Add New Course</h2>
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
                <label for="course_name">Course Name</label>
                <input type="text" id="course_name" name="course_name" class="form-control" value="<?php echo htmlspecialchars($courseName); ?>" required>
            </div>
            
            <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Existing Courses</h2>
    </div>
    <div class="card-body">
        <?php if (empty($courses)): ?>
            <p>No courses available.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?php echo $course['id']; ?></td>
                                <td><?php echo htmlspecialchars($course['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($course['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" name="delete_course" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course? This will also delete all associated semesters, subjects, and notes.')">Delete</button>
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
