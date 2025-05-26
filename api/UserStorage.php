<?php

    require_once __DIR__ .'/../db/db.php';
    require_once('user/user.php');

        const EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';     
        const MAX_USERS = 1000;
        const PASSWORD_REGEX ='/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';  //sample regexes, might change later
        const USERNAME_REGEX = '/^[a-zA-Z0-9_]{3,20}$/';       
        const NAME_REGEX = '/^[A-Za-z]{2,}$/';   
       
        const FN_REGEX = '(/^[0-9]{1}MI[0-9]{7}$/)|(/^[0-9]{7})';  
        const TEACHER_EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@fmi\.uni-sofia\.bg$/';

        const STUDENT_ROLE = 'Студент';
        const TEACHER_ROLE = 'Преподавател';

        class UserStorage {                   
        
        private $db;


        function __construct() {
           $this->db = new DB();
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
            return ["isValid" => false, "message" => "Невалидна парола!". $userData["password"]];
        }
        
        if (!preg_match(USERNAME_REGEX , $userData["username"])) {
            return ["isValid" => false, "message" => "Невалидно потребителско име!"];  
        
        }        
        if (!preg_match(NAME_REGEX , $userData["name"])) {
            return ["isValid" => false, "message" => "Невалидно име!"];
        }

        if (!preg_match(NAME_REGEX , $userData["surname"])) {
            return ["isValid" => false, "message" => "Невалидно фамилно име!"];
        }

        return ["isValid" => true, "message" => "Данните са валидни!"];
    }

    function handleRoles($userData) {
        if ($userData["role"] === STUDENT_ROLE) {            
            if (!preg_match(FN_REGEX, $userData["fn"])) {  //TO DO: validate fn- regex and existence in the csv file
                return ["isValid" => false, "message" => "Студент с такъв ФН не съществува!"];
            }
        } elseif ($userData["role"] === TEACHER_ROLE) {          
            if (!preg_match(TEACHER_EMAIL_REGEX, $userData["email"])) {   //check for teacher email
                return ["isValid" => false, "message" => "Преподавател с такъв имейл не съществува!"];
            }
        } 
        return ["isValid" => true, "message" => "Валидна потребителска роля!"];
    }

    function getUserCount($conn) {
        $sqlCount = "SELECT COUNT(*) FROM users";
                $stmtCount = $conn->prepare($sqlCount);
                $stmtCount->execute();
            return $stmtCount->fetchColumn();
    } 

    function registerUser() {

        $userData = json_decode(file_get_contents("php://input"), true);
        $valid = $this->validateUserData($userData);
        $validRole = $this->handleRoles($userData);

         if ($valid["isValid"] && $validRole["isValid"]) {

            $userData["password"] = password_hash(
               $userData["password"], PASSWORD_DEFAULT);

        try {
                $conn = $this->db->getConnection();
                $userCount = $this->getUserCount($conn);
                $sql = "INSERT INTO users (id, fn, email, password, username, name, surname, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                $stmt->execute([$userCount + 1, $userData["fn"], $userData["email"], 
                            $userData["password"], $userData["username"],
                            $userData["name"], $userData["surname"],
                            $userData["role"]]);

                http_response_code(200);
                echo json_encode(["message" => "Регистрацията е успешна!"]);
 
         } catch (PDOException $e) {
            http_response_code(500);

            if ($e->errorInfo[1] === 1062) {
                echo json_encode(["message" => $e->getMessage()]);
            } else {
                //echo json_encode(["message" => $e->getMessage()]);
                echo json_encode(["message" => "Грешка при записа!"]);
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
            $connection = $this->db->getConnection();
            $sql = "SELECT * from users where email = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$userData["email"]]);

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
                
              password_verify($userData["password"], $user["password"]);
                              
                if (password_verify($userData["password"], $user["password"])) { //for testing purposes, change to password_verify later
                    unset($user["password"]);
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
         
    function login() {

    $userData = json_decode(file_get_contents("php://input"), true);

    if ($userData && isset($userData["email"]) && isset($userData["password"])) {

        try {
            $user = $this->getUserFromDB($userData);
            if (!$user) {
                http_response_code(400);
                exit(json_encode(["message" => "Потребителят не е намерен!"]));
            }

            session_start();

            $_SESSION["user"] = $user;
            http_response_code(200);
            echo json_encode(["message" => "Входът е успешен"]); 

        } catch (Error $exc) {
            http_response_code(500);
            echo json_encode(["message" => "Грешка при вход!"]);
    }} else {
        http_response_code(400);
        echo json_encode(["message" => "Невалидни данни"]);
    }            
}

    function resetPassword() {
        $userData = json_decode(file_get_contents("php://input"), true);
        if (!$userData || !isset($userData["username"]) || !isset($userData["password"])) {
            http_response_code(400);
            echo json_encode(["message" => "Некоректни данни!"]);
            return;
        }

        if (!preg_match(PASSWORD_REGEX, $userData["password"])) {
            try {
                $conn = $this->db->getConnection();
                $sql = "UPDATE users SET password = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([password_hash($userData["password"], PASSWORD_DEFAULT), $userData["username"]]);

                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Паролата е променена успешно!"]);
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Грешка при промяна на паролата!"]);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["message" => "Грешка при промяна на паролата!"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Невалидни данни!"]);
        }
    }
}
?>