<?php
/**
 * Common Functions
 */

require_once 'config/database.php';

/**
 * Get all courses from database
 * 
 * @return array Array of course objects
 */
function getAllCourses() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM courses ORDER BY name";
    $result = $conn->query($sql);
    
    $courses = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
    
    $conn->close();
    return $courses;
}

/**
 * Get semesters for a specific course
 * 
 * @param int $courseId Course ID
 * @return array Array of semester objects
 */
function getSemestersByCourse($courseId) {
    $conn = getDbConnection();
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
    return $semesters;
}

/**
 * Get subjects for a specific semester
 * 
 * @param int $semesterId Semester ID
 * @return array Array of subject objects
 */
function getSubjectsBySemester($semesterId) {
    $conn = getDbConnection();
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
    return $subjects;
}

/**
 * Get notes for a specific subject
 * 
 * @param int $subjectId Subject ID
 * @param bool $approvedOnly Whether to return only approved notes
 * @param int $userId Optional user ID to filter notes by user
 * @return array Array of note objects
 */
function getNotesBySubject($subjectId, $approvedOnly = true, $userId = null) {
    $conn = getDbConnection();
    
    $sql = "SELECT n.*, u.username FROM notes n 
            JOIN users u ON n.user_id = u.id 
            WHERE n.subject_id = ?";
    
    if ($approvedOnly) {
        $sql .= " AND n.is_approved = 1";
    }
    
    if ($userId !== null) {
        $sql .= " AND n.user_id = ?";
    }
    
    $sql .= " ORDER BY n.upload_date DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($userId !== null) {
        $stmt->bind_param("ii", $subjectId, $userId);
    } else {
        $stmt->bind_param("i", $subjectId);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
    }
    
    $stmt->close();
    $conn->close();
    return $notes;
}

/**
 * Get all pending notes (for admin)
 * 
 * @return array Array of pending note objects
 */
function getPendingNotes() {
    $conn = getDbConnection();
    
    $sql = "SELECT n.*, u.username, s.name as subject_name, sem.name as semester_name, c.name as course_name 
            FROM notes n 
            JOIN users u ON n.user_id = u.id 
            JOIN subjects s ON n.subject_id = s.id 
            JOIN semesters sem ON s.semester_id = sem.id 
            JOIN courses c ON sem.course_id = c.id 
            WHERE n.is_approved = 0 
            ORDER BY n.upload_date ASC";
    
    $result = $conn->query($sql);
    
    $notes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
    }
    
    $conn->close();
    return $notes;
}

/**
 * Get user's notes
 * 
 * @param int $userId User ID
 * @return array Array of note objects
 */
function getUserNotes($userId) {
    $conn = getDbConnection();
    
    $sql = "SELECT n.*, s.name as subject_name, sem.name as semester_name, c.name as course_name 
            FROM notes n 
            JOIN subjects s ON n.subject_id = s.id 
            JOIN semesters sem ON s.semester_id = sem.id 
            JOIN courses c ON sem.course_id = c.id 
            WHERE n.user_id = ? 
            ORDER BY n.upload_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
    }
    
    $stmt->close();
    $conn->close();
    return $notes;
}

/**
 * Get total number of users
 * 
 * @return int Total number of users
 */
function getTotalUsers() {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    
    return $row['total'];
}

/**
 * Get total number of notes
 * 
 * @param bool $approvedOnly Whether to count only approved notes
 * @return int Total number of notes
 */
function getTotalNotes($approvedOnly = false) {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as total FROM notes";
    
    if ($approvedOnly) {
        $sql .= " WHERE is_approved = 1";
    }
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    
    return $row['total'];
}

/**
 * Get total number of pending notes
 * 
 * @return int Total number of pending notes
 */
function getTotalPendingNotes() {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as total FROM notes WHERE is_approved = 0";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    
    return $row['total'];
}

/**
 * Approve or reject a note
 * 
 * @param int $noteId Note ID
 * @param bool $approve Whether to approve (true) or reject (false)
 * @return bool True on success, false on failure
 */
function approveRejectNote($noteId, $approve = true) {
    $conn = getDbConnection();
    
    if ($approve) {
        // Approve the note
        $sql = "UPDATE notes SET is_approved = 1 WHERE id = ?";
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
        
        // Delete the note from database
        $sql = "DELETE FROM notes WHERE id = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $noteId);
    $success = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $success;
}

/**
 * Generate a unique filename for uploaded files
 * 
 * @param string $originalFilename Original filename
 * @param int $userId User ID
 * @return string Unique filename
 */
function generateUniqueFilename($originalFilename, $userId) {
    $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
    $timestamp = time();
    $uniqueId = uniqid();
    
    return $timestamp . '_' . $userId . '_' . $uniqueId . '.' . $extension;
}

/**
 * Create directory if it doesn't exist
 * 
 * @param string $path Directory path
 * @return bool True if directory exists or was created successfully
 */
function createDirectoryIfNotExists($path) {
    if (!file_exists($path)) {
        return mkdir($path, 0755, true);
    }
    return true;
}

/**
 * Get breadcrumb trail for current page
 * 
 * @param array $items Array of breadcrumb items [label => url]
 * @return string HTML for breadcrumb navigation
 */
function getBreadcrumb($items) {
    $html = '<nav aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb">';
    
    $count = count($items);
    $i = 0;
    
    foreach ($items as $label => $url) {
        $i++;
        if ($i === $count) {
            // Last item (active)
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($label) . '</li>';
        } else {
            // Not last item
            $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($label) . '</a></li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    return $html;
}
