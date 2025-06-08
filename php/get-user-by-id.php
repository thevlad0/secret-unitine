<?php
    $userId = json_decode(file_get_contents("php://input"), true);
    $userStorage = new UserStorage();
    echo json_encode(["username" => $userStorage->getUserById($userId)->getUsername()]);  
?>