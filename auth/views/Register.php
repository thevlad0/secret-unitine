<?php
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => ['Методът не е разрешен.']]);
    exit;
   }

    require_once __DIR__ . '/../AuthenticationController.php';

    $user = json_decode(file_get_contents('php://input'), true);
    
        $username = $user['username'];
        $email =  $user['email'];
        $password = $user['password'];
        $confirmPassword = $user['confirmPassword'];

        if ($username && $email && $password && $confirmPassword) {
            $authController = new AuthenticationController();

            $result = $authController->register($username, $email, $password, $confirmPassword);   

            if ($result['status'] === 'success') {
                session_start();
                $_SESSION['user'] = $result['user'];
                 echo json_encode(['status' => 'success']);                
                exit();
            } else {
               echo json_encode(['status' => 'error', 'message' => $result['message']]);
               exit();
            }
        }       
?>

