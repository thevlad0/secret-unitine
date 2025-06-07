<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$auth_views_path     = __DIR__ . '/auth/views';
$messages_views_path = __DIR__ . '/messages/views';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo $actual_path;

switch ($actual_path) {
    case '/':
        require $messages_views_path . '/inbox.php';
        break;

    case '/register':
        require $auth_views_path . '/register.php';
        break;

    case '/login':
        require $auth_views_path . '/login.php';
        break;

    case '/logout':
        require $auth_views_path . '/logout.php';
        break;

    case '/forgotten-password':
        require $auth_views_path . '/forgotten-password.php';
        break;

    default:
        http_response_code(404);
        //require __DIR__ . '/404.php';
        break;
}

?>