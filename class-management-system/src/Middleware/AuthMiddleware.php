<?php

require_once __DIR__ . '/../../config/app.php';

class AuthMiddleware {
    public static function handle(): void {
        if (!is_logged_in()) {
            set_flash('error', 'Please log in to access this page.');
            redirect('/login');
        }
    }
}
