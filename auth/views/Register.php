<?php
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../AuthenticationController.php';

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm-password'];

        if ($username && $email && $password && $confirmPassword) {
            $authController = new AuthenticationController();

            $result = $authController->register($username, $email, $password, $confirmPassword);

            if ($result['status'] === 'success') {
                $_SESSION['user'] = $result['user'];
                header("Location: /" . BASE_PATH . "inbox");
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
    <base href="<?php echo BASE_URL; ?>">
    <title>Регистрация</title> 
    <link rel="stylesheet" href="auth/views/css/styles.css">
</head>
<body> 
    <div class="login-container">
        <form id="register-form" action="" method="POST">
            <h2>Създаване на профил</h2>

            <div class="form-group">
                <label for="username">Потребителско име</label>
                <input id="username" type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Имейл</label>
                <input id="email" type="email" name="email" inputmode="email" required>
            </div>

            <div class="form-group">
                <label for="password">Парола</label>
                <input id="password" type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Потвърди паролата</label>
                <input id="confirm-password" type="password" name="confirm-password" required>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php 
                        if (is_array($errors)) {
                            foreach ($errors as $error) {
                                echo htmlspecialchars($error) . '<br>';
                            }
                        } else {
                            echo htmlspecialchars($errors);
                        }
                    ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="login-btn">Регистрация</button>            

            <div class="form-footer">
                <p>Вече имате профил? <a href="login">Вход</a></p>
            </div>
        </form>
    </div>
</body>
</html>