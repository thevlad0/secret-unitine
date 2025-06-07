<?php
    define('PASSWORD_REGEX', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/');

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

        $result = __requestUserData();

        if ($result['status'] === 'error') {
            return $result;
        }

        $user = new User(
            $result['fn'],
            $result['email'],
            $email,
            $password,
            $username,
            $result['name'],
            $result['lastname'],
            $result['role']
        );

        $userStorage->add($user);

        return [
            'status' => 'success',
            'message' => 'Регистрацията е успешна.',
        ];
    }

    function __validateUserData($userData) {
        $errors = [];

        if (!$userData || !isset($userData["username"]) || !isset($userData["email"]) || !isset($userData["password"])) {
            $errors[] = "Липсващи задължителни полета!";
        }

        if (!filter_var($userData["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Невалиден имейл!";
        }

        if (!preg_match(PASSWORD_REGEX, $userData["password"])) {
            $errors[] = "Липсващи задължителни полета!";
        }

        if ($userData["password"] !== $userData["confirm_password"]) {
            $errors[] = "Въведените пароли не съвпадат!";
        }

        if(!empty($errors)) {
            return ["status" => "error", "message" => implode("\n", $errors)];
        }

        return ["status" => "success", "message" => "Данните са валидни!"];
    }

    function __requestUserData() {
        $ch = curl_init();

        $url = "https://localhost:8000/api/user_data_provider.php";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (!curl_errno($ch)) {
            $result = json_decode($response, true);
        }

        curl_close($ch);

        return $result;
    }
?>