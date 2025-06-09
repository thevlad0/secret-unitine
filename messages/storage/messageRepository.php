<?php
require_once('message.php');
require_once('messageRepositoryAPI.php');
require_once('../db/db.php');

define("INBOX_FOLDER_ID", 1);
define("SENT_FOLDER_ID", 2);
define("DELETED_FOLDER_ID", 3);

class MessageRepository implements MessageRepositoryAPI {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function addMessage(Message $message, array $recipientsIds) {
        try {
            $connection = $this->db->getConnection();
            
            $connection->beginTransaction();
            $sql = "INSERT INTO messages (senderId, sentAt, topic, content, chainNumber, isAnonymous) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStatement = $connection->prepare($sql);
            $insertStatement->execute([$message->getSenderId(), $message->getSentAt(), $message->getTopic(), $message->getContent(),
            $message->getChainNumber(), $message->getIsAnonymous()]);  

            $messageId = $connection->lastInsertId();
            //add to message_status_table AS SENT --new private method
            $sql = "INSERT INTO user_messages_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
            $insertStatement = $connection->prepare($sql);
            $insertStatement->execute([$messageId, $message->getSenderId(), SENT_FOLDER_ID]); 

            $sql = "INSERT INTO message_recipients (messageId, recipientId, recipientGroupId) VALUES (?, ?, ?)";
            $insertStatement = $connection->prepare($sql);

            foreach ($recipientsIds as $recipientId) {
                //if Group::isValidGroupId($recipientId) {
                if ($this->isValidGroupId($recipientId)) {
                //$members = Group::getMembersIdsOfGroup($recipientId)
                $members = $this->getMembersIdsOfGroup($recipientId);
                foreach ($members as $groupMemberId) {
                    $insertStatement->execute([$messageId, $groupMemberId, $recipientId]);

                    //add to message_status_table AS received  -->new method !!! sql! vmesto sqlInsertInFolder
                    $sqlInsertInFolder = "INSERT INTO user_messages_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
                    $insertStatementMessageStatus = $connection->prepare($sqlInsertInFolder);
                    $insertStatementMessageStatus->execute([$messageId, $groupMemberId, INBOX_FOLDER_ID]); 
                }
                } else {
                    $insertStatement->execute([$messageId, $recipientId, null]);

                    //add to message_status_table AS received  -->new method !!!!
                    $sqlInsertInFolder = "INSERT INTO user_messages_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
                    $insertStatementMessageStatus = $connection->prepare($sqlInsertInFolder);
                    $insertStatementMessageStatus->execute([$messageId, $recipientId, INBOX_FOLDER_ID]);  
                }
            }
            
            $connection->commit();
            echo "Successfully added message";

        } catch (PDOException $e) {
            $connection->rollback();
            error_log(date("Y-m-d H:i:s") . " - Error occurred while adding message: "
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function removeMessageOfFolder(int $messageId, int $userId, string $folderName) {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            if ($messageFolderId == null ) {
                throw new InvalidArgumentException("Folder name of message to remove is not Inbox, SentMessages or Deleted. Input folder name: ".$folderName);
            }
            if (strcasecmp($folderName, "Inbox") == 0 || strcasecmp($folderName, "SentMessages") == 0) {
                //if message is sent from me to me, update it everywhere
                if ($this->haveSameSenderAndRecipient($messageId, $userId)) {
                    //delete from current folder
                    $sql = "DELETE FROM user_messages_status WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $deleteStatement = $connection->prepare($sql);
                    $deleteStatement->execute([$messageId, $userId, $messageFolderId]);

                    //move it to trashed
                    $sql = "UPDATE user_messages_status SET messageFolderId = ? WHERE messageId = ? AND userId = ?";
                    $updateStatement = $connection->prepare($sql);
                    $updateStatement->execute([DELETED_FOLDER_ID, $messageId, $userId]);
                } else {
                    $sql = "UPDATE user_messages_status SET messageFolderId = ? WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $updateStatement = $connection->prepare($sql);
                    $updateStatement->execute([DELETED_FOLDER_ID, $messageId, $userId, $messageFolderId]);
                }
            } else if (strcasecmp($folderName, "Deleted") == 0) {
                $sql = "SELECT COUNT(*) AS count FROM user_messages_status WHERE messageId = :messageId";
                $selectStatement = $connection->prepare($sql);
                $selectStatement->execute(['messageId' => $messageId]);
                $resultData = $selectStatement->fetch();
                //same sender and recipient when remove from deleted message remove message from messages
                if ($resultData && $resultData['count'] == 1) {
                    //Important! CASCADE ON DELETE for messageId_FK in user_messages_status and message_recipients tables !
                    $sql = "DELETE FROM messages WHERE id = ?";
                    $deleteStatement = $connection->prepare($sql);
                    $deleteStatement->execute([$messageId]);
                } else {
                    $sql = "DELETE FROM user_messages_status WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $deleteStatement = $connection->prepare($sql);
                    $deleteStatement->execute([$messageId, $userId, $messageFolderId]);
                }
            }
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while removing a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function getSentMessagesOfUser(int $userId): array {
        try {
            $connection = $this->db->getConnection();
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
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500); 
        }
    }

    public function getInboxOfUser(int $userId): array {
        try {
            $connection = $this->db->getConnection();
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
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function getMessageRecipientsIds($messageId): array {
        try {
            $connection = $this->db->getConnection();
            $sql = "SELECT recipientId, recipientGroupId FROM message_recipients WHERE messageId = ?";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$messageId]);
            
            $recipientsData = $selectStatement->fetchAll();
            $recipientsIds = [];

            foreach ($recipientsData as $recipient) {
                if ($recipient['recipientGroupId'] == null) {
                    $recipientsIds[] = $recipient['recipientId'];
                } else if(!in_array($recipient['recipientGroupId'], $recipientsIds)) {
                    $recipientsIds[] = $recipient['recipientGroupId'];
                }
            }
            return $recipientsIds;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while getting recipientsIds of message with id=$messageId : "
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function changeStarredStatusOfMessage(bool $isStarred, int $messageId, int $userId, string $folderName) { 
         try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "UPDATE user_messages_status SET isStarred = ? WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
            $updateStatement = $connection->prepare($sql);
            $updateStatement->execute([$isStarred, $messageId, $userId, $messageFolderId]);
        }
         catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while starring a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function readMessage(int $messageId, int $userId, string $folderName) { 
         try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "UPDATE user_messages_status SET isRead = 1 WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
            $updateStatement = $connection->prepare($sql);
            $updateStatement->execute([$messageId, $userId, $messageFolderId]);
        }
         catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while starring a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function filterByStar(int $userId, string $folderName) : array {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT * FROM messages m JOIN user_messages_status AS ums ON m.id = ums.messageId WHERE ums.userId = ? AND ums.messageFolderId = ? AND ums.isStarred = 1";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId]);
            
            $starredMessagesData = $selectStatement->fetchAll();
            $starredMessages = [];

            foreach ($starredMessagesData as $starredMessage) {
                $starredMessages[] = Message::fromArray($starredMessage);
            }
            return $starredMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by star: " 
            . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function filterByRead(bool $isRead, int $userId, string $folderName) : array {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT * FROM messages m JOIN user_messages_status AS ums ON m.id = ums.messageId WHERE ums.userId = ? AND ums.messageFolderId = ? AND ums.isRead = ?";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId, $isRead]);
            
            $readMessagesData = $selectStatement->fetchAll();
            $readMessages = [];

            foreach ($readMessagesData as $readMessage) {
                $readMessages[] = Message::fromArray($readMessage);
            }
            return $readMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by unread: " 
            . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

     public function filterByAnonimity(bool $isAnonimous, int $userId, string $folderName) : array {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT * FROM messages m JOIN user_messages_status AS ums ON m.id = ums.messageId WHERE ums.userId = ? AND ums.messageFolderId = ? AND m.isAnonymous = ?";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId, $isAnonimous]);
            
            $anonymousMessagesData = $selectStatement->fetchAll();
            $anonymousMessages = [];

            foreach ($anonymousMessagesData as $anonymousMessage) {
                $anonymousMessages[] = Message::fromArray($anonymousMessage);
            }
            return $anonymousMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by anonimity: " 
            . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    //TO-DO!!!! filter samo po grupi v koito uchastva dadeniq potrebitel
    public function filterByGroup(int $groupId, int $userId, string $folderName) : array {
         try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT * FROM messages m
             JOIN user_messages_status AS ums ON m.id = ums.messageId
             JOIN message_recipients AS mr ON m.id = mr.messageId
             WHERE ums.userId = ? AND ums.messageFolderId = ? AND mr.recipientGroupId = ?";

            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId, $groupId]);
            
            $resultData = $selectStatement->fetchAll();
            $resultMessages = [];

            foreach ($resultData as $message) {
                $resultMessages[] = Message::fromArray($message);
            }
            return $resultMessages;
        }  catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by group: " 
            . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }
    
    public function filterByDate(string $date, int $userId, string $folderName) : array {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT * FROM messages m JOIN user_messages_status AS ums ON m.id = ums.messageId WHERE ums.userId = ? AND ums.messageFolderId = ? AND m.sentAt = ?";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId, $date]);
            
            $messagesFromDateData = $selectStatement->fetchAll();
            $messagesFromDate = [];

            foreach ($messagesFromDateData as $messageFromDate) {
                $messagesFromDate[] = Message::fromArray($messageFromDate);
            }
            return $messagesFromDate;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by date: " 
            . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function filterByTopic(string $topic, int $userId, string $folderName) : array {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT * FROM messages m JOIN user_messages_status AS ums ON m.id = ums.messageId WHERE ums.userId = ? AND ums.messageFolderId = ? AND m.topic = ?";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId, $topic]);
            
            $messagesByTopicData = $selectStatement->fetchAll();
            $messagesByTopic = [];

            foreach ($messagesByTopicData as $messageByTopic) {
                $messagesByTopic[] = Message::fromArray($messageByTopic);
            }
            return $messagesByTopic;  
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by topic: " 
            . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }
    
    public function sortMessagesByDate(string $order, int $userId, string $folderName) : array {
         try {
            if (strcasecmp($order, "DESC") != 0 && strcasecmp($order, "ASC") != 0) {
                error_log(date("Y-m-d H:i:s") . " - Error occurred while sorting with order= $order" . "\n", 3,"../logs/error_log.txt");
                http_response_code(500);
                throw new InvalidArgumentException("Sorting order is not DESC or ASC. Input order: ".$order);
            }
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $order = strtoupper($order);

            $sql = "SELECT * FROM messages m JOIN user_messages_status AS ums ON m.id = ums.messageId WHERE ums.userId = ? AND ums.messageFolderId = ? ORDER BY m.sentAt $order";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId]);

            $sortedMessagesData = $selectStatement->fetchAll();
            $sortedMessages = [];
            
            foreach ($sortedMessagesData as $message) {
                $sortedMessages[] = Message::fromArray($message);
            }
            return $sortedMessages;  
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while sorting with order= $order ". $e->getMessage() . "\n", 3, "../logs/error_log.txt");
            http_response_code(500);
        }
    }

    private function getMessageFolderId(string $folderName): ?int {
        switch ($folderName) {
            case 'Inbox': return INBOX_FOLDER_ID;
            case 'SentMessages': return SENT_FOLDER_ID;
            case 'Deleted': return DELETED_FOLDER_ID;
            default: return INBOX_FOLDER_ID;
        }
    }

    private function haveSameSenderAndRecipient(int $messageId, int $userId): bool {
         try {
            $connection = $this->db->getConnection();
            $sql = "SELECT 1 AS haveSameSenderAndRecipient
             FROM user_messages_status AS ums
             JOIN messages AS m ON ums.messageId = m.id WHERE m.id = :messageId
              AND m.senderId = :userId AND ums.userId = :userId";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute(['messageId' => $messageId,
                                       'userId' => $userId]);
            
            $resultData = $selectStatement->fetch();

            return $resultData ? (bool) $resultData['haveSameSenderAndRecipient'] : false;
        } catch (PDOException $e) {
            http_response_code(500);
             error_log(date("Y-m-d H:i:s") . " - Error occurred while getting messageFolderId with folderName=$folderName : "
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
        }
    }

    //move this method to Group class make it static!!
    private function isValidGroupId(int $groupId): bool {
         try {
         $connection = $this->db->getConnection();
         $sql = "SELECT 1 AS isValidGroup FROM groups WHERE id = :groupId";
         $selectStatement = $connection->prepare($sql);
         $selectStatement->execute(['groupId' => $groupId]);
            
         $resultData = $selectStatement->fetch();
         return $resultData ? $resultData['isValidGroup'] : false;
          } catch (PDOException $e) {
            http_response_code(500);
             error_log(date("Y-m-d H:i:s") . " - Error occurred while checking is valid group id: "
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
        }
    }

    //move this method to Group class make it static!!
    private function getMembersIdsOfGroup(int $groupId):?array {
         try {
            $connection = $this->$db->getConnection();
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
             . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
        }
    }
}

//Test cases

//$test = new MessageRepository();

/*$message = new Message(2, 'Interesting topic', 'Hello world!', true);
$test->addMessage($message, [4]);
$message = new Message(3, 'New topic', 'Bye!', true);
$test->addMessage($message, [2, 4]);*/

/*$message = new Message(3, 'Same sender and recipient', 'Bye bye!', false);
$test->addMessage($message, [3]);*/

//$test->removeMessageOfFolder(1, 4, 'Inbox');
//$test->removeMessageOfFolder(1, 2, 'SentMessages');
//$test->removeMessageOfFolder(3, 3, 'SentMessages');
//$test->removeMessageOfFolder(3, 3, 'Deleted');
//$test->removeMessageOfFolder(1, 2, 'Deleted');

/*$message = new Message(3, 'Interesting topic', 'Hello world!', true);
$test->addMessage($message, [4]);*/

//var_dump($test->getSentMessagesOfUser(3));
//var_dump($test->getSentMessagesOfUser(2));

//var_dump($test->getInboxOfUser(2));
//var_dump($test->getInboxOfUser(4));

//var_dump($test->getMessageRecipientsIds(2));
//var_dump($test->getMessageRecipientsIds(1));
//var_dump($test->getMessageRecipientsIds(1));

//$test->changeStarredStatusOfMessage(true, 2, 2, 'Inbox');
//$test->changeStarredStatusOfMessage(true, 4, 3, 'SentMessages');
//$test->changeStarredStatusOfMessage(false, 4, 3, 'SentMessages');

//$test->unstarMessage(4, 3, 'SentMessages');

//$test->readMessage(2, 2, 'Inbox');
//$test->readMessage(4, 3, 'SentMessages');
//$test->readMessage(1, 4, 'Deleted');

//var_dump($test->filterByStar(2, 'Inbox'));
//var_dump($test->filterByStar(4, 'SentMessages'));

//var_dump($test->filterByRead(false, 4, 'Inbox'));
//var_dump($test->filterByRead(false, 4, 'Deleted'));
//var_dump($test->filterByRead(false, 3, 'SentMessages'));

//var_dump($test->filterByRead(true, 4, 'Inbox'));
//var_dump($test->filterByRead(true, 4, 'Deleted'));
//var_dump($test->filterByRead(true, 3, 'SentMessages'));

//var_dump($test->filterByAnonimity(true, 3, 'SentMessages'));
//var_dump($test->filterByAnonimity(false, 3, 'SentMessages'));

//var_dump($test->filterByDate('2025-06-08 16:20:53', 3, 'SentMessages'));

//var_dump($test->filterByTopic('Interesting topic', 3, 'SentMessages'));
//var_dump($test->filterByTopic('New topic', 2, 'Inbox'));

//var_dump($test->sortMessagesByDate('ASC', 4, 'Inbox'));
//var_dump($test->sortMessagesByDate('DESC', 4, 'Inbox'));
?>