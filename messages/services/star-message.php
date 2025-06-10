<?php 
    require_once('../MessageController.php');

    $data = json_decode(file_get_contents('php://input'), true);
    $messageController = new MessageController();

    $messageController->changeStarredStatusOfMessage($data['isStarred'], $data['messageId'], $data['userId'], $data['folderName']);
?>