<?php
    header("Access-Control-Allow-Origin: http://localhost");
    header('Content-Type: application/json; charset=utf-8');
    session_start();

    require_once __DIR__ . '/../auth/UserController.php';

    $action = $_REQUEST['action'] ?? '';
    $userController = new UserController();

    switch ($action) {
        case 'search_users':
            $searchTerm = trim($_GET['term'] ?? '');
            $users = $userController->search($searchTerm);
            echo json_encode(['status' => 'success', 'users' => $users]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Невалидно действие.']);
            break;
    }
?>