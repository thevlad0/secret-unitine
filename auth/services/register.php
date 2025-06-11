<?php

    
    require_once __DIR__ . '/../models/User.php';
    
    define('PASSWORD_REGEX', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/');

    function __requestUserData($username) {
        $ch = curl_init();

        $url = "../api/user_data_provider.php";   //to check if it works!!!

        $postData = [
            'username' => $username,
        ];

        $jsonData = json_encode($postData);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        
        // Disable SSL verification for testing purposes
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        

        $response = curl_exec($ch);
        $result = null;

        if (!curl_errno($ch)) {
            $result = json_decode($response, true);
        }

        curl_close($ch);

        return $result;
    }

     function __validateUserData($userData) {
        $errors = [];


        if (!$userData || !isset($userData["username"]) || !isset($userData["email"]) || !isset($userData["password"]) || !isset($userData["confirm_password"])) {
            $errors[] = "Липсващи задължителни полета!";
        }

        if (!filter_var($userData["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Невалиден имейл!";
        }

        if (!preg_match(PASSWORD_REGEX, $userData["password"])) {
            $errors[] = "Паролата трябва да съдържа минимум осем символа, измежду които главни, малки букви и цифри!";
        }

        if ($userData["password"] !== $userData["confirm_password"]) {
            $errors[] = "Въведените пароли не съвпадат!";
        }

        if(!empty($errors)) {
            return ["status" => "error", "message" => implode("\n", $errors)];
        }

        return ["status" => "success", "message" => "Данните са валидни!"];
    }


    function handleRegister($username, $email, $password, $confirm_password, $userStorage) {
        if ($userStorage->exists($username)) {
            return [
                'status' => 'error',
                'message' => 'Потребител с това потребителско име вече съществува.'
            ];
        }

        $result = __validateUserData([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirm_password
        ]);

        if ($result['status'] === 'error') {
            return $result;
        }

        $result = __requestUserData($username);

        if ($result['status'] === 'error') {
            return $result;
        }

        $result = $result['data'];

        $user = new User(
            $result['fn'],
            $result['email'],
            $email,
            password_hash($password, PASSWORD_BCRYPT),
            $username,
            $result['name'],
            $result['lastname'],
            $result['role']
        );

        $user = $userStorage->add($user);

        return [
            'status' => 'success',
            'message' => 'Регистрацията е успешна.',
            'user' => $user
        ];
    }

   
    
?>