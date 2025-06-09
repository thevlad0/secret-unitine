<?php
    function handleSendResetPasswordEmail($username, $userStorage, $passwordResetStorage) {
        if (!$userStorage->exists($username)) {
            return [
                'status' => 'error',
                'message' => 'Потребител с това потребителско име не съществува.'
            ];
        }

        $user = $userStorage->get($username);

        $resetToken = bin2hex(random_bytes(3));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+20 minutes'));
        
        $passwordReset = new PasswordReset($username, $resetToken, $expiresAt);

        $result = __sendEmail($user['recoveryEmail'], $resetToken);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Грешка при изпращане на имейл.'
            ];
        } else {
            $passwordResetStorage->add($passwordReset);
            return [
                'status' => 'success',
                'message' => 'Имейлът с кода за смяна на парола е изпратен.'
            ];
        }
    }

    function handleConfirmResetPassword($username, $passwordResetStorage) {
        $passwordReset = $passwordResetStorage->get($username);

        if (!$passwordReset) {
            return [
                'status' => 'error',
                'message' => 'Няма заявка за смяна на парола.'
            ];
        }

        $currentTime = date('Y-m-d H:i:s');
        if ($currentTime > $passwordReset['expiresAt']) {
            return [
                'status' => 'error',
                'message' => 'Кодът е изтекъл.'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Паролата е успешно сменена.'
        ];

    }

    function handleChangePassword($username, $newPassword, $confirm_password, $userStorage) {
        if (!$userStorage->exists($username)) {
            return [
                'status' => 'error',
                'message' => 'Потребител с това потребителско име не съществува.'
            ];
        }

        $user = $userStorage->get($username);

        if (!preg_match(PASSWORD_REGEX, $newPassword)) {
            return [
                'status' => 'error',
                'message' => 'Паролата трябва да съдържа минимум осем символа, измежду които главни, малки букви и цифри.'
            ];
        }

        if ($newPassword !== $confirm_password) {
            return [
                'status' => 'error',
                'message' => 'Паролите не съвпадат.'
            ];
        }

        $user['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        $userStorage->update($user['username'], $user);

        return [
            'status' => 'success',
            'message' => 'Паролата е успешно сменена.'
        ];
    }

    function __sendEmail($to, $token) {
        $subject = 'Смяна на парола Secret-Unitine';

        $message = 'Вашият код е: ' . $token . "\r\n" .
                   'Кодът е валиден за 20 минути.' . "\r\n";

        $headers = 'From: 8f228a001@smtp-brevo.com' . "\r\n" .
                'Reply-To: 8f228a001@smtp-brevo.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            return true;
        }

        return false;
    }
?>