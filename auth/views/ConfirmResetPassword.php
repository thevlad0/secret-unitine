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
                header("Location: /" . BASE_PATH . "change-password?username=" . urlencode($username));
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
    <title>Потвърждение с код</title>
    <link rel="stylesheet" href="auth/views/css/styles.css">
</head>
<body>
    <div class="login-container">
        <form id="reset-password-form" method="POST" action="?username=<?php echo htmlspecialchars(urlencode($username)); ?>">
            <h2>Потвърждение с код</h2>
            <p class="form-description">
                Изпратихме код за потвърждение на имейла, свързан с потребителското име <strong><?php echo htmlspecialchars($username); ?></strong>. Моля, въведете го по-долу:
            </p>

            <div class="form-group">
                <label for="code">Код за потвърждение</label>
                <input id="code" class="input-field" type="text" name="code" inputmode="numeric" required>
            </div>

            <?php if (!empty($errors)): ?>
                <p class="error-message"><?php echo htmlspecialchars($errors); ?></p>
            <?php endif; ?>

            <button type="submit" class="login-btn">Потвърди</button>

            <div class="form-footer">
                <p>Не получихте код? <a href="forgotten-password">Изпрати отново</a></p>
            </div>
        </form>
    </div>
</body>
</html>