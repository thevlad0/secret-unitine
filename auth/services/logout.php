<?php
    function handleLogout() {
        // Start the session if it hasn't been started yet
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header("Location: /" . BASE_PATH . "login");
        exit();
    }
?>