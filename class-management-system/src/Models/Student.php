<?php

require_once __DIR__ . '/../../config/database.php';

class Student {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name, u.email
            FROM students s
            JOIN users u ON s.user_id = u.id
            ORDER BY s.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name, u.email
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function findByStudentCode(string $code): ?array {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name, u.email
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.student_code = :code
            LIMIT 1
        ");
        $stmt->execute(['code' => trim($code)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name, u.email
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO students (user_id, student_code, phone, parent_phone, address, grade_level, barcode_path)
            VALUES (:user_id, :student_code, :phone, :parent_phone, :address, :grade_level, :barcode_path)
        ");
        $stmt->execute([
            'user_id'      => $data['user_id'],
            'student_code' => $data['student_code'],
            'phone'        => $data['phone'] ?? null,
            'parent_phone' => $data['parent_phone'] ?? null,
            'address'      => $data['address'] ?? null,
            'grade_level'  => $data['grade_level'],
            'barcode_path' => $data['barcode_path'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function generateUniqueCode(): string {
        do {
            $code = 'STU-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM students WHERE student_code = :code");
            $stmt->execute(['code' => $code]);
        } while ($stmt->fetchColumn() > 0);
        return $code;
    }

    public function enrollCourse(int $studentId, int $courseId): bool {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO course_enrollments (student_id, course_id)
            VALUES (:student_id, :course_id)
        ");
        return $stmt->execute([
            'student_id' => $studentId,
            'course_id'  => $courseId
        ]);
    }

    public function getEnrolledCourses(int $studentId): array {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as teacher_name
            FROM courses c
            JOIN course_enrollments ce ON c.id = ce.course_id
            JOIN users u ON c.teacher_id = u.id
            WHERE ce.student_id = :student_id
            ORDER BY c.title ASC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function countTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM students");
        return (int) $stmt->fetchColumn();
    }
}
