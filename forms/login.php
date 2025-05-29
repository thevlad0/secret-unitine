<?php
    require_once __DIR__ . '/../api/UserStorage.php';

        $userStorage = new UserStorage();
        echo $userStorage->login();       
?>