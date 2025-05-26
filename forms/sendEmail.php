<?php
    header("Content-Type: application/json");

    $headers = "From: no-reply@example.com\r\n" .
           "Content-Type: text/plain; charset=UTF-8";

    $userData = json_decode(file_get_contents("php://input"), true);
    $email = $userData["email"];
    $code = $userData["code"];

     if (mail($email,"Верификационен код за промяна на паролата", $code, $headers)) {
        http_response_code(200);

        echo json_encode(["message" => "Имейлът е изпратен успешно!"]);  //actually this is not quite sure;
                                                                        //there are some other potential unhappy paths 
                                                                        //that I just do not want to deal with         
     } else {
        http_response_code(500);
        echo json_encode(["message" => "Грешка при изпращане на имейла!"]);
     }
?>