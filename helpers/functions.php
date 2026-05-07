<?php
/**
 * Helper Functions — Security, Sanitization, Formatting
 */

// ── Session Management ──
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {

        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');

        session_start();
    }

    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

function currentUser(): array
{
    return $_SESSION['user'] ?? [];
}

// ── CSRF Protection ──
function generateCSRF(): string
{
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }

    return $_SESSION[CSRF_TOKEN_NAME];
}

function csrfField(): string
{
    $token = generateCSRF();

    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . e($token) . '">';
}

function verifyCSRF(): bool
{
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';

    return !empty($token)
        && isset($_SESSION[CSRF_TOKEN_NAME])
        && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// ── Input Sanitization ──
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitize(string $str): string
{
    return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitizeInt($val): int
{
    return (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
}

function sanitizeFloat($val): float
{
    return (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function sanitizeEmail(string $val): string
{
    return filter_var(trim($val), FILTER_SANITIZE_EMAIL);
}

// ── Validation ──
function validateRequired(array $fields, array $data): array
{
    $errors = [];

    foreach ($fields as $field => $label) {
        if (empty(trim($data[$field] ?? ''))) {
            $errors[$field] = "{$label} is required.";
        }
    }

    return $errors;
}

function validateMobile(string $mobile): bool
{
    return preg_match('/^[6-9]\d{9}$/', $mobile) === 1;
}

function validatePAN(string $pan): bool
{
    return empty($pan) || preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', strtoupper($pan)) === 1;
}

function validateAadhar(string $aadhar): bool
{
    return empty($aadhar) || preg_match('/^\d{12}$/', $aadhar) === 1;
}

// ── File Upload with MIME Validation ──
function uploadFile(array $file, string $subDir = '', array $allowedTypes = []): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'error' => 'Upload failed with error code: ' . $file['error']
        ];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return [
            'success' => false,
            'error' => 'File exceeds maximum size of 2MB.'
        ];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (empty($allowedTypes)) {
        $allowedTypes = ALLOWED_IMAGE_TYPES;
    }

    if (!in_array($mimeType, $allowedTypes, true)) {
        return [
            'success' => false,
            'error' => 'Invalid file type.'
        ];
    }

    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];

    $ext = $extMap[$mimeType] ?? pathinfo($file['name'], PATHINFO_EXTENSION);

    $filename = uniqid('file_', true) . '.' . $ext;

    $dir = rtrim(UPLOAD_DIR, '/') . '/' . trim($subDir, '/') . '/';

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $path = $dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $path)) {
        return [
            'success' => false,
            'error' => 'Failed to move uploaded file.'
        ];
    }

    return [
        'success' => true,
        'filename' => $filename,
        'path' => $subDir . '/' . $filename
    ];
}

// ── URL & Navigation ──
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function activeNav(string $page): string
{
    $current = $_GET['page'] ?? 'dashboard';

    return $current === $page ? 'active' : '';
}

// ── Formatting ──
function formatCurrency(float $amount): string
{
    return '₹' . number_format($amount, 0, '.', ',');
}

function formatDate(string $date): string
{
    if (empty($date) || $date === '0000-00-00') {
        return '—';
    }

    return date('d M Y', strtotime($date));
}

function formatDateTime(string $dt): string
{
    if (empty($dt)) {
        return '—';
    }

    return date('d M Y, h:i A', strtotime($dt));
}

function initials(string $name): string
{
    $words = explode(' ', trim($name));

    $init = strtoupper($words[0][0] ?? '');

    if (count($words) > 1) {
        $init .= strtoupper(end($words)[0] ?? '');
    }

    return $init;
}

function avatarColor(string $name): string
{
    $colors = [
        '#0a1628',
        '#0e4429',
        '#6b2130',
        '#2d1a50',
        '#504a1a',
        '#1a3950',
        '#501a3a',
        '#1a5038'
    ];

    $hash = crc32($name);

    return $colors[abs($hash) % count($colors)];
}

// ── Flash Messages ──
function setFlash(string $type, string $title, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'title' => $title,
        'message' => $message
    ];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;

    unset($_SESSION['flash']);

    return $flash;
}

// ── Activity Logging ──
function logActivity(PDO $db, string $action, string $module, string $details = ''): void
{
    $stmt = $db->prepare("
        INSERT INTO activity_log
        (user_id, action, module, details, ip_address)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $action,
        $module,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
    ]);
}