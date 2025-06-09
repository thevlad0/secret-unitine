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
            header("Location: " . BASE_PATH . "/login");
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
    <base href="<?php echo BASE_URL; ?>">
    <title>Смяна на парола</title>
    <link rel="stylesheet" href="/auth/views/css/styles.css">
</head>
<body>
    <div class="login-container">
        <form id="change-password-form" action="?username=<?php echo htmlspecialchars(urlencode($username)); ?>" method="POST">
            <h2>Нова парола</h2>
            <p class="form-description">
                Въведете новата си парола за потребител <strong><?php echo htmlspecialchars($username); ?></strong>.
            </p>

            <div class="form-group">
                <label for="new-password">Нова парола</label>
                <input id="new-password" type="password" name="new-password" placeholder="Въведете новата парола" required>
            </div>
            
            <div class="form-group">
                <label for="new-password-confirmation">Потвърдете новата парола</label>
                <input id="new-password-confirmation" type="password" name="new-password-confirmation" placeholder="Потвърдете паролата отново" required>
            </div>

            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <button type="submit" class="login-btn">Смени паролата</button>
        </form>
    </div>
</body>
</html>