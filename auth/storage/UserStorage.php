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

            $users_data = $stmt->fetchALL(PDO::FETCH_ASSOC);
            $users = [];

            foreach ($users_data as $user_data) {
                $users[] = new User(
                    $user_data['fn'],
                    $user_data['email'],
                    $user_data['recoveryEmail'],
                    $user_data['password'],
                    $user_data['username'],
                    $user_data['name'],
                    $user_data['lastname'],
                    $user_data['role']
                );
            }

            return $users;
        }

        public function get($username) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return null;
            }

            $user_data = $stmt->fetchALL(PDO::FETCH_ASSOC)[0];

            return new User(
                $user_data['fn'],
                $user_data['email'],
                $user_data['recoveryEmail'],
                $user_data['password'],
                $user_data['username'],
                $user_data['name'],
                $user_data['lastname'],
                $user_data['role']
            );
        }

        public function add($user) {
            $stmt = $this->conn->prepare("INSERT INTO users (fn, email, recoveryEmail, password, username, name, surname, role) VALUES (:fn, :email, :recoveryEmail, :password, :username, :name, :surname, :role)");
            
            $fn = $user->fn();
            $email = $user->email();
            $recoveryEmail = $user->recoveryEmail();
            $password = $user->password();
            $username = $user->username();
            $name = $user->name();
            $surname = $user->lastname();
            $role = $user->role();
            
            $stmt->bindParam(':fn', $fn);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':recoveryEmail', $recoveryEmail);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':surname', $surname);
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