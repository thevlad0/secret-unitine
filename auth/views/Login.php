<?php
    session_start();

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        if ($email && $password) {
            $authController = new AuthenticationController();

            $result = $authController->login($email, $password);

            if ($result['status'] === 'success') {
                $_SESSION['user'] = $result['user'];
                header("Location: inbox");
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
        <form id="login-form">      
            <label class="input-label" for="email">Имейл:</label>
            <input class="input-field" type="email" id="email" inputmode="email" placeholder="Въведете имейл" required>

            <label class="input-label" for="password">Парола:</label>
            <input class="input-field" type="password" id="password" placeholder="Въведете парола" required>

            <?php
                if (!empty($error)) {
                    echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                }
            ?>
            
            <button class="button" type="submit" id="login-btn">Вход</button>    
   
            <label class="input-label" id="register-label" for="register-btn">Нямаш профил?</label>
            <а id="register-btn">Регистрирай се тук!</а>
        </form>
    </body>
</html>