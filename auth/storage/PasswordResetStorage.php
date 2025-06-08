<?php
    require_once __DIR__ . '/../../storage/Storage.php';
    require_once __DIR__ . '/../../storage/db.php';

    require_once __DIR__ . '/../models/PasswordReset.php';

    class PasswordResetStorage implements Storage {
        private $conn;

        public function __construct() {
            $db = new DB();
            $this->conn = $db->getConnection();
        }

        public function get($username) {
            $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return null;
            }

            $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);

            return new PasswordReset(
                $reset_data['username'],
                $reset_data['resetToken'],
                $reset_data['expiresAt']
            );
        }

        public function add($password_reset) {
            $stmt = $this->conn->prepare("INSERT INTO password_resets (username, resetToken, expiresAt) VALUES (:username, :resetToken, :expiresAt)");
            
            $username = $password_reset->username();
            $resetToken = $password_reset->resetToken();
            $expiresAt = $password_reset->expiresAt();
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':resetToken', $resetToken);
            $stmt->bindParam(':expiresAt', $expiresAt);
            
            return $stmt->execute();
        }
        public function remove($username) {
            $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE username = :username");
            $stmt->bindParam(':username', $username);
            return $stmt->execute();
        }

        public function exists($username) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM password_resets WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        }
    }
?>