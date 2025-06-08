<?php
    class PasswordReset {
        private $username;
        private $resetToken;
        private $expiresAt;

        public function __construct($username, $resetToken, $expiresAt) {
            $this->username = $username;
            $this->resetToken = $resetToken;
            $this->expiresAt = $expiresAt;
        }

        public function username() {
            return $this->username;
        }

        public function resetToken() {
            return $this->resetToken;
        }

        public function expiresAt() {
            return $this->expiresAt;
        }
    }
?>