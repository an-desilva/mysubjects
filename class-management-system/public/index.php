<?php

/**
 * Single Entry Point - Front Controller
 * Tuition & Class Management System
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Class Autoloader for src/ directory
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/src/Controllers/' . $class . '.php',
        BASE_PATH . '/src/Models/' . $class . '.php',
        BASE_PATH . '/src/Middleware/' . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Extract requested URI path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$uriPath = parse_url($requestUri, PHP_URL_PATH);

// Strip base path prefix if app is served from subfolder (e.g. /myubject/class-management-system/public)
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$scriptDir = str_replace('\\', '/', $scriptDir);
if ($scriptDir !== '/' && str_starts_with($uriPath, $scriptDir)) {
    $uriPath = substr($uriPath, strlen($scriptDir));
}

// Fallback check for query route parameter (e.g. ?route=login)
if (isset($_GET['route'])) {
    $uriPath = '/' . ltrim($_GET['route'], '/');
}

$path = '/' . trim($uriPath, '/');
if ($path === '/' || $path === '/index.php') {
    $path = is_logged_in() ? '/' : '/login';
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

// Front Controller Router Table
switch ($path) {
    // Authentication Routes
    case '/login':
        $controller = new AuthController();
        if ($requestMethod === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;

    case '/logout':
        (new AuthController())->logout();
        break;

    // Admin Routes
    case '/admin/dashboard':
        (new AdminController())->dashboard();
        break;

    case '/admin/students':
        (new StudentController())->index();
        break;

    case '/admin/students/store':
        (new StudentController())->store();
        break;

    case '/admin/students/enroll':
        (new StudentController())->enroll();
        break;

    case '/admin/attendance':
        (new AttendanceController())->index();
        break;

    case '/admin/attendance/mark':
        (new AttendanceController())->markManual();
        break;

    case '/api/attendance/scan':
        (new AttendanceController())->scanApi();
        break;

    case '/admin/fees':
        (new FeeController())->index();
        break;

    case '/admin/fees/pay':
        (new FeeController())->store();
        break;

    case '/fees/receipt':
        (new FeeController())->receipt();
        break;

    // Teacher Routes
    case '/teacher/materials':
        (new MaterialController())->teacherIndex();
        break;

    case '/teacher/materials/upload':
        (new MaterialController())->upload();
        break;

    case '/teacher/quizzes':
        (new QuizController())->teacherIndex();
        break;

    case '/teacher/quizzes/create':
        (new QuizController())->storeQuiz();
        break;

    case '/teacher/quizzes/add-question':
        (new QuizController())->addQuestion();
        break;

    // Student Routes
    case '/student/materials':
        (new MaterialController())->studentIndex();
        break;

    case '/materials/download':
        (new MaterialController())->download();
        break;

    case '/student/quizzes':
    case '/student/take-quiz':
        (new QuizController())->studentTake();
        break;

    case '/student/submit-quiz':
        (new QuizController())->submitQuiz();
        break;

    case '/student/fees':
        (new FeeController())->studentFees();
        break;

    // Home route redirect
    case '/':
        if (is_logged_in()) {
            $role = auth_user()['role'];
            if ($role === 'admin') redirect('/admin/dashboard');
            if ($role === 'teacher') redirect('/teacher/materials');
            if ($role === 'student') redirect('/student/materials');
        } else {
            redirect('/login');
        }
        break;

    default:
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='en'><head><title>404 Not Found</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-950 text-white min-h-screen flex items-center justify-center p-6'><div class='text-center space-y-4'><h1 class='text-6xl font-extrabold text-brand-500'>404</h1><h2 class='text-2xl font-bold'>Page Not Found</h2><p class='text-slate-400'>The path <code>" . htmlspecialchars($path) . "</code> does not exist.</p><a href='" . base_url() . "' class='inline-block px-6 py-3 bg-indigo-600 rounded-xl font-bold text-white hover:bg-indigo-500 transition-colors'>Return Home</a></div></body></html>";
        break;
}
