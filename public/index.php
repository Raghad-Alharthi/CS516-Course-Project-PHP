<?php
// 1. Bootstrap
require_once __DIR__ . '/../app/config.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: Redirect user based on their role
function redirectToDashboard()
{
    if (empty($_SESSION['role'])) {
        header('Location: index.php?route=account/login');
        exit;
    }

    switch ($_SESSION['role']) {
        case 'Admin':
            header('Location: index.php?route=admin/dashboard');
            break;
        case 'Teacher':
            header('Location: index.php?route=teacher/dashboard');
            break;
        case 'Student':
            header('Location: index.php?route=student/dashboard');
            break;
        default:
            session_destroy();
            header('Location: index.php?route=account/login');
            break;
    }
    exit;
}

// 2. Determine $route
if (!empty($_GET['route'])) {
    // Use explicit ?route=controller/action
    $route = trim($_GET['route'], '/');
} else {
    // Fallback to pretty‐URL parsing
    $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base   = dirname($_SERVER['SCRIPT_NAME']);
    $path   = substr($uri, strlen($base));
    $route  = trim($path, '/') ?: null;
}

// 3. Handle if no route is set
if ($route === null) {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?route=account/login');
        exit;
    } else {
        redirectToDashboard();
    }
}

// 4. Split into controller/action
[$controller, $action] = explode('/', $route) + [1 => 'index'];
$controller = strtolower($controller);
$action     = strtolower($action);

// 5. Dispatch
$ctrlFile  = __DIR__ . "/../app/controllers/" . ucfirst($controller) . "Controller.php";
$className = ucfirst($controller) . 'Controller';

if (is_file($ctrlFile)) {
    require_once $ctrlFile;
    if (class_exists($className)) {
        $ctl = new $className();
        if (method_exists($ctl, $action)) {
            $ctl->{$action}();
            exit;
        }
    }
}

// 6. 404 Fallback
http_response_code(404);
echo "Page not found.";
