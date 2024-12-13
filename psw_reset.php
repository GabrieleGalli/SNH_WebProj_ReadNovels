<?php

require_once 'db/conn.php';
require_once 'incl/session.php';

$title = "Send reset password";

if (isset($_SESSION['usr'])) {
    header("Location: dashboard.php");
    exit;
}

$isTokenValid = false;
$TOKEN_RST = sanitize_input($_GET['token']) ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $TOKEN_RST) {
    $isTokenValid = $Token->verifyValidityTknPswReset($_GET['token']);
    if (!$isTokenValid) {
        logEvent('PSW Reset', 'Insuccess - invalid or expired token', '');
        header('Location: index.php?r=' . base64_encode('1'));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['r'])) {
    $r = sanitize_input(base64_decode($_GET['r']));
    if ($r == '1') {
        echo '<div class="alert alert-danger">Password cannot be the same as the previous one!</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //** Check CSRF Token */
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logEvent('PSW Reset', 'Insuccess - invalid token', '');
        die('Invalid CSRF token');
    }
    refreshToken();

    //** Check Timestamp */
    if (!isset($_POST['timestamp']) || !isTimestampValid($_POST['timestamp'])) {
        logEvent('PSW Reset', 'Insuccess - expired timestamp', '');
        die('Request expired. Retry later.');
    }

    try {

        //** Check credentials - Fail-Open Flaws */ 
        //is_string() returns true if argument is set, is not null, and is a string it returns false in all the other cases
        if (!is_string($_POST["rst_token"]) || !is_string($_POST['password1'])) {
            logEvent('PSW Reset', 'Insuccess - missing credentials', '');
            http_response_code(401); // Unauthorized
            die('Missing or wrong-format credentials.');
        }

        $TOKEN_RST = $_POST['rst_token'];
        $newPassword = password_hash($_POST['password1'], PASSWORD_BCRYPT);
        $isTokenValid = $Token->verifyValidityTknPswReset($_POST['rst_token']); // esiste una richiesta di reset della psw

        if (!$isTokenValid) {
            logEvent('PSW Reset', 'Insuccess - invalid or expired token', '');
            redirect(3);
        }

        $userId = $isTokenValid['ID_U'];
        $user = $User->getUserByID($userId);
        $old_psw = $user['PASSWORD'];


        if (password_verify($_POST['password1'], $old_psw)) {
            logEvent('PSW Reset', 'Insuccess - error in updating password, same password', $user);
            header('Location: psw_reset.php?token=' . $TOKEN_RST . '&r=' . base64_encode('1'));
            exit;
        }

        if (!$User->updatePsw($userId, $newPassword)) {
            logEvent('PSW Reset', 'Insuccess - error in updating password', $user);
            redirect(3);
        }

        if (!$Token->deleteTokenPswRst($_POST['rst_token'])) {
            logEvent('PSW Reset', 'Insuccess - error in deleting token', $user);
            redirect(3);
        }

        logEvent('PSW Reset', 'Success', $user['USERNAME']);
        redirect(4);
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
    <script type="text/javascript" src="incl/utils.js"></script>

    <title><?= $title ?></title>
</head>

<body>
    <div class="container title text-center">

        <h1 class="text-center">
            <?= $title ?><br />
        </h1>
    </div>

    <?php if ($isTokenValid) { ?>
        <div class="container p-5 border border-light border-3 rounded-3 shadow">
            <h3 class="text-center">Enter your new password</h3><br>
            <form method="POST" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm()">
                <div class="form-floating">
                    <input required type="password" class="form-control" id="password1" name="password1"
                        placeholder="Password" onchange=checkPasswords()>
                    <label for="password1" style="color:black">New Password</label>
                </div><br>
                <div class="form-floating">
                    <input required type="password" class="form-control" id="password2" name="password2"
                        placeholder="Repeat password" onchange=checkPasswords()>
                    <label for="password2" style="color:black">Repeat password</label>
                </div><br>
                <p id="message"></p>
                <div class="d-grid gap-2 col-6 mx-auto">
                    <input type="hidden" name="rst_token" value="<?= $TOKEN_RST ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="timestamp" value="<?= time() ?>">
                    <button type="submit" class="btn btn-outline-dark" name="submitBTN">UPDATE PASSWORD</button><br>
                </div>
            </form>
        </div>
    <?php } ?>

</body>


</html>