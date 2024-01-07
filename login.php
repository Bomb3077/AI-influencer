<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    // Validate user's login (This should be your actual user authentication logic)
    if ($_POST['login'] && $_POST['password']) {
        // Capture login information
        $_SESSION['login'] = $_POST['login']??null; 
        $_SESSION['password'] = $_POST['password']??null;
        echo "Logged in successfully";
    } else {
        // Handle error: login information is incomplete or incorrect
        echo "Invalid login or password!";
    }
}
