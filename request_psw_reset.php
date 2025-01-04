<?php

require_once 'db/conn.php';
require_once 'incl/session.php';
require 'libs/PHPMailer-master/src/PHPMailer.php';
require 'libs/PHPMailer-master/src/Exception.php';
require 'libs/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

$title = "Send reset password login";

if (isset($_SESSION['usr'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //** Check CSRF Token */
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logEvent('Request PSW Reset', 'Insuccess - invalid token', '');
        die('Invalid CSRF token');
    }
    refreshToken();

    //** Check Timestamp */
    if (!isset($_POST['timestamp']) || !isTimestampValid($_POST['timestamp'])) {
        logEvent('Request PSW Reset', 'Insuccess - expired timestamp', '');
        die('Request expired. Retry later.');
    }

    try {
        //** Check credentials - Fail-Open Flaws */ 
        //is_string() returns true if argument is set, is not null, and is a string it returns false in all the other cases
        if (!is_string($_POST["email"])) {
            logEvent('Request PSW Reset', 'Insuccess - missing credentials', $email);
            http_response_code(401); // Unauthorized
            die('Missing or wrong-format credentials.');
        }

        $email = $_POST['email'];
        $user = $User->getUserByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32)); // Genera un token sicuro
            $expires = time() + 3600; // 1 ora di validità

            if ($Token->insertTokenPswRst($user['ID'], $token, $expires)) {

                // Genera il link di reset
                $resetLink = "https://localhost/SNH_WebProj_Novels/psw_reset.php?token=" . urlencode($token);

                try {
                    // Configurazione server SMTP
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;                                                                     // Autenticazione SMTP
                    $mail->Host = 'smtp.sendgrid.net';                                                          // Server SMTP (modifica se usi un altro provider)
                    $mail->Username = $ENV['username'];                                                                 // Email del mittente
                    $mail->Password = $ENV['password'];       // Password o App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                                         // Crittografia TLS
                    $mail->Port = 587;                                                                          // Porta SMTP (usa 465 per SSL)
                    $mail->SMTPDebug = 3;                                                                       // Livello precisione debug
                    //$mail->Debugoutput = 'html';                                                              // [DEBUG] Output leggibile nel browser
                    $mail->setFrom($ENV['from'], 'Read Novels');                          // Mittente
                    $mail->addAddress($email, $user['NAME'] . ' ' . $user['SURNAME']);           // Destinatario
                    $mail->isHTML(true);                                                                // Abilita HTML
                    $mail->Subject = 'Password Reset Request - Read Novels';                                    // Oggetto
                    $mail->Body =                                                                               // Corpo HTML
                        '<h1>Click the link below to reset your password:</h1><br>
                        <a href="' . $resetLink . '">Reset password</a><br>';

                    $mail->send();                                                                              // Invio email
                    redirect(2);
                } catch (Exception $e) {
                    redirect(1);
                }
            } else {
                redirect(2);
            }
        } else {
            redirect(2);
        }
    } catch (Exception $e) {
        http_response_code(403); // Forbidden Error 
        die('Some error occured. Denied access.');
    }

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once 'incl/bootstrap.php'; ?>
    <title><?= $title ?></title>
</head>

<body>
    <div class="container title text-center">
        <h1 class="text-center">
            <?= $title ?><br />
        </h1>
    </div>

    <div class="container p-5 border border-light border-3 rounded-3 shadow">
        <p><a href="index.php">Homepage</a></p>
        <h3 class="text-center">Enter your email to receive a password reset link</h3><br>
        <form method="POST" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>">
            <div class="form-floating">
                <input required type="email" class="form-control" id="email" name="email" placeholder="Email">
                <label for="email" style="color:black">Email</label>
            </div><br>
            <div class="d-grid gap-2 col-6 mx-auto">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'],ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="timestamp" value="<?= time() ?>">
                <button type="submit" class="btn btn-outline-dark" name="submitBTN">SEND RESET LINK</button><br>
            </div>
        </form>
    </div>
</body>

</html>