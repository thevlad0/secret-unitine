<?php 
    require_once('../MessageController.php');

    session_start();

    //IMPORTANT!!!

    //if (isSet($_SESSION["user"]))) {
        //$userId = $_SESSION['user']['id'];
        //!isSet($_SESSION['userID'])

        $userId = 4;
        $messageController = new MessageController();

        //$userSortedInboxMessages = $messageController->getInboxOfUser($userId);

        $userSortedInboxMessages = $messageController->sortMessagesByDate('DESC', $userId, 'Inbox');
        $inboxMessagesJson = array_map(fn($msg) => $msg->jsonSerialize(), $userSortedInboxMessages);
        echo json_encode($inboxMessagesJson, JSON_UNESCAPED_UNICODE);
   /* } else {
        http_response_code(400);
        echo json_encode(["message" => "Грешка при отварянето на входящата кутия!"]);
        exit;
    }*/
?>