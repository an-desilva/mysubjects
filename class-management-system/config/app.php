<?php

// Load .env variables if present
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv("{$name}={$value}");
        }
    }
}

// Global Application Constants
$appName = getenv('APP_NAME') ?: ($_ENV['APP_NAME'] ?? 'Tuition & Class Management System');
$appEnv  = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'development');
$appDebug = getenv('APP_DEBUG') ?: ($_ENV['APP_DEBUG'] ?? 'true');

define('APP_NAME', $appName);
define('APP_ENV', $appEnv);
define('APP_DEBUG', filter_var($appDebug, FILTER_VALIDATE_BOOLEAN));
define('BASE_PATH', dirname(__DIR__));

// Dynamic Base URL helper calculation
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$publicPath = dirname($scriptName);
$publicPath = str_replace('\\', '/', $publicPath);
if ($publicPath === '/' || $publicPath === '.') {
    $publicPath = '';
}
$calculatedAppUrl = $protocol . '://' . $host . $publicPath;
$envAppUrl = getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? ($_SERVER['APP_URL'] ?? null));
define('APP_URL', rtrim($envAppUrl ?: $calculatedAppUrl, '/'));

// Secure Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime', $_ENV['SESSION_LIFETIME'] ?? 7200);
    session_start();
}

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper Functions
function base_url(string $path = ''): string {
    return APP_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function asset(string $path): string {
    return base_url('assets/' . ltrim($path, '/'));
}

function format_currency($amount): string {
    return 'Rs. ' . number_format((float) $amount, 2);
}

function redirect(string $url): void {
    if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
        $url = base_url($url);
    }
    header("Location: {$url}");
    exit();
}

function view(string $viewPath, array $data = []): void {
    extract($data);
    $fullPath = BASE_PATH . '/views/' . ltrim($viewPath, '/') . '.php';
    if (file_exists($fullPath)) {
        require $fullPath;
    } else {
        throw new Exception("View file not found: {$fullPath}");
    }
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function response_json(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function set_flash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

function get_flash(string $type): ?string {
    if (isset($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    return null;
}

function auth_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function has_role(string ...$roles): bool {
    if (!is_logged_in()) return false;
    return in_array($_SESSION['user']['role'], $roles, true);
}

function csrf_field(): string {
    $token = $_SESSION['csrf_token'] ?? '';
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function verify_csrf(): bool {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
