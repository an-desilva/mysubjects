<?php

require_once __DIR__ . '/../../config/database.php';

class Attendance {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function markAttendance(int $studentId, int $courseId, string $date, string $status, int $markedBy, ?string $scannedCode = null): array {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO attendance (student_id, course_id, date, status, marked_by, scanned_code)
                VALUES (:student_id, :course_id, :date, :status, :marked_by, :scanned_code)
                ON DUPLICATE KEY UPDATE status = :status_update, marked_by = :marked_by_update, scanned_code = :scanned_code_update
            ");
            $stmt->execute([
                'student_id'        => $studentId,
                'course_id'         => $courseId,
                'date'              => $date,
                'status'            => $status,
                'marked_by'         => $markedBy,
                'scanned_code'      => $scannedCode,
                'status_update'     => $status,
                'marked_by_update'  => $markedBy,
                'scanned_code_update' => $scannedCode
            ]);
            return ['success' => true, 'message' => 'Attendance recorded successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getRecentLogs(int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as student_name, s.student_code, c.title as course_title, m.name as marked_by_name
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN courses c ON a.course_id = c.id
            JOIN users m ON a.marked_by = m.id
            ORDER BY a.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTodayStats(): array {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) as count 
            FROM attendance 
            WHERE date = :today 
            GROUP BY status
        ");
        $stmt->execute(['today' => $today]);
        $rows = $stmt->fetchAll();

        $stats = ['present' => 0, 'late' => 0, 'absent' => 0, 'total' => 0];
        foreach ($rows as $r) {
            $stats[$r['status']] = (int) $r['count'];
            $stats['total'] += (int) $r['count'];
        }
        return $stats;
    }

    public function getStudentAttendance(int $studentId): array {
        $stmt = $this->db->prepare("
            SELECT a.*, c.title as course_title, c.course_code
            FROM attendance a
            JOIN courses c ON a.course_id = c.id
            WHERE a.student_id = :student_id
            ORDER BY a.date DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }
}
