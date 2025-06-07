<?php
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
                    $user_data['password'],
                    $user_data['username'],
                    $user_data['name'],
                    $user_data['surname'],
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
                $user_data['password'],
                $user_data['username'],
                $user_data['name'],
                $user_data['surname'],
                $user_data['role']
            );
        }

        public function add($user) {
            $stmt = $this->conn->prepare("INSERT INTO users (fn, email, password, username, name, surname, role) VALUES (:fn, :email, :password, :username, :name, :surname, :role)");
            
            $stmt->bindParam(':fn', $user->fn());
            $stmt->bindParam(':email', $user->email());
            $stmt->bindParam(':password', $user->password());
            $stmt->bindParam(':username', $user->username());
            $stmt->bindParam(':name', $$user->name());
            $stmt->bindParam(':surname', $$user->surname());
            $stmt->bindParam(':role', $$user->role());
            
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