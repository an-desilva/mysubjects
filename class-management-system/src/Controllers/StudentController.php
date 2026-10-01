<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Student.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Course.php';

class StudentController {
    private Student $studentModel;
    private User $userModel;
    private Course $courseModel;

    public function __construct() {
        RoleMiddleware::handle('admin');
        $this->studentModel = new Student();
        $this->userModel = new User();
        $this->courseModel = new Course();
    }

    public function index(): void {
        $students = $this->studentModel->getAll();
        $courses = $this->courseModel->getAll();
        
        // Attach enrolled courses to each student
        foreach ($students as &$st) {
            $st['enrolled_courses'] = $this->studentModel->getEnrolledCourses($st['id']);
        }

        view('admin/students', [
            'students' => $students,
            'courses'  => $courses
        ]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/students');
        }

        if (!verify_csrf()) {
            set_flash('error', 'Invalid CSRF token.');
            redirect('/admin/students');
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? 'password123';
        $gradeLevel = sanitize($_POST['grade_level'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $parentPhone = sanitize($_POST['parent_phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $courseIds = $_POST['course_ids'] ?? [];

        if (empty($name) || empty($email) || empty($gradeLevel)) {
            set_flash('error', 'Name, Email, and Grade Level are required fields.');
            redirect('/admin/students');
        }

        if ($this->userModel->findByEmail($email)) {
            set_flash('error', 'A user with this email already exists.');
            redirect('/admin/students');
        }

        // 1. Create User account
        $userId = $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => 'student'
        ]);

        // 2. Generate Unique Student Code
        $studentCode = $this->studentModel->generateUniqueCode();

        // 3. Create Student profile
        $studentId = $this->studentModel->create([
            'user_id'      => $userId,
            'student_code' => $studentCode,
            'phone'        => $phone,
            'parent_phone' => $parentPhone,
            'address'      => $address,
            'grade_level'  => $gradeLevel,
            'barcode_path' => $studentCode
        ]);

        // 4. Enroll in selected courses
        if (!empty($courseIds) && is_array($courseIds)) {
            foreach ($courseIds as $cId) {
                $this->studentModel->enrollCourse($studentId, (int) $cId);
            }
        }

        set_flash('success', "Student {$name} registered successfully with Code: {$studentCode}");
        redirect('/admin/students');
    }

    public function enroll(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            redirect('/admin/students');
        }

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $courseId = (int) ($_POST['course_id'] ?? 0);

        if ($studentId > 0 && $courseId > 0) {
            $this->studentModel->enrollCourse($studentId, $courseId);
            set_flash('success', 'Student enrolled in course successfully.');
        } else {
            set_flash('error', 'Invalid student or course selection.');
        }

        redirect('/admin/students');
    }
}
