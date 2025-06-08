<?php
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../AuthenticationController.php';

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];

        if ($username) {
            $authController = new AuthenticationController();

            $result = $authController->sendResetPasswordEmail($username);

            if ($result['status'] === 'success') {
                header("Location: /confirm-reset-password?username=" . urlencode($username));
                exit();
            } else {
                $error = $result['message'];
            }
        } else {
            $error = 'Не е въведено потребителско име.';
        }
    }
    
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgotten Password</title>
</head>
<body>
    <form id="forgotten-password" method="POST">
        <label class="input-label" for="username" id="username-label">Потребителско име:</label>
        <input class="input-field" type="text" name="username" placeholder="Въведете потребителското си име" required>
        
        <?php
            if (!empty($error)) {
                echo '<p class="error">' . htmlspecialchars($error) . '</p>';
            }
        ?>
        
        <button class="button" type="submit" id="send-code-btn">Изпрати код</button>
    </form>
</body>
</html>