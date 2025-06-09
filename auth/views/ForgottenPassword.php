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
                header("Location: /" . BASE_PATH . "confirm-reset-password?username=" . urlencode($username));
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
    <base href="<?php echo BASE_URL; ?>">
    <title>Забравена парола</title>
    <link rel="stylesheet" href="auth/views/css/styles.css">
</head>
<body>
    <div class="login-container">
        <form id="forgotten-password-form" method="POST">
            <h2>Възстановяване</h2>
            <p class="form-description">
                Въведете потребителското си име и ние ще ви изпратим инструкции за възстановяване на паролата.
            </p>

            <div class="form-group">
                <label for="username">Потребителско име</label>
                <input id="username" type="text" name="username" required>
            </div>
            
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            
            <button type="submit" class="login-btn">Изпрати код</button>

            <div class="form-footer">
                <a href="login">Върни се към Вход</a>
            </div>
        </form>
    </div>
</body>
</html>