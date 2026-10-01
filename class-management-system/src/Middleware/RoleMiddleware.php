<?php

require_once __DIR__ . '/../../config/app.php';

class RoleMiddleware {
    public static function handle(string ...$allowedRoles): void {
        AuthMiddleware::handle();

        $user = auth_user();
        if (!$user || !in_array($user['role'], $allowedRoles, true)) {
            set_flash('error', 'Access denied. You do not have permission for this section.');
            
            // Redirect based on current user role
            if ($user['role'] === 'admin') {
                redirect('/admin/dashboard');
            } elseif ($user['role'] === 'teacher') {
                redirect('/teacher/materials');
            } else {
                redirect('/student/materials');
            }
        }
    }
}
