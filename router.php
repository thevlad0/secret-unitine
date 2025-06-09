<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$auth_views_path     = __DIR__ . '/auth/views';
$messages_views_path = __DIR__ . '/messages/views';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = ltrim($request, '/');

switch ($request) {
    case BASE_PATH . '':
    case BASE_PATH . 'inbox':
        require $messages_views_path . '/Inbox.php';
        break;

    case BASE_PATH . 'register':
        require $auth_views_path . '/Register.php';
        break;

    case BASE_PATH . 'login':
        require $auth_views_path . '/Login.php';
        break;

    case BASE_PATH . 'logout':
        require $auth_views_path . '/Logout.php';
        break;

    case BASE_PATH . 'forgotten-password':
        require $auth_views_path . '/ForgottenPassword.php';
        break;

    case BASE_PATH . 'confirm-reset-password':
        $username = $_GET['username'];
        require $auth_views_path . '/ConfirmResetPassword.php';
        break;

    case BASE_PATH . 'change-password':
        $username = $_GET['username'];
        require $auth_views_path . '/ChangePassword.php';
        break;

    default:
        http_response_code(404);
        //require __DIR__ . '/404.php';
        break;
}

?>