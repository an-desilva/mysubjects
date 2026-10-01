<?php

require_once __DIR__ . '/../../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (:name, :email, :password, :role)
        ");
        $stmt->execute([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => $data['role'] ?? 'student',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getTeachers(): array {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE role = 'teacher' ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByRole(string $role): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = :role");
        $stmt->execute(['role' => $role]);
        return (int) $stmt->fetchColumn();
    }
}
