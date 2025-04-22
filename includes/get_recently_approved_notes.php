<?php
/**
 * Get recently approved notes for a user
 *
 * @param int $userId User ID
 * @param int $limit Number of notes to fetch (default 3)
 * @return array Array of note objects
 */
function getRecentlyApprovedNotes($userId, $limit = 3) {
    $conn = getDbConnection();
    $sql = "SELECT n.*, s.name as subject_name, sem.name as semester_name, c.name as course_name
            FROM notes n
            JOIN subjects s ON n.subject_id = s.id
            JOIN semesters sem ON s.semester_id = sem.id
            JOIN courses c ON sem.course_id = c.id
            WHERE n.user_id = ? AND n.is_approved = 1
            ORDER BY n.upload_date DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $notes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
    }
    $stmt->close();
    $conn->close();
    return $notes;
}
