<?php
    class User {
        private $fn;
        private $email;
        private $recoveryEmail;
        private $password;
        private $username;
        private $name;
        private $lastname;
        private $role;
            
        public function __construct($fn, $email, $recoveryEmail, $password, $username, $name, $lastname, $role) {
            $this->fn = $fn;
            $this->email = $email;
            $this->recoveryEmail = $recoveryEmail;
            $this->password = password_hash($password, PASSWORD_BCRYPT);
            $this->username = $username;
            $this->name = $name;
            $this->surname = $lastname;
            $this->role = $role;
        }

        public function fn() {
            return $this->fn;
        }
        
        public function email() {
            return $this->email;
        } 

        public function recoveryEmail() {
            return $this->recoveryEmail;
        }
        
        public function username() {
            return $this->username;
        }

        public function name() {
            return $this->name;
        }

        public function lastname() {
            return $this->lastname;
        }
        
        public function role() {
            return $this->role;
        }

        public function toDTO() {
            return new UserDTO(
                $this->email,
                $this->name . ' ' . $this->lastname,
                $this->role
            );
        }
    }

    class UserDTO {
        private $email;
        private $name;
        private $role;

        public function __construct($email = '', $name = '', $role = '') {
            $this->email = $email;
            $this->name = $name;
            $this->role = $role;
        }

        public function email() {
            return $this->email;
        }

        public function name() {
            return $this->name;
        }

        public function role() {
            return $this->role;
        }
    }
?>