<?php

require_once __DIR__ . '/../../config/database.php';

class Course {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as teacher_name,
            (SELECT COUNT(*) FROM course_enrollments ce WHERE ce.course_id = c.id) as enrolled_count
            FROM courses c
            JOIN users u ON c.teacher_id = u.id
            ORDER BY c.title ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByTeacher(int $teacherId): array {
        $stmt = $this->db->prepare("
            SELECT c.*,
            (SELECT COUNT(*) FROM course_enrollments ce WHERE ce.course_id = c.id) as enrolled_count
            FROM courses c
            WHERE c.teacher_id = :teacher_id
            ORDER BY c.title ASC
        ");
        $stmt->execute(['teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as teacher_name
            FROM courses c
            JOIN users u ON c.teacher_id = u.id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO courses (teacher_id, course_code, title, description, monthly_fee, schedule_day, schedule_time)
            VALUES (:teacher_id, :course_code, :title, :description, :monthly_fee, :schedule_day, :schedule_time)
        ");
        $stmt->execute([
            'teacher_id'    => $data['teacher_id'],
            'course_code'   => $data['course_code'],
            'title'         => $data['title'],
            'description'   => $data['description'] ?? '',
            'monthly_fee'   => $data['monthly_fee'],
            'schedule_day'  => $data['schedule_day'],
            'schedule_time' => $data['schedule_time']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function countTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM courses");
        return (int) $stmt->fetchColumn();
    }
}
