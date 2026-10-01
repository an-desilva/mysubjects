<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/Student.php';
require_once __DIR__ . '/../Models/Course.php';

class FeeController {
    private Payment $paymentModel;
    private Student $studentModel;
    private Course $courseModel;

    public function __construct() {
        $this->paymentModel = new Payment();
        $this->studentModel = new Student();
        $this->courseModel = new Course();
    }

    public function index(): void {
        RoleMiddleware::handle('admin');
        $payments = $this->paymentModel->getAllPayments();
        $students = $this->studentModel->getAll();
        $courses = $this->courseModel->getAll();
        $stats = $this->paymentModel->getMonthlyFeeStats();

        view('admin/fees', [
            'payments' => $payments,
            'students' => $students,
            'courses'  => $courses,
            'stats'    => $stats
        ]);
    }

    public function store(): void {
        RoleMiddleware::handle('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            redirect('/admin/fees');
        }

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $month = sanitize($_POST['month'] ?? date('Y-m'));
        $amount = (float) ($_POST['amount'] ?? 0);
        $paymentMethod = sanitize($_POST['payment_method'] ?? 'cash');
        $notes = sanitize($_POST['notes'] ?? '');

        if ($studentId <= 0 || $courseId <= 0 || $amount <= 0) {
            set_flash('error', 'Student, Course, and Amount are required fields.');
            redirect('/admin/fees');
        }

        $receiptNumber = $this->paymentModel->recordPayment([
            'student_id'     => $studentId,
            'course_id'      => $courseId,
            'month'          => $month,
            'amount'         => $amount,
            'payment_date'   => date('Y-m-d'),
            'payment_method' => $paymentMethod,
            'status'         => 'paid',
            'notes'          => $notes
        ]);

        if ($receiptNumber) {
            // Fetch student and course for SMS notification
            $student = $this->studentModel->findById($studentId);
            $course = $this->courseModel->findById($courseId);
            if ($student && $course) {
                require_once __DIR__ . '/../Services/SmsService.php';
                SmsService::sendPaymentNotification(
                    $student,
                    $course['title'],
                    $amount,
                    $receiptNumber,
                    $student['access_token'] ?? ''
                );
            }

            set_flash('success', "Payment recorded! Receipt No: {$receiptNumber}");
            redirect('/fees/receipt?number=' . urlencode($receiptNumber));
        } else {
            set_flash('error', 'Failed to record payment. Please try again.');
            redirect('/admin/fees');
        }
    }

    public function receipt(): void {
        AuthMiddleware::handle();
        $receiptNumber = sanitize($_GET['number'] ?? '');
        if (empty($receiptNumber)) {
            redirect('/admin/fees');
        }

        $receipt = $this->paymentModel->getReceipt($receiptNumber);
        if (!$receipt) {
            set_flash('error', 'Receipt not found.');
            redirect('/admin/fees');
        }

        // Student can only view their own receipts
        $user = auth_user();
        if ($user['role'] === 'student' && $user['student_id'] != $receipt['student_id']) {
            set_flash('error', 'Access denied.');
            redirect('/student/fees');
        }

        view('admin/receipt', ['receipt' => $receipt]);
    }

    public function studentFees(): void {
        RoleMiddleware::handle('student');
        $user = auth_user();
        $studentId = $user['student_id'];

        if (!$studentId) {
            $student = $this->studentModel->findByUserId($user['id']);
            $studentId = $student ? $student['id'] : 0;
        }

        $payments = $this->paymentModel->getStudentPayments($studentId);
        $enrolledCourses = $this->studentModel->getEnrolledCourses($studentId);

        view('student/fees', [
            'payments'        => $payments,
            'enrolledCourses' => $enrolledCourses
        ]);
    }
}
