<?php
/**
 * Pixous HR Portal — Front Controller (Router)
 */

// ── Bootstrap ──
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

// Models
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Employee.php';
require_once __DIR__ . '/models/LeaveRequest.php';
require_once __DIR__ . '/models/Payroll.php';
require_once __DIR__ . '/models/Task.php';

// Controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/EmployeeController.php';
require_once __DIR__ . '/controllers/LeaveController.php';
require_once __DIR__ . '/controllers/PayrollController.php';
require_once __DIR__ . '/controllers/TaskController.php';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Start session
startSecureSession();

// ── Routing ──
$page   = sanitize($_GET['page'] ?? 'dashboard');
$action = sanitize($_GET['action'] ?? 'index');

// Valid routes
$routes = [
    'auth' => [
        'login'  => 'login',
        'logout' => 'logout'
    ],

    'dashboard' => [
        'index' => 'index'
    ],

    'employees' => [
        'index'         => 'index',
        'create'        => 'create',
        'edit'          => 'edit',
        'view'          => 'view',
        'delete'        => 'delete',
        'toggle-status' => 'toggleStatus'
    ],

    'leaves' => [
        'index'   => 'index',
        'store'   => 'store',
        'approve' => 'approve',
        'delete'  => 'delete'
    ],

    'payroll' => [
        'index'    => 'index',
        'generate' => 'generate',
        'payslip'  => 'payslip'
    ],

    'tasks' => [
        'index'  => 'index',
        'store'  => 'store',
        'update' => 'update',
        'delete' => 'delete'
    ]
];

// Controller mapping
$controllers = [
    'auth'      => 'AuthController',
    'dashboard' => 'DashboardController',
    'employees' => 'EmployeeController',
    'leaves'    => 'LeaveController',
    'payroll'   => 'PayrollController',
    'tasks'     => 'TaskController'
];

// Route validation
if (!isset($routes[$page]) || !isset($routes[$page][$action])) {

    if (isLoggedIn()) {
        redirect('dashboard');
    } else {
        redirect('auth/login');
    }
}

// Load controller
$controllerClass = $controllers[$page];
$method = $routes[$page][$action];

$controller = new $controllerClass();

// Method validation
if (!method_exists($controller, $method)) {
    die("Method not found: " . $method);
}

// Execute method
$controller->$method();