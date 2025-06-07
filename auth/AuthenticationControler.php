<?php
    session_start();

    require_once __DIR__ . '/services/register.php';
    require_once __DIR__ . '/services/login.php';
    require_once __DIR__ . '/services/logout.php';
    require_once __DIR__ . '/services/reset_password.php';

    class AuthenticationController {
        private $userStorage;

        public function __construct() {
            $this->userStorage = new UserStorage();
        }

        public function login($username, $password) {
            return handleLogin($username, $password, $this->userStorage);
        }

        public function logout() {
            handleLogout();
        }

        public function register($username, $email, $password, $confirm_password) {
            return handleRegister($username, $email, $password, $confirm_password, $this->userStorage);
        }

        public function resetPassword($username, $newPassword) {
            return handleResetPassword($username, $newPassword, $this->userStorage);
        }
    }
?>