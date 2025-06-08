<?php
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../AuthenticationController.php';

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newPassword = $_POST['new-password'];
        $newPasswordConfirmation = $_POST['new-password-confirmation'];

        if ($newPassword && $newPasswordConfirmation) {
            $authController = new AuthenticationController();
            $result = $authController->changePassword($username, $newPassword, $newPasswordConfirmation);
        if ($result['status'] === 'success') {
            header("Location: /login");
            exit();
        } else {
            $error = $result['message'];
        }
    }
    } else {
        $error = 'Моля, попълнете всички полета.';
    }
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <form id="change-password" action="" method="POST">"
        <label class="input-label" for="username-field" id="username-label">Промяна на паролата:</label>
        <input class="input-field" type="password" name="new-password" placeholder="Въведете новата си парола" required>
        <input class="input-field" type="password" name="new-password-confirmation" placeholder="Въведете новата си парола" required>

        <?php
            if (!empty($error)) {
                echo '<p class="error">' . htmlspecialchars($error) . '</p>';
            }
        ?>

        <button class="button" type="submit" id="forgotten-password-btn">Промяна на паролата</button>
    </form>
</body>
</html>