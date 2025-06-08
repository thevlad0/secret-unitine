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
                header("Location: /inbox");
                exit();
            } else {
                $errors = $result['message'];
                echo $result['message'];
            }
        }
    }
?>

<html> 
    <meta charset="UTF-8">
    <head> 
        <title>Register</title> 
        <link rel="stylesheet"  href="registration.css">
    </head>
    <body> 
        <form id="register-form" action="" method="POST">
            <label class="input-label" for="username">Потребителско име:</label>
            <input class="input-field" type="text" name="username" placeholder="Въведете потребителско име" required>

            <label class="input-label" for="email">Имейл:</label>
            <input class="input-field" type="email" name="email" inputmode="email" placeholder="Въведете имейл" required>

            <label class="input-label" for="password">Парола:</label>
            <input class="input-field" type="password" name="password" placeholder="Въведете парола" required>
            
            <label class="input-label" for="confirm-password">Потвърди паролата:</label>
            <input class="input-field" type="password" name="confirm-password" placeholder="Потвърдете паролата" required>

            <?php
                if (!empty($errors)) {
                    $errorStr = '';
                    foreach ($errors as $error) {
                        $errorStr .= htmlspecialchars($error) . '<br>';
                    }
                    echo '<p class="error">' . $errorStr . '</p>';
                }
            ?>

            <button type="submit" id="register-btn">Регистрация</button>            
        </form>
    </body>
</html>