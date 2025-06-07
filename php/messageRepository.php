<?php
require_once('message.php');
require_once('db/db.php');

class MessageRepository implements MessageRepositoryAPI {
    private $db;
    
    //define as constants
    define(INBOX_FOLDER_ID, 1);
    define(SENT_FOLDER_ID, 2);
    define(DELETED_FOLDER_ID, 3);

    public function __construct() {
        $db = new DB();
    }

    //recipientsIds sa id-tata na To: ot new message, moje i da sa grupi 
    public function addMessage(Message $message, array recipientsIds) {
        try {
            $connection = self::$db->getConnection();
            
            //TO-DO bez id(messaage), to shte e avtomaticno???
            $sql = "INSERT INTO messages (id, senderId, sentAt,
     topic, content, chainNumber, isAnonymous) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStatement = $connection->prepare($sql);
            $insertStatement->execute([$message->getId(), $message->getSenderId(),
            $message->getSentAt(), $message->getTopic(), $message->getContent(), $message->getChainNumber(), $message->getIsAnonymous()]);  

            //add to message_status_table AS SENT --new private method
            $sql = "INSERT INTO user_message_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
            $insertStatement = $connection->prepare($sql);
            $insertStatement->execute([$message->getId(), $message->getSenderId(), SENT_FOLDER_ID]); 

            $sql = "INSERT INTO message_recipients (messageId, recipientId, recipientGroupId) VALUES (?, ?, ?)";
            $insertStatement = $connection->prepare($sql);
            foreach ($recipientsIds as $recipientId) {
                //if Group::isValidGroupId($recipientId) {
                if isValidGroupId($recipientId) {
                //$members = Group::getMembersIdsOfGroup($recipientId)
                $members = getMembersIdsOfGroup($recipientId);
                foreach ($recipientId as $groupMemberId) {
                    $insertStatement = $connection->execute([$message->getId(), $groupMemberId, $recipientId]);

                    //add to message_status_table AS received  -->new method !!! sql! vmesto sqlInsertInFolder
                    $sqlInsertInFolder = "INSERT INTO user_message_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
                    $insertStatement = $connection->prepare($sqlInsertInFolder);
                    $insertStatement->execute([$message->getId(), $groupMemberId, INBOX_FOLDER_ID]); 
                }
                } else {
                    $insertStatement = $connection->execute([$message->getId(), $recipientId, null]);

                    //add to message_status_table AS received  -->new method !!!!
                    $sqlInsertInFolder = "INSERT INTO user_message_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
                    $insertStatement = $connection->prepare($sqlInsertInFolder);
                    $insertStatement->execute([$message->getId(), $recipientId, INBOX_FOLDER_ID]);  
                }
            }
            echo json_encode(["status" => "SUCCESS", "message" => "Съобщението е добавено."]);

        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while adding message: "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function removeMessageOfFolder(int $messageId, int $userId, string $folderName) {
        try {
            $connection = self::$db->getConnection();

            $messageFolderDeletedId = getMessageFolderId("Deleted");    //to remove?? vinagi da si e 3?? TO-DO!!
            $messageFolderId = getMessageFolderId($folderName);
            if ($messageFolderId == null ) {
                throw new InvalidArgumentException("Folder name of message to remove is not Inbox, SentMessages or Deleted. Input folder name: ".$folderName);
            }
            if (strcasecmp(folderName, "Inbox") == 0 || strcasecmp(folderName, "SentMessages") == 0) {
                //if message is sent from me to me, update it everywhere
                if (haveSameSenderAndRecipient($messageId, $userId)) {
                    $sql = "UPDATE user_messages_status SET messageFolderId = ? WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $updateStatement = $connection->prepare($sql);
                    $updateStatement->execute([$messageFolderDeletedId, $messageId, $userId, $messageFolderId]);
                }

                else {
                    $sql = "UPDATE user_messages_status SET messageFolderId = ? WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $updateStatement = $connection->prepare($sql);
                    $updateStatement->execute([$messageFolderDeletedId, $messageId, $userId, $messageFolderId]);
                }
            } else if (strcasecmp(folderName, "Deleted") == 0) {
                $sql = "DELETE FROM user_messages_status WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                $deleteStatement = $connection->prepare($sql);
                $deleteStatement->execute([$messageId, $userId, $messageFolderId]);

                //probvai dali shte se iztrie ot messages avtomatichno????!!!
            }
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while removing a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function getSentMessagesOfUser(int $userId): array {
        try {
            $connection = self::$db->getConnection();
            $sql = "SELECT * FROM messages WHERE senderId = ?";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId]);
            
            $inboxData = $selectStatement->fetchAll();
            $sentMessages = [];

            foreach ($inboxData as $sentMessage) {
                $sentMessages[] = Message::fromArray($sentMessage);
            }
            return $sentMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while getting sent messages of user with id=$userId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
            
        }
    }

    public function getInboxOfUser(int $userId): array {
        try {
            $connection = self::$db->getConnection();
            $sql = "SELECT * FROM messages m JOIN message_recipients AS mr ON m.id = mr.messageId WHERE mr.recipientId = ?";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId]);
            
            $inboxData = $selectStatement->fetchAll();
            $receivedMessages = [];

            foreach ($inboxData as $receivedMessage) {
                $receivedMessages[] = Message::fromArray($receivedMessage);
            }
            return $receivedMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while getting inbox messages of user with id=$userId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function starMessage(int $messageId, int $userId, string $folderName) { 
         try {
            $connection = self::$db->getConnection();
            $messageFolderId = getMessageFolderId($folderName);
            $sql = "UPDATE user_messages_status SET isStarred = 1 WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
            $updateStatement = $connection->prepare($sql);
            $updateStatement->execute([$messageId, $userId, $messageFolderId]);
        }
         catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while starring a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function readMessage(int $messageId, int $userId, string $folderName) { 
         try {
            $connection = self::$db->getConnection();
            $messageFolderId = getMessageFolderId($folderName);
            $sql = "UPDATE user_messages_status SET isRead = 1 WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
            $updateStatement = $connection->prepare($sql);
            $updateStatement->execute([$messageId, $userId, $messageFolderId]);
        }
         catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while starring a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }


    //TO-DO:!!!
    public function filterByStar(int $userId, string $folderName) : array {
        try {
            
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by star: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function filterByAnonimity(int $userId, string $folderName) : array {
        try {
            
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by anonimity: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function filterByGroup(int $groupId, int $userId, string $folderName) : array {
        try {
            
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by group: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }
    
    public function filterByDate(string date, int $userId, string $folderName) : array {
        try {
            
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by date: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function filterByTopic(string topic, int $userId, string $folderName) : array {
        try {
            
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by topic: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

   public function sortAscendingByDate(int $userId, string $folderName) : array {
        try {
            
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while sorting ascending by date: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function sortDescendingByDate(int $userId, string $folderName) : array {
        try {
            
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while sorting ascending by date: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
            http_response_code(500);
        }
    }


    //TO-DO toRemove
    private function getMessageFolderId(string $folderName): ?int {
         try {
            $connection = self::$db->getConnection();
            $sql = "SELECT id FROM message_folders WHERE folderName = ?";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$folderName]);

            $folderIdData = $selectStatement->fetch();

            return $folderIdData ? $folderIdData['id'] : null;

            //second way
            /*switch ($folderName) {
                case 'Inbox': 
                    $messageFolderId = INBOX_FOLDER_ID;
                    break;
                case 'SentMessages': 
                    $messageFolderId = SENT_FOLDER_ID;
                    break;
                case 'DELETED_FOLDER_ID': 
                    $messageFolderId = SENT_FOLDER_ID;
                    break;
            }*/
        } catch (PDOException $e) {
            http_response_code(500);
             error_log(date("Y-m-d H:i:s") . " - Error occurred while getting messageFolderId with folderName=$folderName : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
        }
    }

    private function haveSameSenderAndRecipient(int $messageId, int $userId): bool {
         try {
            $connection = self::$db->getConnection();
            $sql = "SELECT 1 AS haveSameSenderAndRecipient
             FROM user_messages_status
             JOIN messages ON user_messages_status.messageId = messages.id WHERE messages.id = :messageId
              AND messages.senderId = :userId AND user_messages_status.userId = :userId";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute(['messageId' => $messageId,
                                       'userId' => $userId]);
            
            $resultData = $selectStatement->fetch();

            return $resultData ? (bool) $resultData['haveSameSenderAndRecipient'] : false;
        } catch (PDOException $e) {
            http_response_code(500);
             error_log(date("Y-m-d H:i:s") . " - Error occurred while getting messageFolderId with folderName=$folderName : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
        }
    }

    //move this method to Group class make it static!!
    private isValidGroupId(int $groupId): bool {
         $connection = self::$db->getConnection();
         $sql = "SELECT 1 AS isValidGroup FROM groups WHERE id = :groupId";
         $selectStatement = $connection->prepare($sql);
         $selectStatement->execute(['groupId' => $groupId]);
            
         $resultData = $selectStatement->fetch();
         return $resultData ? $resultData['isValidGroup'] : false;
    }

    //move this method to Group class make it static!!
    private getMembersIdsOfGroup(int $groupId):?array {
         try {
            $connection = self::$db->getConnection();
            $sql = "SELECT userId FROM group_members WHERE groupId = :groupId";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute(['groupId' => $groupId]);
            
            $usersData = $selectStatement->fetchAll();
            $usersIds = [];
            foreach ($usersData as $userId) {
                $usersIds[] = $userId['userId'];
            }
            return $usersIds;
        } catch (PDOException $e) {
            http_response_code(500);
             error_log(date("Y-m-d H:i:s") . " - Error occurred while members ids of group with groupId = $groupId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/logs/error_log.txt");
        }
    }
}
?>