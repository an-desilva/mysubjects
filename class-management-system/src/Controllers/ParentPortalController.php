<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Models/Student.php';
require_once __DIR__ . '/../Models/Attendance.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/Quiz.php';

class ParentPortalController {
    private Student $studentModel;
    private Attendance $attendanceModel;
    private Payment $paymentModel;
    private Quiz $quizModel;
    private PDO $db;

    public function __construct() {
        $this->studentModel = new Student();
        $this->attendanceModel = new Attendance();
        $this->paymentModel = new Payment();
        $this->quizModel = new Quiz();
        $this->db = Database::getConnection();
    }

    public function viewReport(string $token): void {
        $token = sanitize($token);
        if (empty($token)) {
            $this->showNotFound();
            return;
        }

        // Fetch student by access_token, student_code, or short token prefix
        $stmt = $this->db->prepare("
            SELECT s.*, u.name, u.email
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.access_token = :token 
               OR s.student_code = :code 
               OR LEFT(s.access_token, 8) = :short_token
            LIMIT 1
        ");
        $stmt->execute([
            'token'       => $token,
            'code'        => $token,
            'short_token' => $token
        ]);
        $student = $stmt->fetch();

        if (!$student) {
            $this->showNotFound();
            return;
        }

        $studentId = (int) $student['id'];

        // Enrolled courses
        $enrolledCourses = $this->studentModel->getEnrolledCourses($studentId);

        // Attendance summary & logs
        $attendanceLogs = $this->attendanceModel->getStudentAttendance($studentId);
        $totalSessions = count($attendanceLogs);
        $presentCount = 0;
        $lateCount = 0;
        $absentCount = 0;

        foreach ($attendanceLogs as $att) {
            if ($att['status'] === 'present') $presentCount++;
            elseif ($att['status'] === 'late') $lateCount++;
            else $absentCount++;
        }

        $attendanceRate = $totalSessions > 0 ? round((($presentCount + $lateCount) / $totalSessions) * 100, 1) : 100;

        // Payments history
        $payments = $this->paymentModel->getStudentPayments($studentId);
        $totalPaidAmount = array_sum(array_column($payments, 'amount'));

        // Quiz attempts
        $stmtQuiz = $this->db->prepare("
            SELECT qa.*, q.title as quiz_title, q.pass_mark, c.title as course_title
            FROM quiz_attempts qa
            JOIN quizzes q ON qa.quiz_id = q.id
            JOIN courses c ON q.course_id = c.id
            WHERE qa.student_id = :student_id AND qa.status = 'completed'
            ORDER BY qa.submitted_at DESC
        ");
        $stmtQuiz->execute(['student_id' => $studentId]);
        $quizAttempts = $stmtQuiz->fetchAll();

        view('parent/report_view', [
            'student'          => $student,
            'enrolledCourses'  => $enrolledCourses,
            'attendanceLogs'   => $attendanceLogs,
            'totalSessions'    => $totalSessions,
            'presentCount'     => $presentCount,
            'lateCount'        => $lateCount,
            'absentCount'      => $absentCount,
            'attendanceRate'   => $attendanceRate,
            'payments'         => $payments,
            'totalPaidAmount'  => $totalPaidAmount,
            'quizAttempts'     => $quizAttempts
        ]);
    }

    private function showNotFound(): void {
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='en'><head><title>Invalid Report Link</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-950 text-white min-h-screen flex items-center justify-center p-6'><div class='glass-panel p-8 rounded-3xl text-center space-y-4 max-w-md border border-slate-800'><i class='fa-solid fa-triangle-exclamation text-4xl text-rose-500'></i><h1 class='text-2xl font-bold'>Invalid Parent Link</h1><p class='text-slate-400 text-sm'>This parent access token is invalid or expired. Please contact the class administrator for a new report link.</p></div></body></html>";
        exit();
    }
}
