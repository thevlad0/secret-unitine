<?php
    require_once __DIR__ . '/../../storage/Storage.php';
    require_once __DIR__ . '/../../storage/db.php';
    
    require_once __DIR__ . '/../models/User.php';

    class UserStorage implements Storage {
        private $conn;

        public function __construct() {
            $db = new DB();
            $this->conn = $db->getConnection();
        }

        public function getAll() {
            $stmt = $this->conn->prepare("SELECT * FROM users");
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return [];
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById($id) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        }

        public function getByEmail($email) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        }

        public function get($username) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        }

        public function add($user) {
            $stmt = $this->conn->prepare("INSERT INTO users (fn, email, recoveryEmail, password, username, name, lastname, role) VALUES (:fn, :email, :recoveryEmail, :password, :username, :name, :lastname, :role)");
            
            $fn = $user->fn();
            $email = $user->email();
            $recoveryEmail = $user->recoveryEmail();
            $password = $user->password();
            $username = $user->username();
            $name = $user->name();
            $lastname = $user->lastname();
            $role = $user->role();
            
            $stmt->bindParam(':fn', $fn);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':recoveryEmail', $recoveryEmail);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':role', $role);
            
            if ($stmt->execute()) {
                return [
                    'id' => $this->conn->lastInsertId(),
                    'fn' => $fn,
                    'email' => $email,
                    'recoveryEmail' => $recoveryEmail,
                    'password' => $password,
                    'username' => $username,
                    'name' => $name,
                    'lastname' => $lastname,
                    'role' => $role
                ];
            }
        }

        public function update($username, $user) {
            $stmt = $this->conn->prepare("UPDATE users SET fn = :fn, email = :email, recoveryEmail = :recoveryEmail, password = :password, name = :name, lastname = :lastname, role = :role WHERE username = :username");
            
            $fn = $user['fn'];
            $email = $user['email'];
            $recoveryEmail = $user['recoveryEmail'];
            $password = $user['password'];
            $username = $user['username'];
            $name = $user['name'];
            $lastname = $user['lastname'];
            $role = $user['role'];
            
            $stmt->bindParam(':fn', $fn);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':recoveryEmail', $recoveryEmail);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':role', $role);
            
            return $stmt->execute();
        }

        public function remove($username) {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            return $stmt->execute();
        }

        public function exists($username) {
            return !is_null($this->get($username));
        }
    }
?>