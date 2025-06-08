<?php
    function handleLogin($username, $password, $userStorage) {
        if (isset($username) && isset($password)) {
            $user = $userStorage->get($username);
            if ($user && password_verify($password, $user->password())) {
                http_response_code(200);
                return [
                    'status' => 'success',
                    'message' => 'Успешен вход в системата.',
                    'user' => $user
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Потребителското име или паролата са неправилни.'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Не са предоставени потребителско име и парола.'
            ];
        }
    }
?>