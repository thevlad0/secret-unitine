//A script that reads a particular email from db and returns info about it

<?php
require_once __DIR__ . '../storage/messageRepository.php';
require_once __DIR__ . '../models/message.php';

session_start();

if (!isSet($_SESSION['userID'])) {
   http_response_code(400);
   echo json_encode(['message' => 'User has not logged in!']);
   exit;
}

if (!isset($_GET['messageId'])) {
  http_response_code(400);
  echo json_encode(['message' => 'Message id missing!']);
  exit;
}

$messageID = intval($_GET['messageId']);
$messageRepository = new MessageRepository();
$message = $messageRepository->getMessageById($messageID);

if (!$message) {
  http_response_code(404);
  echo json_encode(['message' => 'No message with that id!']);
  exit;
}

$messageRepository->readMessage($messageID, $_SESSION['userID'], 'Inbox');  //marking message as read
echo json_encode($message->jsonSerialize());