<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Material.php';
require_once __DIR__ . '/../Models/Course.php';
require_once __DIR__ . '/../Models/Student.php';

class MaterialController {
    private Material $materialModel;
    private Course $courseModel;
    private Student $studentModel;

    public function __construct() {
        $this->materialModel = new Material();
        $this->courseModel = new Course();
        $this->studentModel = new Student();
    }

    public function teacherIndex(): void {
        RoleMiddleware::handle('teacher', 'admin');
        $user = auth_user();

        if ($user['role'] === 'teacher') {
            $courses = $this->courseModel->getByTeacher($user['id']);
            $materials = $this->materialModel->getByTeacher($user['id']);
        } else {
            $courses = $this->courseModel->getAll();
            $materials = $this->materialModel->getAll();
        }

        view('teacher/materials', [
            'courses'   => $courses,
            'materials' => $materials
        ]);
    }

    public function upload(): void {
        RoleMiddleware::handle('teacher', 'admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            redirect('/teacher/materials');
        }

        $title = sanitize($_POST['title'] ?? '');
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $type = sanitize($_POST['type'] ?? 'pdf');
        $description = sanitize($_POST['description'] ?? '');

        if (empty($title) || $courseId <= 0 || !isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            set_flash('error', 'Please fill in all required fields and select a valid PDF file.');
            redirect('/teacher/materials');
        }

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
            set_flash('error', 'Only PDF and Word document files are allowed.');
            redirect('/teacher/materials');
        }

        $targetDir = BASE_PATH . '/storage/uploads/materials/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = uniqid('mat_', true) . '.' . $ext;
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $user = auth_user();
            $this->materialModel->create([
                'course_id'   => $courseId,
                'uploaded_by' => $user['id'],
                'title'       => $title,
                'description' => $description,
                'file_path'   => $filename,
                'file_size'   => $file['size'],
                'type'        => $type
            ]);

            set_flash('success', 'Study material uploaded successfully.');
        } else {
            set_flash('error', 'Failed to upload file to storage.');
        }

        redirect('/teacher/materials');
    }

    public function studentIndex(): void {
        RoleMiddleware::handle('student');
        $user = auth_user();

        $student = $this->studentModel->findByUserId($user['id']);
        if (!$student) {
            set_flash('error', 'Student record not found.');
            redirect('/login');
        }

        $materials = $this->materialModel->getForStudent($student['id']);

        view('student/materials', [
            'materials' => $materials
        ]);
    }

    public function download(): void {
        AuthMiddleware::handle();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            set_flash('error', 'Invalid material ID.');
            redirect('/student/materials');
        }

        $material = $this->materialModel->findById($id);
        if (!$material) {
            set_flash('error', 'Material not found.');
            redirect('/student/materials');
        }

        // Check permission if student
        $user = auth_user();
        if ($user['role'] === 'student') {
            $student = $this->studentModel->findByUserId($user['id']);
            $enrolled = $this->studentModel->getEnrolledCourses($student['id']);
            $enrolledCourseIds = array_column($enrolled, 'id');
            if (!in_array($material['course_id'], $enrolledCourseIds)) {
                set_flash('error', 'Access denied. You are not enrolled in this course.');
                redirect('/student/materials');
            }
        }

        $filePath = BASE_PATH . '/storage/uploads/materials/' . $material['file_path'];

        if (!file_exists($filePath)) {
            // Serve sample fallback PDF notice if file missing
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($material['title']) . '.pdf"');
            echo "%PDF-1.4 sample PDF content placeholder";
            exit();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($material['title']) . '.pdf"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    }
}
