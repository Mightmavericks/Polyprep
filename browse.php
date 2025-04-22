<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

// Get query parameters
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$semesterId = isset($_GET['semester_id']) ? (int)$_GET['semester_id'] : 0;
$subjectId = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// Initialize variables
$courseName = '';
$semesterName = '';
$subjectName = '';
$notes = [];

// Get all courses
$courses = getAllCourses();

// If course is selected, get semesters
$semesters = [];
if ($courseId > 0) {
    $semesters = getSemestersByCourse($courseId);
    
    // Get course name
    foreach ($courses as $course) {
        if ($course['id'] == $courseId) {
            $courseName = $course['name'];
            break;
        }
    }
}

// If semester is selected, get subjects
$subjects = [];
if ($semesterId > 0) {
    $subjects = getSubjectsBySemester($semesterId);
    
    // Get semester name
    foreach ($semesters as $semester) {
        if ($semester['id'] == $semesterId) {
            $semesterName = $semester['name'];
            break;
        }
    }
}

// If subject is selected, get notes
if ($subjectId > 0) {
    $notes = getNotesBySubject($subjectId, true);
    
    // Get subject name
    foreach ($subjects as $subject) {
        if ($subject['id'] == $subjectId) {
            $subjectName = $subject['name'];
            break;
        }
    }
}

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Browse Notes</h1>
    <?php if (!empty($courseName)): ?>
        <p>
            <?php echo htmlspecialchars($courseName); ?>
            <?php if (!empty($semesterName)): ?>
                &raquo; <?php echo htmlspecialchars($semesterName); ?>
                <?php if (!empty($subjectName)): ?>
                    &raquo; <?php echo htmlspecialchars($subjectName); ?>
                <?php endif; ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>
</div>

<!-- Breadcrumb -->
<?php
$breadcrumbItems = ['Home' => 'index.php', 'Dashboard' => isAdmin() ? 'admin_dashboard.php' : 'user_dashboard.php', 'Browse Notes' => ''];
echo getBreadcrumb($breadcrumbItems);
?>

<!-- Filter Form -->
<div class="card">
    <div class="card-header">
        <h2>Filter Notes</h2>
    </div>
    <div class="card-body">
        <form id="browse-form" method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="course-dropdown">Select Course</label>
                <select id="course-dropdown" name="course_id" class="form-control">
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo ($courseId == $course['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester-dropdown">Select Semester</label>
                <select id="semester-dropdown" name="semester_id" class="form-control" <?php echo empty($courseId) ? 'disabled' : ''; ?>>
                    <option value="">Select Semester</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo $semester['id']; ?>" <?php echo ($semesterId == $semester['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($semester['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="subject-dropdown">Select Subject</label>
                <select id="subject-dropdown" name="subject_id" class="form-control" <?php echo empty($semesterId) ? 'disabled' : ''; ?>>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>" <?php echo ($subjectId == $subject['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
</div>

<!-- Notes Display -->
<div class="card">
    <div class="card-header">
        <h2>
            <?php
            if (!empty($subjectName)) {
                echo htmlspecialchars($subjectName) . ' Notes';
            } elseif (!empty($semesterName)) {
                echo htmlspecialchars($semesterName) . ' Notes';
            } elseif (!empty($courseName)) {
                echo htmlspecialchars($courseName) . ' Notes';
            } else {
                echo 'All Notes';
            }
            ?>
        </h2>
    </div>
    <div class="card-body">
        <?php if (empty($notes) && $subjectId > 0): ?>
            <p>No notes available for this subject yet.</p>
        <?php elseif (empty($subjectId)): ?>
            <p>Please select a subject to view notes.</p>
        <?php else: ?>
            <div class="notes-grid">
                <?php foreach ($notes as $note): ?>
                    <div class="card note-card">
                        <div class="note-thumbnail" data-pdf-url="<?php echo htmlspecialchars($note['file_path']); ?>">
                            <img src="assets/images/pdf-icon.png" alt="PDF Icon">
                        </div>
                        <div class="note-info">
                            <h3 class="note-title"><?php echo htmlspecialchars($note['title']); ?></h3>
                            <div class="note-meta">
                                Uploaded by: <?php echo htmlspecialchars($note['username']); ?><br>
                                Date: <?php echo date('M d, Y', strtotime($note['upload_date'])); ?>
                            </div>
                        </div>
                        <div class="note-actions">
                            <a href="<?php echo htmlspecialchars($note['file_path']); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
                            <a href="<?php echo htmlspecialchars($note['file_path']); ?>" download class="btn btn-primary btn-sm">Download</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
