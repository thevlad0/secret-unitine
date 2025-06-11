<?php 
    require_once('../MessageController.php');

    session_start();

    //IMPORTANT!!!

    //if (isSet($_SESSION["user"]))) {
        //$userId = $_SESSION['user']['id'];
        //!isSet($_SESSION['userID'])

        $userId = 4;       //CHANGE!!!!!!!!!!!

        $data = json_decode(file_get_contents('php://input'), true);
        $messageController = new MessageController();
        $userMessages = [];
        if (!isSet($data['folderName'])) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while generating content - not entered folder name: " . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
            exit;
        }
        if (isSet($data['sortBy'])) {
            $orderBy = strcmp($data['sortBy'], 'date-ASC') === 0 ? 'ASC' : 'DESC';
            $userMessages = $messageController->sortMessagesByDate($orderBy, $userId, $data['folderName']);
        } else if (isSet($data['filterBy'])) {
            if (strcmp($data['filterBy'], 'anonymous') === 0) {
                $userMessages = $messageController->filterByAnonimity(true, $userId, $data['folderName']);
            } else if (strcmp($data['filterBy'], 'non-anonymous') === 0) {
                $userMessages = $messageController->filterByAnonimity(false, $userId, $data['folderName']);
            } else if (strcmp($data['filterBy'], 'read') === 0) {
                $userMessages = $messageController->filterByRead(true, $userId, $data['folderName']);
            } else if (strcmp($data['filterBy'], 'unread') === 0) {
                $userMessages = $messageController->filterByRead(false, $userId, $data['folderName']);
            }
        } else if (strcmp($data['folderName'], 'Starred') === 0) {
            $userMessages = $messageController->getStarredMessagesOfUser($userId);
        } else {
            $userMessages = $messageController->sortMessagesByDate('DESC', $userId, $data['folderName']);
        }

        $messagesJson = array_map(fn($msg) => $msg->jsonSerialize(), $userMessages);
        echo json_encode(["userId" => $userId, "messages" => $messagesJson], JSON_UNESCAPED_UNICODE);
   /* } else {
        http_response_code(400);
        echo json_encode(["message" => "Грешка при отварянето на входящата кутия!"]);
        exit;
    }*/
?>