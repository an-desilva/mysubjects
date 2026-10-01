<?php

require_once __DIR__ . '/../../config/database.php';

class Material {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO materials (course_id, uploaded_by, title, description, file_path, file_size, type)
            VALUES (:course_id, :uploaded_by, :title, :description, :file_path, :file_size, :type)
        ");
        $stmt->execute([
            'course_id'   => $data['course_id'],
            'uploaded_by' => $data['uploaded_by'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? '',
            'file_path'   => $data['file_path'],
            'file_size'   => $data['file_size'] ?? 0,
            'type'        => $data['type'] ?? 'pdf'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array {
        $stmt = $this->db->prepare("
            SELECT m.*, c.title as course_title, c.course_code, u.name as uploader_name
            FROM materials m
            JOIN courses c ON m.course_id = c.id
            JOIN users u ON m.uploaded_by = u.id
            ORDER BY m.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByTeacher(int $teacherId): array {
        $stmt = $this->db->prepare("
            SELECT m.*, c.title as course_title, c.course_code
            FROM materials m
            JOIN courses c ON m.course_id = c.id
            WHERE m.uploaded_by = :teacher_id
            ORDER BY m.created_at DESC
        ");
        $stmt->execute(['teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }

    public function getForStudent(int $studentId): array {
        $stmt = $this->db->prepare("
            SELECT m.*, c.title as course_title, c.course_code, u.name as teacher_name
            FROM materials m
            JOIN courses c ON m.course_id = c.id
            JOIN course_enrollments ce ON c.id = ce.course_id
            JOIN users u ON m.uploaded_by = u.id
            WHERE ce.student_id = :student_id
            ORDER BY m.created_at DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT m.*, c.title as course_title
            FROM materials m
            JOIN courses c ON m.course_id = c.id
            WHERE m.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
