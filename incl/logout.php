<?php

require_once 'session.php';
require_once __DIR__ . '/../incl/utils.php';

logEvent('Logout', '', $_SESSION['usr']);

session_unset();
session_destroy();

// Delete the remember me cookie if it exists
if (isset($_COOKIE['REMEMBER_ME'])) {
    setcookie('REMEMBER_ME', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true, // Requires HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

}

header('Location: ../index.php');
exit;

?>