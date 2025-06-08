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

<html> 
    <meta charset="UTF-8">
    <head> 
        <title>Login</title> 
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body> 
        <form id="login-form" action="" method="POST">      
            <label class="input-label" for="username">Имейл:</label>
            <input class="input-field" type="text" name="username" placeholder="Въведете имейл" required>

            <label class="input-label" for="password">Парола:</label>
            <input class="input-field" type="password" name="password" placeholder="Въведете парола" required>

            <?php
                if (!empty($error)) {
                    echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                }
            ?>
            
            <button class="button" type="submit" id="login-btn">Вход</button> 

            <a id="forgotten-password" href="#/forgotten-password">Забравена парола?</a>
            <p class="label" id="register-label" for="register-link">Нямаш профил?</п>
            <a id="register-link" href="#/register">Регистрирай се тук!</a>
        </form>
    </body>
</html>