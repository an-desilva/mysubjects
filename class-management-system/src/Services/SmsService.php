<?php

class SmsService {
    private static string $userId = '12345'; // Replace with Notify.lk / SMS.lk User ID
    private static string $apiKey = 'DEMO_API_KEY'; // Replace with SMS Gateway API Key
    private static string $senderId = 'TuitionPro';

    public static function sendSms(string $toPhone, string $message): bool {
        if (empty($toPhone)) {
            return false;
        }

        // Format phone number to international Sri Lankan format (e.g. 947XXXXXXXX)
        $cleanPhone = preg_replace('/[^0-9]/', '', $toPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '94' . substr($cleanPhone, 1);
        }

        // Log SMS to file log storage
        $logDir = BASE_PATH . '/storage/logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logEntry = sprintf("[%s] SMS to %s (%s): %s\n", date('Y-m-d H:i:s'), $toPhone, $cleanPhone, $message);
        file_put_contents($logDir . 'sms.log', $logEntry, FILE_APPEND);

        // cURL request to SMS API Gateway (Notify.lk / SMS.lk format)
        $postData = [
            'user_id' => self::$userId,
            'api_key' => self::$apiKey,
            'sender_id' => self::$senderId,
            'to'      => $cleanPhone,
            'message' => $message
        ];

        $ch = curl_init('https://app.notify.lk/api/v1/send');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            error_log("SMS Gateway Error: " . $err);
            return false;
        }

        return true;
    }

    public static function sendAttendanceNotification(array $student, string $courseTitle, string $status, string $time, string $accessToken): void {
        $parentPhone = $student['parent_phone'] ?? $student['phone'] ?? '';
        if (empty($parentPhone)) return;

        $reportUrl = base_url('/report/' . $accessToken);
        $message = sprintf(
            "EduClassPro: %s attended %s at %s. Status: %s. View report: %s",
            $student['name'],
            $courseTitle,
            $time,
            strtoupper($status),
            $reportUrl
        );

        self::sendSms($parentPhone, $message);
    }

    public static function sendPaymentNotification(array $student, string $courseTitle, float $amount, string $receiptNumber, string $accessToken): void {
        $parentPhone = $student['parent_phone'] ?? $student['phone'] ?? '';
        if (empty($parentPhone)) return;

        $reportUrl = base_url('/report/' . $accessToken);
        $message = sprintf(
            "EduClassPro: Fee payment of Rs. %.2f received for %s (%s). Receipt: %s. Parent portal: %s",
            $amount,
            $student['name'],
            $courseTitle,
            $receiptNumber,
            $reportUrl
        );

        self::sendSms($parentPhone, $message);
    }
}
