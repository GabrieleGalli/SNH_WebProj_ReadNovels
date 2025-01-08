<?php

require_once 'db/conn.php';
require_once 'incl/session.php';
require_once 'incl/utils.php';

$title = "Read Novels Register";

if (isset($_SESSION['usr'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //** Check CSRF Token */
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logEvent('Register', 'Insuccess - invalid token', '');
        die('Invalid CSRF token');
    }
    refreshToken();

    //** Check Timestamp */
    if (!isset($_POST['timestamp']) || !isTimestampValid($_POST['timestamp'])) {
        logEvent('Register', 'Insuccess - expired timestamp', '');
        die('Request expired. Retry later.');
    }

    //** Check Login Attempts - Account Locking */
    $ip_addr = getIPAddress();
    if (!checkAttempts($pdo, $ip_addr)) {
        logEvent('Register', 'Insuccess - too many tries', $ip_addr);
        die('Too many registration attempts. Retry later.');
    }

    //** Check Captcha */
    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = getSecKeyCaptcha();
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseData = json_decode($response);
    if (!$responseData->success) {
        logEvent('Register', 'Insuccess - captcha failed', '');
        die('CAPTCHA verification failed. Try again.');
    }

    try {
        //** Check credentials - Fail-Open Flaws */ 
        //is_string() returns true if argument is set, is not null, and is a string it returns false in all the other cases
        if (
            !is_string($_POST["username"]) ||
            !is_string($_POST["name"]) ||
            !is_string($_POST["surname"]) ||
            !is_string($_POST["email"]) ||
            !is_string($_POST["password1"]) ||
            !is_string($_POST["password2"])
        ) {
            logEvent('Register', 'Insuccess - missing credentials', $_POST["username"]);
            http_response_code(401); // Unauthorized
            die('Missing or wrong-format credentials.');
        }

        $username = strtolower(sanitize_input($_POST['username']));
        $name = sanitize_input($_POST['name']);
        $surname = sanitize_input($_POST['surname']);
        $email = sanitize_input($_POST['email']);
        $password1 = sanitize_input($_POST['password1']);
        $password2 = sanitize_input($_POST['password2']);
        $premium = isset($_POST['premium']) ? 1 : 0;

        if (
            !$username || !$name || !$surname || !$password1 || !$password2 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            logEvent('Register', 'Insuccess - invalid input data', $_POST["username"]);
            die('Invalid input data.');
        }

        if ($password1 !== $password2) {
            echo '<div class="alert alert-danger">Passwords don\'t match!</div>';
            logEvent('Login', 'Insuccess - incorrect password matching', $_POST['username']);
            logAttempt($pdo, $ip_addr); // register attempt failed
            exit;
        }

        $result = $User->insertUser($premium, $username, $name, $surname, $email, $password1);

        if (!$result) {
            echo '<div class="alert alert-danger">Username or Email already in use.</div>';
            logAttempt($pdo, $ip_addr); // Register attempt failed
            logEvent('Register', 'Insuccess - username or email already in use', $_POST['username']);
        } else {
            logEvent('Register', 'Success', $_POST['username']);
            header('Location: index.php?r=' . base64_encode('0'));
        }

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
    <script type="text/javascript" src="incl/utils.js"></script>

    <title><?= $title ?></title>
</head>

<body>

    <div class="container title text-center">
        <h1 class="text-center">
            <?= $title ?><br />
        </h1>
    </div>

    <div class="container p-5 border border-light border-3 rounded-3 shadow">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm()">
            <div class="form-floating">
                <input required type="text" class="form-control" id="name" name="name" placeholder="Name">
                <label for="name" style="color:black">Name</label>
            </div><br>
            <div class="form-floating">
                <input required type="text" class="form-control" id="surname" name="surname" placeholder="Surname">
                <label for="surname" style="color:black">Surname</label>
            </div><br>
            <div class="form-floating">
                <input required type="email" class="form-control" id="email" name="email" placeholder="Email">
                <label for="email" style="color:black">Email</label>
            </div><br>
            <div class="form-floating">
                <input required type="text" class="form-control" id="username" name="username" placeholder="Username"
                    oninput="this.value = this.value.toLowerCase(); /*checkUsername()*/">
                <label for="username" style="color:black">Username</label>
                <small id="username-status" class="text-danger"></small>
            </div><br>
            <div class="form-floating">
                <input required type="password" class="form-control" id="password1" name="password1"
                    placeholder="Password" onchange=checkPasswords()>
                <label for="password1" style="color:black">Password</label>
            </div><br>
            <div class="form-floating">
                <input required type="password" class="form-control" id="password2" name="password2"
                    placeholder="Repeat password" onchange=checkPasswords()>
                <label for="password2" style="color:black">Repeat password</label>
            </div><br>
            <p id="message"></p>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="premium" name="premium">
                <label class="form-check-label" for="premium" style="color:black">Premium</label>
            </div><br>
            <div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha" data-sitekey=<?= getSiteKeyCaptcha() ?>></div>
            </div><br>
            <div class="d-grid gap-2 col-6 mx-auto">
                <input type="hidden" name="timestamp" value="<?= time() ?>">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-outline-dark" name="submitBTN">REGISTER</button><br>
            </div>
        </form>
        <div>
            <p class="text-center">Already have and account? <a href="index.php">Log in</a></p>
        </div>
    </div>

</body>

</html>