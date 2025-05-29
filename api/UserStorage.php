<?php

    require_once('db.php');
    require_once('user.php');

        const EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';     
        const MAX_USERS = 1000;
        const PASSWORD_REGEX ='/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';  //sample regexes, might change later
        const USERNAME_REGEX = '/^[a-zA-Z0-9_]{3,20}$/';       
        const NAME_REGEX = '/^[a-zA-Z]{2,}$/';

        class UserStorage {                    
        private $loggedInUsers;
        private $db;


        function __construct() {
            $loggedInUsers = new ArrayObject();
            $db = new DB();
        }

        
       function validateUserData($userData) {
        if (!$userData || !isset($userData["username"]) ||
            !isset($userData["email"]) || !isset($userData["password"])) {
            return ["isValid" => false, "message" => "Некоректни данни!"];
        }

        if (!preg_match(EMAIL_REGEX , $userData["email"])) {
            return ["isValid" => false, "message" => "Невалиден имейл!"];
        }

        if (!preg_match(PASSWORD_REGEX , $userData["password"])) {
            return ["isValid" => false, "message" => "Невалидна парола!"];
        }
        
        if (!preg_match(USERNAME_REGEX , $userData["username"])) {
            return ["isValid" => false, "message" => "Невалидно потребителско име!"];
        }
        
        if (!preg_match(PASSWORD_REGEX , $userData["name"])) {
            return ["isValid" => false, "message" => "Невалидно име!"];
        }

        if (!preg_match(PASSWORD_REGEX , $userData["surname"])) {
            return ["isValid" => false, "message" => "Невалидно фамилно име!"];
        }

        return ["isValid" => true, "message" => "Данните са валидни!"];
    }

    function getUsersRoleId(PDO $connection, $roleStr) {
        $sql = "SELECT id FROM roles WHERE type = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$roleStr]);
        if ($stmt->rowCount() === 1) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["id"];
        } else {
            throw new Exception("Invalid role");
        }
    }
    
    function registerUser() {

        $userData = json_decode(file_get_contents("php://input"), true);
        $valid = self::validateUserData($userData);

         if ($valid["isValid"]) {
             $userData["password"] = password_hash(
                $userData["password"], PASSWORD_DEFAULT);

        try {
                $conn = self::$db->getConnection();
                $sql = "INSERT INTO users (name, surname, 
                username, email, password, roles_id) VALUES (?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                $stmt->execute([$userData["name"], $userData["surname"],
                 $userData["username"], $userData["email"],
                            $userData["password"], 
                            self::getUsersRoleId($conn, $userData["roleStr"])]);

                echo json_encode(["status" => "SUCCES", 
                "message" => "Регистрацията е успешна"]);
 
         } catch (PDOException $e) {
            http_response_code(500);

            if ($e->errorInfo[1] === 1062) {
                echo json_encode(["message" => "Имейлът вече съществува!"]);
            } else {
                echo json_encode(["message" => "Грешка при регистрация!"]);
            }
        }
        catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Грешка при регистрация!"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => $valid["message"]]);
    }

}

    function getUserFromDB($userData) {
         try {
            $connection = self::$db->getConnection();
            $sql = "SELECT * from users where email = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$userData["email"]]);

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
                
                $isPasswordValid = password_verify($userData["password"], $user["password"]);
                if ($isPasswordValid) {
                    return $user;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } catch (PDOException $exc) {
            throw new Error($exc->getMessage());
        }
    }
         
    function login($email, $password) {

    $userData = json_decode(file_get_contents("php://input"), true);

    if ($userData && isset($userData["email"]) && isset($userData["password"])) {

        try {
            $user = self::getUserFromDB($userData);
            if (!$user) {
                http_response_code(400);
                exit(json_encode(["message" => "Входът е неуспешен"]));
            }

            session_start();

            $_SESSION["user"] = $user;
            echo json_encode(["message" => "Входът е успешен"]); 

        } catch (Error $exc) {
            http_response_code(500);
            echo json_encode(["message" => "Грешка при вход"]);
        }

    } else {
        http_response_code(400);
        echo json_encode(["message" => "Невалидни данни"]);
    }            
}
    }
    
    
         
     
?>