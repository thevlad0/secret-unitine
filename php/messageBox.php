<?php
require_once('message.php');
class MessageBox {
    private $id;
    private $messages;

    private $messageRepository;
    //private $receivedMessages;
    //private $sentMessages;

    public function __construct($id) {
        $this->id = $id;
        $this->receivedMessages = array();
        $this->sentMessages = array();
    }

    /*public function __construct($id, $receivedMessages, $sentMessages) {
        $this->id = $id;
        $this->receivedMessages = $receivedMessages;
        $this->sentMessages = $sentMessages;
    }*/

    public function __construct($id, $messages) {
        $this->id = $id;
        $this->messages = $messages;
    }

    //public function addReceivedMessage(Message $message) {
    //@Throws InvalidArgumentException
    //TO-DO: Log file for errors!
    public function addReceivedMessage($message) {
        if (!($message instanceof Message)) {
            throw new InvalidArgumentException("An object of type Message is expected, not of type " . gettype($message));
        }
        $this->receivedMessages[] = $message;
    }

    //@Throws InvalidArgumentException
    //TO-DO: Log file for errors! // insert to BD??
    public function addSentMessage($message) {
        if (!($message instanceof Message)) {
            throw new InvalidArgumentException("An object of type Message is expected, not of type " . gettype($message));
        }
        $this->sentMessages[] = $message;
    }

    //removeMessage -> remove From BD

    //FILTER
    //getStarredMessages
    //getDeletedMessages
    //getreceivedMessages
    //getsentMessages
}
?>