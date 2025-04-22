<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

// Redirect if admin
if (isAdmin()) {
    header('Location: admin_dashboard.php');
    exit;
}

// Get user's notes
$userNotes = getUserNotes($_SESSION['user_id']);

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">User Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
</div>

<!-- Breadcrumb -->
<?php echo getBreadcrumb(['Home' => 'index.php', 'Dashboard' => '']); ?>

<!-- User's Notes Section -->
<div class="card">
    <div class="card-header">
        <h2>My Notes</h2>
    </div>
    <div class="card-body">
        <?php if (empty($userNotes)): ?>
            <p>You haven't uploaded any notes yet.</p>
            <a href="upload.php" class="btn btn-primary">Upload Notes</a>
        <?php else: ?>
            <div class="table-responsive">
                <div class="table-responsive">
                <table class="table responsive-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Subject</th>
                            <th>Upload Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get user's notes
                        $notes = getUserNotes($_SESSION['user_id']);
                        
                        if (count($notes) > 0) {
                            foreach ($notes as $note) {
                                echo '<tr>';
                                echo '<td data-label="Title">' . htmlspecialchars($note['title']) . '</td>';
                                echo '<td data-label="Course">' . htmlspecialchars($note['course_name']) . '</td>';
                                echo '<td data-label="Semester">Semester ' . htmlspecialchars($note['semester_name']) . '</td>';
                                echo '<td data-label="Subject">' . htmlspecialchars($note['subject_name']) . '</td>';
                                echo '<td data-label="Upload Date">' . htmlspecialchars(date('M d, Y', strtotime($note['upload_date']))) . '</td>';
                                echo '<td data-label="Status">' . ($note['is_approved'] ? '<span class="badge badge-success">Approved</span>' : '<span class="badge badge-warning">Pending</span>') . '</td>';
                                echo '<td data-label="Actions"><a href="' . htmlspecialchars($note['file_path']) . '" target="_blank" class="btn btn-sm btn-primary">View</a></td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7">No notes uploaded yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="upload.php" class="btn btn-primary">Upload More Notes</a>
        <?php endif; ?>
    </div>
</div>

<!-- Browse Notes Section -->
<div class="card">
    <div class="card-header">
        <h2>Browse Notes</h2>
    </div>
    <div class="card-body">
        <form id="browse-form" method="GET" action="browse.php">
            <div class="form-group">
                <label for="course-dropdown">Select Course</label>
                <select id="course-dropdown" name="course_id" class="form-control">
                    <option value="">Select Course</option>
                    <?php
                    $courses = getAllCourses();
                    foreach ($courses as $course) {
                        echo '<option value="' . $course['id'] . '">' . htmlspecialchars($course['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester-dropdown">Select Semester</label>
                <select id="semester-dropdown" name="semester_id" class="form-control" disabled>
                    <option value="">Select Semester</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="subject-dropdown">Select Subject</label>
                <select id="subject-dropdown" name="subject_id" class="form-control" disabled>
                    <option value="">Select Subject</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Browse Notes</button>
        </form>
    </div>
</div>

<!-- Recently Approved Notes -->
<div class="card">
    <div class="card-header">
        <h2>Recently Approved Notes</h2>
    </div>
    <div class="card-body">
        <div class="notes-grid">
            <?php
            // This would normally fetch recently approved notes from the database
            // For now, we'll just display placeholder content
            ?>
            <div class="card note-card">
                <div class="note-thumbnail">
                    <img src="assets\images\pdf-icon.png" alt="PDF Icon">
                </div>
                <div class="note-info">
                    <h3 class="note-title">Introduction to Programming</h3>
                    <div class="note-meta">Computer Science • Semester 1</div>
                </div>
                <div class="note-actions">
                    <a href="#" class="btn btn-primary btn-sm">View</a>
                </div>
            </div>
            
            <div class="card note-card">
                <div class="note-thumbnail">
                    <img src="assets\images\pdf-icon.png" alt="PDF Icon">
                </div>
                <div class="note-info">
                    <h3 class="note-title">Data Structures</h3>
                    <div class="note-meta">Computer Science • Semester 2</div>
                </div>
                <div class="note-actions">
                    <a href="#" class="btn btn-primary btn-sm">View</a>
                </div>
            </div>
            
            <div class="card note-card">
                <div class="note-thumbnail">
                    <img src="assets\images\pdf-icon.png" alt="PDF Icon">
                </div>
                <div class="note-info">
                    <h3 class="note-title">Digital Logic Design</h3>
                    <div class="note-meta">Computer Science • Semester 1</div>
                </div>
                <div class="note-actions">
                    <a href="#" class="btn btn-primary btn-sm">View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
