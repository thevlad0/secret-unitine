<?php
    header('Content-Type: application/json; charset=utf-8');

    const USER_DATA_FILE = __DIR__ . '/../util/data/users.csv';

    function getUserData($file_name, $username) {
        $userData = [];

        if (file_exists($file_name)) {
            if (($handle = fopen($file_name, "r")) !== FALSE) {
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

    $input = json_decode(file_get_contents('php://input'), true);

    $userData = getUserData(USER_DATA_FILE, $input['username']);

    if (!empty($userData)) {
        echo json_encode([
            "status" => "success",
            "data" => $userData
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Потребителят не е намерен."
        ], JSON_UNESCAPED_UNICODE);
    }
?>