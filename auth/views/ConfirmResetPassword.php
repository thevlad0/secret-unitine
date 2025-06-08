<?php
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../AuthenticationController.php';

    $errors = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $code = $_POST['code'];

        if ($code) {
            $authController = new AuthenticationController();

            $result = $authController->confirmResetPassword($username, $code);

            if ($result['status'] === 'success') {
                header("Location: /change-password?username=" . urlencode($username));
                exit();
            } else {
                $errors = $result['message'];
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Reset Password</title>
</head>
<body>
    <form id="reset-password" method="POST">
        <label class="input-label" id="code-label">Изпратихме код за потвърждение на имейла Ви. Моля, въведете го по-долу:</label>
        <input class="input-field" type="text" name="code" inputmode="numeric" placeholder="Въведете получения код" required>

        <?php
            if (!empty($errors)) {
                echo '<p class="error">' . htmlspecialchars($errors) . '</p>';
            }
        ?>

        <button class="button" type="submit" id="send-code-btn">Потвърждаване</button>
    </form>
</body>
</html>