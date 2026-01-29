<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);

session_start();

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_NAME', 'Dynamic Quiz Application');
define('APP_VERSION', '1.0.0');
define('RESULTS_DIR', __DIR__ . '/../data/results/');

if (!file_exists(RESULTS_DIR)) {
    mkdir(RESULTS_DIR, 0755, true);
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function send_json_response($data, $status_code = 200)
{
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>