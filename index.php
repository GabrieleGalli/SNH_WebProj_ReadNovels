<?php

require_once 'db/conn.php';
require_once 'incl/session.php';
require_once 'incl/utils.php';

$title = "Read Novels Login";

//** SOLO PER TEST - reset dei tentativi registrazione e login impostare cron job altrimenti */
/* $now = time();
$Q = "DELETE FROM log_attempts WHERE TIME < :now";
$stmt = $pdo->prepare($Q);
$stmt->bindParam(":now", $now, PDO::PARAM_INT);
$stmt->execute(); */

if (isset($_SESSION['usr'])) {
    $redirectPage = ($_SESSION['usr'] == 'admin') ? 'controlpanel.php' : 'dashboard.php';
    header("Location: $redirectPage");
    exit;
} elseif (isset($_COOKIE['REMEMBER_ME'])) {
    $token = $_COOKIE['REMEMBER_ME'];
    $isTokenValid = $Token->verifyValidityTknRememberMe($token);

    if ($isTokenValid && password_verify($token, $isTokenValid['TOKEN'])) {
        // Token is valid, log in the user
        $user = $User->getUserById($isTokenValid['ID_U']);
        $_SESSION['usr'] = $user['USERNAME'];
        $_SESSION['id'] = $user['ID'];
        $redirectPage = ($_SESSION['usr'] == 'admin') ? 'controlpanel.php' : 'dashboard.php';
        header("Location: $redirectPage");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['r'])) {
    $r = sanitize_input(base64_decode($_GET['r']));
    if ($r == '0') {
        echo '<div class="alert alert-success">Registration successful! You can now login.</div>';

    } else if ($r == '1') {
        echo '<div class="alert alert-danger">Something went wrong!</div>';
    } else if ($r == '2') {
        echo '<div class="alert alert-danger">Email sent successfully!</div>';
    } else if ($r == '3') {
        echo '<div class="alert alert-danger">Error updating password!</div>';
    } else if ($r == '4') {
        echo '<div class="alert alert-danger">Password updated successfully!</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //** Check CSRF Token */
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logEvent('Login', 'Insuccess - invalid token', '');
        die('Invalid CSRF token');
    }
    //rigenera token

    //** Check Timestamp */
    if (!isset($_POST['timestamp']) || !isTimestampValid($_POST['timestamp'])) {
        logEvent('Login', 'Insuccess - expired timestamp', '');
        die('Request expired. Retry later.');
    }

    //** Check Login Attempts - Account Locking */
    $ip_addr = getIPAddress();
    if (!checkAttempts($pdo, $ip_addr)) {
        logEvent('Login', 'Insuccess - too many tries', '');
        die('Too many tries. Retry later.');
    }

    //** Check Captcha */
    /*
    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = getSecKeyCaptcha();
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseData = json_decode($response);
    if (!$responseData->success) {
        logEvent('Register', 'Insuccess - captcha failed', '');
        die('CAPTCHA verification failed. Try again.');
    }
    */

    try {
        //** Check credentials - Fail-Open Flaws */ 
        //is_string() returns true if argument is set, is not null, and is a string it returns false in all the other cases
        if (!is_string($_POST["username"]) || !is_string($_POST["password"])) {
            logEvent('Login', 'Insuccess - missing credentials', $_POST["username"]);
            http_response_code(401); // Unauthorized
            die('Missing or wrong-format credentials.');
        }

        $username = strtolower(sanitize_input($_POST['username']));
        $password = sanitize_input($_POST['password']);

        if (!$username || !$password) {
            logEvent('Login', 'Insuccess - invalid input data', $_POST["username"]);
            die('Invalid input data.');
        }

        $user = $User->getUserByUsername($username);
        $psw_saved = $user['PASSWORD'];

        if (!$user || !password_verify($password, $psw_saved)) {
            echo '<div class="alert alert-danger">Username or Password is incorrect! Please try again.</div>';
            logAttempt($pdo, $ip_addr); // Registra tentativo fallito
            logEvent('Login', 'Insuccess - incorrect credentials', $_POST['username']);
            exit;
        }

        $_SESSION['usr'] = $user['USERNAME'];
        $_SESSION['id'] = $user['ID'];

        //** Remember me -> Token */
        if (isset($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(32)); // Generate a secure token
            $expires = time() + (3600 * 24 * 30); // Token valid for 30 days

            if ($Token->insertTokenRememberMe($user['ID'], $token, $expires)) {
                setcookie('REMEMBER_ME', $token, [
                    'expires' => $expires,
                    'path' => '/',
                    'secure' => true, // Richiede HTTPS
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }
        }

        logEvent('Login', 'Success', $_SESSION['usr']);
        $redirectPage = ($user['USERNAME'] == 'admin') ? 'controlpanel.php' : 'dashboard.php';
        header("Location: $redirectPage");

        exit;

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
        <form id="myform" method="post" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>">
            <div class="form-floating">
                <input required type="text" class="form-control" id="username" name="username" placehoder="Username"
                    oninput="this.value = this.value.toLowerCase();">
                <label for="username" style="color:black">Username</label>
            </div><br>
            <div class="form-floating">
                <input required type="password" class="form-control" id="password" name="password"
                    placehoder="Password">
                <label for="password" style="color:black">Password</label>
            </div><br>
            <div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha" data-sitekey=<?= getSiteKeyCaptcha() ?>></div>
            </div><br>
            <div class="d-grid gap-2 col-6 mx-auto">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="timestamp" value="<?= time() ?>">
                <button type="submit" class="btn btn-outline-dark" name="submitBTN">ENTER</button><br>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                <label for="remember_me" style="color:black">Remember Me</label>
            </div><br>
        </form>
        <div>
            <p class="text-center">Don't have an account? <a href="register.php">Register</a></p>
            <p class="text-center"><a href="request_psw_reset.php">Forgot password?</a></p>
        </div>
    </div>



</body>


</html>