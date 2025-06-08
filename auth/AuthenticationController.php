<?php
    require_once __DIR__ . '/storage/UserStorage.php';
    require_once __DIR__ . '/storage/PasswordResetStorage.php';

    require_once __DIR__ . '/services/register.php';
    require_once __DIR__ . '/services/login.php';
    require_once __DIR__ . '/services/logout.php';
    require_once __DIR__ . '/services/reset_password.php';

    class AuthenticationController {
        private $userStorage;
        private $passwordResetStorage;

        public function __construct() {
            $this->userStorage = new UserStorage();
            $this->passwordResetStorage = new PasswordResetStorage();
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

        public function sendResetPasswordEmail($email) {
            return handleSendResetPasswordEmail($email, $this->userStorage, $this->passwordResetStorage);
        }

        public function confirmResetPassword($username, $newPassword) {
            return handleConfirmResetPassword($username, $this->passwordResetStorage);
        }

        public function changePassword($username, $newPassword, $confirmPassword) {
            return handleChangePassword($username, $newPassword, $confirmPassword, $this->userStorage);
        }
    }
?>