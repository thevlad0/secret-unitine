<?php
    require_once __DIR__ . '/storage/UserStorage.php';

    require_once __DIR__ . '/services/search.php';

    class UserController {
        private $userStorage;

        public function __construct() {
            $this->userStorage = new UserStorage();
        }

        public function search($term) {
            return handleSearch($term, $this->userStorage);
        }
    }
?>