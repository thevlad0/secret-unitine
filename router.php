<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$auth_views_path     = __DIR__ . '/auth/views';
$messages_views_path = __DIR__ . '/messages/views';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo $request;

switch ($request) {
    case '/':
    case '/inbox':
        require $messages_views_path . '/Inbox.php';
        break;

    case '/register':
        require $auth_views_path . '/Register.php';
        break;

    case '/login':
        require $auth_views_path . '/Login.php';
        break;

    case '/logout':
        require $auth_views_path . '/Logout.php';
        break;

    case '/forgotten-password':
        require $auth_views_path . '/ForgottenPassword.php';
        break;

    case '/confirm-reset-password':
        $username = $_GET['username'];
        require $auth_views_path . '/ConfirmResetPassword.php';
        break;

    case '/change-password':
        $username = $_GET['username'];
        require $auth_views_path . '/ChangePassword.php';
        break;

    default:
        http_response_code(404);
        //require __DIR__ . '/404.php';
        break;
}

?>