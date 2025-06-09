<?php
require_once('./storage/messageRepository.php');

class MessageController {
    private $messageRepository;

    public function __construct() {
        $this->messageRepository = new MessageRepository();
    }

    public function addMessage($senderId, $topic, $content, $isAnonymous, $recipientsIds) {
        $message = new Message($senderId, $topic, $content, $isAnonymous);
        $this->messageRepository->addMessage($message, $recipientsIds);
    }

    public function removeMessageOfFolder($messageId, $userId, $folderName) {
        $this->messageRepository->removeMessageOfFolder($messageId, $userId, $folderName);
    }

    public function getSentMessagesOfUser($userId): array {
        return $this->messageRepository->getSentMessagesOfUser($userId);
    }

    public function getInboxOfUser($userId): array {
        return $this->messageRepository->getInboxOfUser($userId);
    }

    public function getMessageRecipientsIds($messageId): array {
        return $this->messageRepository->getMessageRecipientsIds($messageId);
    }

    public function changeStarredStatusOfMessage($isStarred, $messageId, $userId, $folderName) {
        $this->messageRepository->changeStarredStatusOfMessage($isStarred, $messageId, $userId, $folderName);
    }

    public function readMessage($messageId, $userId, $folderName) {
        $this->messageRepository->readMessage($messageId, $userId, $folderName);
    }

    public function filterByStar($userId, $folderName) : array {
        return $this->messageRepository->filterByStar($userId, $folderName);
    }

    public function filterByRead($isRead, $userId, $folderName) : array {
        return $this->messageRepository->filterByRead($isRead, $userId, $folderName);
    }

    public function filterByAnonimity($isAnonimous, $userId, $folderName) : array {
        return $this->messageRepository->filterByAnonimity($isAnonimous, $userId, $folderName);
    }

    public function filterByGroup($groupId, $userId, $folderName) : array {
        return $this->messageRepository->filterByGroup($groupId, $userId, $folderName);
    }
    public function filterByDate($date, $userId, $folderName) : array {
        return $this->messageRepository->filterByDate($date, $userId, $folderName);
    }
    public function filterByTopic($topic, $userId, $folderName) : array {
        return $this->messageRepository->filterByTopic($topic, $userId, $folderName);
    }

    public function sortMessagesByDate($order, $userId, $folderName) : array {
        return $this->messageRepository->sortMessagesByDate($order, $userId, $folderName);
    }
}
?>