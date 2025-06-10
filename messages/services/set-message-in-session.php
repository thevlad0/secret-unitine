<?php

    session_start();
    $message = json_decode(file_get_contents('php://input'), true);
    $_SESSION['message'] = $message; 
?>