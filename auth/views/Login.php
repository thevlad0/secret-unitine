<?php
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../AuthenticationController.php';

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($username && $password) {
            $authController = new AuthenticationController();

            $result = $authController->login($username, $password);

            if ($result['status'] === 'success') {
                $_SESSION['user'] = $result['user'];
                header("Location: /inbox");
                exit();
            } else {
                $error = $result['message'];
            }
        }
    } 
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в системата</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <div class="login-container">
        <form id="login-form" action="" method="POST">
            <h2>Вход</h2>

            <div class="form-group">
                <label for="username">Имейл</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Парола</label>
                <input type="password" id="password" name="password" required>
            </div>

            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            
            <button type="submit" class="login-btn">Вход</button>

            <div class="form-footer">
                <p>Нямате профил? <a href="/register">Регистрация</a></p>
                <a href="/forgotten-password">Забравена парола?</a>
            </div>
        </form>
    </div>

</body>
</html>