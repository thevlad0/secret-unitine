<?php
    require_once __DIR__ . '/../../storage/Storage.php';
    require_once __DIR__ . '/../../storage/db.php';

    require_once __DIR__ . '/../models/Group.php';

    class GroupMemberStorage implements Storage {
        private $conn;

        public function __construct() {
            $db = new DB();
            $this->conn = $db->getConnection();
        }

        public function getAll($groupId) {
            $stmt = $this->conn->prepare("SELECT * FROM group_members WHERE groupId = :groupId");
            $stmt->bindParam(':groupId', $groupName);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return [];
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function get($groupId) {
            $stmt = $this->conn->prepare("SELECT * FROM group_members WHERE groupId = :groupId");
            $stmt->bindParam(':groupId', $groupName);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        }

        public function add($groupMember) {
            $stmt = $this->conn->prepare("INSERT INTO group_members (groupId, memberId) VALUES (:groupId, :memberId)");
            
            $groupId = $groupMember->group();
            $memberId = $groupMember->member();
            
            $stmt->bindParam(':groupId', $groupId);
            $stmt->bindParam(':memberId', $memberId);
            
            if ($stmt->execute()) {
                return [
                    'id' => $this->conn->lastInsertId(),
                    'groupId' => $groupId,
                    'memberId' => $memberId
                ];
            }
        }

        public function removeGroup($groupId) {
            $stmt = $this->conn->prepare("DELETE FROM group_members WHERE groupId = :groupId");
            $stmt->bindParam(':groupId', $groupName);
            return $stmt->execute();
        }

        public function remove($groupMember) {
            $stmt = $this->conn->prepare("DELETE FROM group_members WHERE groupId = :groupId AND memberId = :memberId");

            $groupId = $groupMember->group();
            $memberId = $groupMember->member();

            $stmt->bindParam(':groupId', $groupId);
            $stmt->bindParam(':memberId', $memberId);
            return $stmt->execute();
        }

        public function exists($groupMember) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM group_members WHERE groupId = :groupId AND memberId = :memberId");
            
            $groupId = $groupMember->group();
            $memberId = $groupMember->member();
            
            $stmt->bindParam(':groupId', $groupId);
            $stmt->bindParam(':memberId', $memberId);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        }
    }
?>