<?php
class Message {
    private $id;
    private $senderId;
    private $sentAt;
    private $topic;
    private $content;
    private $chainNumber;
    private $isAnonymous;
    private $senderUsername;
    private $isRead;
    private $isStarred;

    public function __construct($id, $senderId, $sentAt, $topic, $content, $chainNumber, $isAnonymous, $senderUsername, $isRead, $isStarred) {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->sentAt = $sentAt;
        $this->topic = $topic;
        $this->content = $content;
        $this->chainNumber = $chainNumber;
        $this->isAnonymous = $isAnonymous;
        $this->senderUsername = $senderUsername;
        $this->isRead = $isRead;
        $this->isStarred = $isStarred;
    }

    public function setChainNumber(int $newValue) {
        if (newValue > 0) {
            $this->chainNumber = newValue;
        } else {
            throw new InvalidArgumentException('New value of chain Number must be positive number. Input was: '.$newValue);
        }
    }

     public function setId(int $newValue) {
        $this->id = $newValue;
    }

    public function getId() {
        return $this->id;
    }

    public function getIsAnonymous() {
        return $this->isAnonymous;
    }

     public function getChainNumber() {
        return $this->chainNumber;
    }

     public function getSentAt() {
        return $this->sentAt;
    }

     public function getSenderId() {
        return $this->senderId;
    }

    public function getTopic() {
        return $this->topic;
    }

    public function getContent() {
        return $this->content;
    }

     public function getSenderUsername() {
        return $this->senderUsername;
    }

    public static function fromArray($data) {
        return new Message($data['id'], $data['senderId'], $data['sentAt'],
     $data['topic'], $data['content'], $data['chainNumber'], $data['isAnonymous'], $data['senderUsername'],
    $data['isRead'], $data['isStarred']);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'senderId' => $this->senderId,            
            'sentAt' => $this->sentAt,
            'topic' => $this->topic,
            'content' => $this->content,
            'chainNumber' => $this->chainNumber,
            'isAnonymous' => $this->isAnonymous,
            'senderUsername' => $this->senderUsername,
            'isRead' => $this->isRead,
            'isStarred' => $this->isStarred,
        ];
    }
}
?>