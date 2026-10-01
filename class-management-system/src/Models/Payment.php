<?php

require_once __DIR__ . '/../../config/database.php';

class Payment {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function recordPayment(array $data): ?string {
        $receiptNumber = $this->generateReceiptNumber();
        $stmt = $this->db->prepare("
            INSERT INTO payments (receipt_number, student_id, course_id, month, amount, payment_date, payment_method, status, notes)
            VALUES (:receipt_number, :student_id, :course_id, :month, :amount, :payment_date, :payment_method, :status, :notes)
        ");
        $success = $stmt->execute([
            'receipt_number' => $receiptNumber,
            'student_id'     => $data['student_id'],
            'course_id'      => $data['course_id'],
            'month'          => $data['month'],
            'amount'         => $data['amount'],
            'payment_date'   => $data['payment_date'] ?? date('Y-m-d'),
            'payment_method' => $data['payment_method'] ?? 'cash',
            'status'         => $data['status'] ?? 'paid',
            'notes'          => $data['notes'] ?? null,
        ]);
        return $success ? $receiptNumber : null;
    }

    public function generateReceiptNumber(): string {
        $prefix = 'REC-' . date('Ym') . '-';
        do {
            $num = $prefix . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE receipt_number = :num");
            $stmt->execute(['num' => $num]);
        } while ($stmt->fetchColumn() > 0);
        return $num;
    }

    public function getReceipt(string $receiptNumber): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, s.student_code, u.name as student_name, u.email as student_email, c.title as course_title, c.monthly_fee
            FROM payments p
            JOIN students s ON p.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN courses c ON p.course_id = c.id
            WHERE p.receipt_number = :num
            LIMIT 1
        ");
        $stmt->execute(['num' => $receiptNumber]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getStudentPayments(int $studentId): array {
        $stmt = $this->db->prepare("
            SELECT p.*, c.title as course_title, c.course_code
            FROM payments p
            JOIN courses c ON p.course_id = c.id
            WHERE p.student_id = :student_id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function getAllPayments(): array {
        $stmt = $this->db->prepare("
            SELECT p.*, s.student_code, u.name as student_name, c.title as course_title
            FROM payments p
            JOIN students s ON p.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN courses c ON p.course_id = c.id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMonthlyFeeStats(): array {
        $currentMonth = date('Y-m');
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(amount), 0) as total_collected,
                COUNT(id) as total_payments
            FROM payments
            WHERE month = :month AND status = 'paid'
        ");
        $stmt->execute(['month' => $currentMonth]);
        $paid = $stmt->fetch();

        // Calculate expected dues for enrolled students this month
        $stmtEnroll = $this->db->query("
            SELECT COALESCE(SUM(c.monthly_fee), 0) as expected
            FROM course_enrollments ce
            JOIN courses c ON ce.course_id = c.id
        ");
        $expected = (float) $stmtEnroll->fetchColumn();
        $collected = (float) ($paid['total_collected'] ?? 0);
        $pending = max(0, $expected - $collected);

        return [
            'month'           => $currentMonth,
            'total_collected' => $collected,
            'total_payments'  => (int) ($paid['total_payments'] ?? 0),
            'expected_total'  => $expected,
            'pending_dues'    => $pending
        ];
    }
}
