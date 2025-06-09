<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    header("Access-Control-Allow-Origin: http://localhost");
    header('Content-Type: application/json; charset=utf-8');
    session_start();

    require_once __DIR__ . '/../groups/GroupController.php';

    $action = $_REQUEST['action'] ?? '';
    $groupController = new GroupController();

    switch ($action) {
        case 'get_groups':
            $groups = $groupController->getUserGroups($_GET['userId'])['groups'] ?? [];
            echo json_encode(['status' => 'success', 'groups' => $groups]);
            break;

        case 'add_group':
            $groupName = trim($_POST['name']);
            $ownerId = (int)($_POST['ownerId']);

            $result = $groupController->createGroup($groupName, $ownerId);
            $result['group']['users'] = [];

            echo json_encode($result);
            break;

        case 'add_user':
            $groupId = (int)($_POST['groupId']);
            $userId = (int)($_POST['userId']);

            $result = $groupController->addGroupMember($groupId, $userId);

            echo json_encode($result);
            break;

        case 'delete_group':
            $groupId = (int)($_POST['id']);
            $deleted = $groupController->removeGroup($groupId);
            echo json_encode($deleted);
            break;
            
        case 'delete_user':
            $groupId = (int)($_POST['groupId']);
            $userId = (int)($_POST['userId']);
            
            $result = $groupController->removeGroupMember($groupId, $userId);

            echo json_encode($result);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Невалидно действие.']);
            break;
    }
?>