<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Student.php';
require_once __DIR__ . '/../Models/Course.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/Attendance.php';

class AdminController {
    private Student $studentModel;
    private Course $courseModel;
    private Payment $paymentModel;
    private Attendance $attendanceModel;

    public function __construct() {
        RoleMiddleware::handle('admin');
        $this->studentModel = new Student();
        $this->courseModel = new Course();
        $this->paymentModel = new Payment();
        $this->attendanceModel = new Attendance();
    }

    public function dashboard(): void {
        $totalStudents = $this->studentModel->countTotal();
        $totalCourses = $this->courseModel->countTotal();
        $feeStats = $this->paymentModel->getMonthlyFeeStats();
        $attendanceStats = $this->attendanceModel->getTodayStats();
        $recentPayments = array_slice($this->paymentModel->getAllPayments(), 0, 5);
        $recentAttendance = $this->attendanceModel->getRecentLogs(5);

        view('admin/dashboard', [
            'totalStudents'    => $totalStudents,
            'totalCourses'     => $totalCourses,
            'feeStats'         => $feeStats,
            'attendanceStats'  => $attendanceStats,
            'recentPayments'   => $recentPayments,
            'recentAttendance' => $recentAttendance
        ]);
    }
}
