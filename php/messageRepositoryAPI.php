<?php 
interface MessageRepositoryAPI {
    //recipientsIds sa id-tata na To: ot new message, moje i da sa id na grupi
    //@message - new message to add 
    public function addMessage(Message $message, array recipientsIds);

    /*
    @messageId -> id of message that must be removed
    @userId -> id of current user
    @folderName = current folder{Inbox, SentMessages or Deleted}
    */
    public function removeMessageOfFolder(int $messageId, int $userId, string $folderName);

    /*
    @userId -> id of current user
    Returns an array with messages
    */
    public function getSentMessagesOfUser(int $userId): array;
    public function getInboxOfUser(int $userId): array;

     /*
    @messageId -> id of message that must be starred/read
    @userId -> id of current user
    @folderName = current folder{Inbox, SentMessages or Deleted}
    */
    public function starMessage(int $messageId, int $userId, string $folderName);
    public function readMessage(int $messageId, int $userId, string $folderName);

    public function filterByStar(int $userId, string $folderName) : array;
    public function filterByAnonimity(int $userId, string $folderName) : array;
    public function filterByGroup(int $groupId, int $userId, string $folderName) : array;
    public function filterByDate(string date, int $userId, string $folderName) : array;
    public function filterByTopic(string topic, int $userId, string $folderName) : array;

    public function sortAscendingByDate(int $userId, string $folderName) : array;
    public function sortDescendingByDate(int $userId, string $folderName) : array;

}
?>