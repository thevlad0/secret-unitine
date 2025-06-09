<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../auth/storage/UserStorage.php';
    require_once __DIR__ . '/storage/GroupStorage.php';
    require_once __DIR__ . '/storage/GroupMemberStorage.php';

    require_once __DIR__ . '/services/get_group.php';
    require_once __DIR__ . '/services/edit_group.php';

    class GroupController {
        private $userStorage;
        private $groupStorage;
        private $groupMemberStorage;

        public function __construct() {
            $this->userStorage = new UserStorage();
            $this->groupStorage = new GroupStorage();
            $this->groupMemberStorage = new GroupMemberStorage();
        }

        public function getAllGroups() {
            return handleGetAllGroups($this->groupStorage, $this->groupMemberStorage, $this->userStorage);
        }

        public function getUserGroups($userId) {
            return handleGetUserGroups($userId, $this->groupStorage, $this->groupMemberStorage, $this->userStorage);
        }

        public function getGroupMembers($groupId) {
            return handleGetGroupMembers($groupId, $this->groupMemberStorage, $this->groupMemberStorage, $this->userStorage);
        }

        public function createGroup($groupName, $ownerId) {
            return handleCreateGroup($groupName, $ownerId, $this->groupStorage, $this->groupMemberStorage);
        }

        public function addGroupMember($groupId, $memberId) {
            return handleAddGroupMember($groupId, $memberId, $this->groupMemberStorage);
        }

        public function removeGroupMember($groupId, $memberId) {
            return handleRemoveGroup($groupId, $memberId, $this->groupMemberStorage);
        }

        public function removeGroup($groupId) {
            return handleRemoveGroup($groupId, $this->groupStorage, $this->groupMemberStorage);
        }
    }
?>