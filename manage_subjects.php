<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require admin access
requireAdmin();

// Initialize variables
$error = '';
$success = '';
$subjectName = '';
$selectedCourseId = '';
$selectedSemesterId = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an add or delete operation
    if (isset($_POST['add_subject'])) {
        // Add new subject
        $subjectName = trim($_POST['subject_name']);
        $selectedSemesterId = (int)$_POST['semester_id'];
        
        // Validate input
        if (empty($subjectName) || empty($selectedSemesterId)) {
            $error = 'Please enter a subject name and select a semester.';
        } else {
            // Connect to database
            $conn = getDbConnection();
            
            // Check if subject already exists for this semester
            $sql = "SELECT id FROM subjects WHERE name = ? AND semester_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $subjectName, $selectedSemesterId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Subject already exists for this semester.';
            } else {
                // Insert new subject
                $sql = "INSERT INTO subjects (semester_id, name) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $selectedSemesterId, $subjectName);
                
                if ($stmt->execute()) {
                    $success = 'Subject added successfully!';
                    $subjectName = ''; // Clear form
                } else {
                    $error = 'Failed to add subject. Please try again.';
                }
            }
            
            $stmt->close();
            $conn->close();
        }
    } elseif (isset($_POST['delete_subject'])) {
        // Delete subject
        $subjectId = (int)$_POST['subject_id'];
        
        if ($subjectId > 0) {
            // Connect to database
            $conn = getDbConnection();
            
            // Delete subject
            $sql = "DELETE FROM subjects WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $subjectId);
            
            if ($stmt->execute()) {
                $success = 'Subject deleted successfully!';
            } else {
                $error = 'Failed to delete subject. Please try again.';
            }
            
            $stmt->close();
            $conn->close();
        } else {
            $error = 'Invalid subject ID.';
        }
    }
}

// Get all courses
$courses = getAllCourses();

// Get semesters for selected course
$semesters = [];
if (!empty($selectedCourseId)) {
    $semesters = getSemestersByCourse($selectedCourseId);
}

// Get all subjects with course and semester names
$conn = getDbConnection();
$sql = "SELECT s.id, s.name, s.created_at, sem.name AS semester_name, c.name AS course_name, 
               sem.id AS semester_id, c.id AS course_id 
        FROM subjects s 
        JOIN semesters sem ON s.semester_id = sem.id 
        JOIN courses c ON sem.course_id = c.id 
        ORDER BY c.name, sem.name, s.name";
$result = $conn->query($sql);

$subjects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}
$conn->close();

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Manage Subjects</h1>
</div>

<!-- Breadcrumb -->
<?php echo getBreadcrumb(['Home' => 'index.php', 'Admin Dashboard' => 'admin_dashboard.php', 'Manage Subjects' => '']); ?>

<div class="card">
    <div class="card-header">
        <h2>Add New Subject</h2>
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
                <label for="course-dropdown">Course</label>
                <select id="course-dropdown" name="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo ($selectedCourseId == $course['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester-dropdown">Semester</label>
                <select id="semester-dropdown" name="semester_id" class="form-control" required>
                    <option value="">Select Semester</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo $semester['id']; ?>" <?php echo ($selectedSemesterId == $semester['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($semester['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="subject_name">Subject Name</label>
                <input type="text" id="subject_name" name="subject_name" class="form-control" value="<?php echo htmlspecialchars($subjectName); ?>" required>
            </div>
            
            <button type="submit" name="add_subject" class="btn btn-primary">Add Subject</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Existing Subjects</h2>
    </div>
    <div class="card-body">
        <?php if (empty($subjects)): ?>
            <p>No subjects available.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Subject Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo $subject['id']; ?></td>
                                <td><?php echo htmlspecialchars($subject['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['semester_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($subject['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                        <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                                        <button type="submit" name="delete_subject" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subject? This will also delete all associated notes.')">Delete</button>
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
