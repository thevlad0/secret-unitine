<?php
require_once __DIR__ . '/storage/messageRepository.php';

class MessageController {
    private $messageRepository;

    public function __construct() {
        $this->messageRepository = new MessageRepository();
    }

    public function addMessage(int $senderId, string $sentAt, string $topic, string $content, $chainNumber, bool $isAnonymous, array $recipientsIds) {
        $this->messageRepository->addMessage($senderId, $sentAt, $topic, $content, $chainNumber, $isAnonymous, $recipientsIds);
    }

    public function removeMessageOfFolder($messageId, $userId, $folderName) {
        $this->messageRepository->removeMessageOfFolder($messageId, $userId, $folderName);
    }

    public function getStarredMessagesOfUser($userId) : array {
        return $this->messageRepository->getStarredMessagesOfUser($userId);
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

    public function filterByRead($isRead, $userId, $folderName) : array {
        return $this->messageRepository->filterByRead($isRead, $userId, $folderName);
    }

    public function filterByAnonimity($isAnonimous, $userId, $folderName) : array {
        return $this->messageRepository->filterByAnonimity($isAnonimous, $userId, $folderName);
    }

    public function sortMessagesByDate($order, $userId, $folderName) : array {
        return $this->messageRepository->sortMessagesByDate($order, $userId, $folderName);
    }
}
?>