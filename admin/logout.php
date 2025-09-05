<?php
if (basename(__FILE__) == 'logout.php') {
    $_SESSION = [];
    session_destroy();
    header('Location: ../index.php');
    exit;
}
?>