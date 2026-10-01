<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Student.php';

class AuthController {
    private User $userModel;
    private Student $studentModel;

    public function __construct() {
        $this->userModel = new User();
        $this->studentModel = new Student();
    }

    public function showLogin(): void {
        if (is_logged_in()) {
            $this->redirectByRole(auth_user()['role']);
        }
        view('auth/login');
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        if (!verify_csrf()) {
            set_flash('error', 'Invalid security token.');
            redirect('/login');
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            set_flash('error', 'Please enter both email and password.');
            redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            set_flash('error', 'Invalid email or password.');
            redirect('/login');
        }

        // Fetch student_id if user is a student
        $studentId = null;
        if ($user['role'] === 'student') {
            $student = $this->studentModel->findByUserId($user['id']);
            $studentId = $student ? $student['id'] : null;
        }

        // Set session
        $_SESSION['user'] = [
            'id'         => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'student_id' => $studentId
        ];

        set_flash('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');
        $this->redirectByRole($user['role']);
    }

    public function logout(): void {
        unset($_SESSION['user']);
        session_destroy();
        session_start();
        set_flash('success', 'You have been logged out successfully.');
        redirect('/login');
    }

    private function redirectByRole(string $role): void {
        switch ($role) {
            case 'admin':
                redirect('/admin/dashboard');
                break;
            case 'teacher':
                redirect('/teacher/materials');
                break;
            case 'student':
                redirect('/student/materials');
                break;
            default:
                redirect('/login');
        }
    }
}
