<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Attendance.php';
require_once __DIR__ . '/../Models/Student.php';
require_once __DIR__ . '/../Models/Course.php';

class AttendanceController {
    private Attendance $attendanceModel;
    private Student $studentModel;
    private Course $courseModel;

    public function __construct() {
        $this->attendanceModel = new Attendance();
        $this->studentModel = new Student();
        $this->courseModel = new Course();
    }

    public function index(): void {
        RoleMiddleware::handle('admin', 'teacher');
        $user = auth_user();

        if ($user['role'] === 'teacher') {
            $courses = $this->courseModel->getByTeacher($user['id']);
        } else {
            $courses = $this->courseModel->getAll();
        }

        $recentLogs = $this->attendanceModel->getRecentLogs(50);
        $stats = $this->attendanceModel->getTodayStats();

        view('admin/attendance', [
            'courses'    => $courses,
            'recentLogs' => $recentLogs,
            'stats'      => $stats
        ]);
    }

    public function scanApi(): void {
        RoleMiddleware::handle('admin', 'teacher');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            response_json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        if (!$data) {
            $data = $_POST;
        }

        $code = sanitize($data['student_code'] ?? '');
        $courseId = (int) ($data['course_id'] ?? 0);
        $status = sanitize($data['status'] ?? 'present');

        if (empty($code) || $courseId <= 0) {
            response_json(['success' => false, 'message' => 'Student Code and Course selection are required.'], 400);
        }

        $student = $this->studentModel->findByStudentCode($code);
        if (!$student) {
            response_json(['success' => false, 'message' => "Student Code '{$code}' not found in system."], 404);
        }

        $course = $this->courseModel->findById($courseId);
        if (!$course) {
            response_json(['success' => false, 'message' => 'Selected class/course does not exist.'], 404);
        }

        $user = auth_user();
        $date = date('Y-m-d');

        $result = $this->attendanceModel->markAttendance(
            $student['id'],
            $courseId,
            $date,
            $status,
            $user['id'],
            $code
        );

        if ($result['success']) {
            // Trigger SMS notification to parent
            require_once __DIR__ . '/../Services/SmsService.php';
            SmsService::sendAttendanceNotification(
                $student,
                $course['title'],
                $status,
                date('h:i A'),
                $student['access_token'] ?? ''
            );

            response_json([
                'success'      => true,
                'message'      => "Attendance marked ({$status}) for {$student['name']}.",
                'student'      => [
                    'name'         => $student['name'],
                    'code'         => $student['student_code'],
                    'grade'        => $student['grade_level'],
                    'course_title' => $course['title'],
                    'time'         => date('h:i A')
                ]
            ]);
        } else {
            response_json(['success' => false, 'message' => $result['message']], 500);
        }
    }

    public function markManual(): void {
        RoleMiddleware::handle('admin', 'teacher');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            redirect('/admin/attendance');
        }

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $studentCode = sanitize($_POST['student_code'] ?? '');
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'present');
        $date = sanitize($_POST['date'] ?? date('Y-m-d'));

        if ($studentId <= 0 && !empty($studentCode)) {
            $studentObj = $this->studentModel->findByStudentCode($studentCode);
            if ($studentObj) {
                $studentId = (int) $studentObj['id'];
            }
        }

        if ($studentId > 0 && $courseId > 0) {
            $user = auth_user();
            $res = $this->attendanceModel->markAttendance($studentId, $courseId, $date, $status, $user['id'], 'MANUAL');
            if ($res['success']) {
                $studentObj = $this->studentModel->findById($studentId);
                $courseObj = $this->courseModel->findById($courseId);
                if ($studentObj && $courseObj) {
                    require_once __DIR__ . '/../Services/SmsService.php';
                    SmsService::sendAttendanceNotification(
                        $studentObj,
                        $courseObj['title'],
                        $status,
                        date('h:i A'),
                        $studentObj['access_token'] ?? ''
                    );
                }
                set_flash('success', "Manual attendance marked ({$status}) for {$studentObj['name']}.");
            } else {
                set_flash('error', $res['message']);
            }
        } else {
            set_flash('error', 'Student not found. Please verify the Student Code (e.g. STU-2026-001).');
        }

        redirect('/admin/attendance');
    }
}
