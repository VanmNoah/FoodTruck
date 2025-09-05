<?php
session_start();

if (!isset($_POST['verification_code'])) {
    header('Location: login.php?error=Please enter a verification code');
    exit;
}

if (!isset($_SESSION['verification_code']) || !isset($_SESSION['verification_code_expiry'])) {
    header('Location: login.php?error=Verification code has expired. Please request a new one');
    exit;
}

if (time() > $_SESSION['verification_code_expiry']) {
    unset($_SESSION['verification_code']);
    unset($_SESSION['verification_code_expiry']);
    header('Location: login.php?error=Verification code has expired. Please request a new one');
    exit;
}

if ($_POST['verification_code'] === $_SESSION['verification_code']) {
    $_SESSION['logged_in'] = true;
    unset($_SESSION['verification_code']);
    unset($_SESSION['verification_code_expiry']);
    unset($_SESSION['demo_code']);
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php?error=Invalid verification code. Please try again');
    exit;
}
?>
