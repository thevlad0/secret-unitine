<?php 
    require_once(__DIR__ . '/../../php/messageRepository.php');

    session_start();

    if (isSet($_SESSION["user"]) && !empty($_SESSION["user"])) {
        $user = $_SESSION["user"];
        $messageRepository = new MessageRepository();

        $userInboxMessages = $messageRepository->getInboxOfUser($user->getId());     
        
        $inboxMessagesJson = array_map(fn($msg) => $msg->jsonSerialize(), $userInboxMessages);
        echo json_encode($inboxMessagesJson);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Грешка при отварянето на входящата кутия!"]);
    }
?>

