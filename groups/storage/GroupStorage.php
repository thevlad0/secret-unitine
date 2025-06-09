<?php
    require_once __DIR__ . '/../../storage/Storage.php';
    require_once __DIR__ . '/../../storage/db.php';

    require_once __DIR__ . '/../models/Group.php';

    class GroupStorage implements Storage {
        private $conn;

        public function __construct() {
            $db = new DB();
            $this->conn = $db->getConnection();
        }

        public function getAll() {
            $stmt = $this->conn->prepare("SELECT * FROM groups");
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return [];
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function get($groupId) {
            $stmt = $this->conn->prepare("SELECT * FROM groups WHERE id = :id");
            $stmt->bindParam(':id', $groupId);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        }

        public function add($groupData) {
            $stmt = $this->conn->prepare("INSERT INTO groups (groupName, ownerId) VALUES (:groupName, :ownerId)");

            $groupName = $groupData->name();
            $ownerId = $groupData->owner();

            $stmt->bindParam(':groupName', $groupName);
            $stmt->bindParam(':ownerId', $ownerId);
            
            if ($stmt->execute()) {
                return [
                    'id' => $this->conn->lastInsertId(),
                    'groupName' => $groupName,
                    'ownerId' => $ownerId
                ];
            }
        }

        public function remove($groupId) {
            $stmt = $this->conn->prepare("DELETE FROM groups WHERE id = :id");
            $stmt->bindParam(':id', $groupId);
            return $stmt->execute();
        }

        public function exists($groupId) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM groups WHERE id = :id");
            $stmt->bindParam(':id', $groupId);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        }
    }
?>