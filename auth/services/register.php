<?php

    
    require_once __DIR__ . '/../models/User.php';
    
    const USER_DATA_FILE = __DIR__ . '/../../util/data/users.csv';
    define('PASSWORD_REGEX', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/');

    //problematic function
    function __requestUserData($username) {
       
        $userData = [];

        if (file_exists(USER_DATA_FILE)) {
            if (($handle = fopen(USER_DATA_FILE, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if (!empty($data[0]) && $data[0] === $username) {
                        $userData = [
                            "username" => $data[0],
                            "fn"       => $data[1] ?? "",
                            "email"    => $data[2] ?? "",
                            "name"     => $data[3] ?? "",
                            "lastname" => $data[4] ?? "",
                            "role"     => $data[5] ?? "",
                        ];
                        break;
                    }
                }
                fclose($handle);
            }
        }

        return $userData;
    }
    //end of problematic function

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

        $result = __requestUserData('velinov1');
      
//($fn, $email, $recoveryEmail, $password, $username, $name, $lastname, $role)
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