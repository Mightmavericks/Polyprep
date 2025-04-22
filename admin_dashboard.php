<?php
// Include necessary files
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require admin access
requireAdmin();

// Get statistics
$totalUsers = getTotalUsers();
$totalNotes = getTotalNotes();
$totalApprovedNotes = getTotalNotes(true);
$totalPendingNotes = getTotalPendingNotes();

// Get pending notes
$pendingNotes = getPendingNotes();

// Include header
include_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
</div>

<!-- Breadcrumb -->
<?php echo getBreadcrumb(['Home' => 'index.php', 'Admin Dashboard' => '']); ?>

<!-- Statistics Section -->
<div class="card">
    <div class="card-header">
        <h2>Statistics</h2>
    </div>
    <div class="card-body">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalNotes; ?></div>
                <div class="stat-label">Total Notes</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalApprovedNotes; ?></div>
                <div class="stat-label">Approved Notes</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalPendingNotes; ?></div>
                <div class="stat-label">Pending Notes</div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Notes Section -->
<div class="card">
    <div class="card-header">
        <h2>Pending Notes</h2>
    </div>
    <div class="card-body">
        <?php if (empty($pendingNotes)): ?>
            <p>No pending notes to approve.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Uploaded By</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Subject</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingNotes as $note): ?>
                            <tr id="note-<?php echo $note['id']; ?>">
                                <td><?php echo htmlspecialchars($note['title']); ?></td>
                                <td><?php echo htmlspecialchars($note['username']); ?></td>
                                <td><?php echo htmlspecialchars($note['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($note['semester_name']); ?></td>
                                <td><?php echo htmlspecialchars($note['subject_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($note['upload_date'])); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($note['file_path']); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
                                    <button class="btn btn-success btn-sm approve-note" data-note-id="<?php echo $note['id']; ?>">Approve</button>
                                    <button class="btn btn-danger btn-sm reject-note" data-note-id="<?php echo $note['id']; ?>">Reject</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Manage Content Section -->
<div class="card">
    <div class="card-header">
        <h2>Manage Content</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <h3>Courses</h3>
                <a href="manage_courses.php" class="btn btn-primary">Manage Courses</a>
            </div>
            
            <div class="col">
                <h3>Semesters</h3>
                <a href="manage_semesters.php" class="btn btn-primary">Manage Semesters</a>
            </div>
            
            <div class="col">
                <h3>Subjects</h3>
                <a href="manage_subjects.php" class="btn btn-primary">Manage Subjects</a>
            </div>
        </div>
    </div>
</div>

<!-- Initialize approval functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    handleNoteApproval();
});
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>
