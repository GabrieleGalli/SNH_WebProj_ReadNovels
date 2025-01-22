<?php

require_once 'session.php';

/*

- trim(): Removes whitespace (and other characters like newline) from the beginning and end of the data.
Example:
Input: " My Novel Title "
Output: "My Novel Title"

- strip_tags(): Removes any HTML or PHP tags from the text.
Example:
Input: "<script>alert('hacked');</script>My Novel"
Output: "My Novel"

- htmlspecialchars(): Converts special characters to HTML entities, preventing XSS (Cross-Site Scripting) attacks.
Example:
Input: "<b>Bold</b> & <script>alert('XSS');</script>"
Output: "&lt;b&gt;Bold&lt;/b&gt; &amp; &lt;script&gt;alert('XSS');&lt;/script&gt;"

*/

function refreshToken()
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Sanitize a given input string by trimming whitespace, stripping HTML tags,
 * and converting special characters to HTML entities.
 *
 * @param string $string The input string to be sanitized.
 * @return string The sanitized string.
 */
function sanitize_input($string)
{
    return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
}

/**
 * Retrieves the client's IP address.
 *
 * This function checks various server variables to determine the client's IP address.
 * It first checks 'HTTP_CLIENT_IP', then 'HTTP_X_FORWARDED_FOR', and finally 'REMOTE_ADDR'.
 *
 * @return string The client's IP address.
 */
function getIPAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function checkAttempts($pdo, $username, $ip_address, $limit = 5, $time_frame = 3600)
{
    // $time_frame = time period for which the account is blocked
    // $time_threshold = remaining time to unlock the account

    $time_threshold = time() - $time_frame;

    $Q = "SELECT COUNT(*) AS N_ATTEMPTS, MAX(TIME) AS last_attempt 
         FROM log_attempts 
         WHERE USERNAME = :usr AND TIME > :time_threshold";
    $stmt = $pdo->prepare($Q);
    $stmt->bindParam(":usr", $username, PDO::PARAM_STR);
    $stmt->bindParam(":time_threshold", $time_threshold, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch();

    if ($result && $result['N_ATTEMPTS'] >= $limit) {
        return false; // Too many attempts on username
    }

    $Q = "SELECT COUNT(*) AS N_ATTEMPTS, MAX(TIME) AS last_attempt 
         FROM log_attempts 
         WHERE IP_ADDR = :ip_addr AND TIME > :time_threshold";
    $stmt = $pdo->prepare($Q);
    $stmt->bindParam(":ip_addr", $ip_address, PDO::PARAM_STR); // IP as string
    $stmt->bindParam(":time_threshold", $time_threshold, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch();

    if ($result && $result['N_ATTEMPTS'] >= $limit) {
        return false; // Too many attempts on IP
    }

    return true;
}

function logAttempt($pdo, $username, $ip_address)
{
    // Auth activities, sensitive transactions, blocked accesses, requests with attacks
    $now = time();
    $Q = "INSERT INTO log_attempts (USERNAME, IP_ADDR, TIME, N_ATTEMPTS) 
        VALUES (:usr, :ip, :now1, 1)
        ON DUPLICATE KEY UPDATE N_ATTEMPTS = N_ATTEMPTS + 1, TIME = :now2";
    $stmt = $pdo->prepare($Q);
    $stmt->bindParam(":usr", $username, PDO::PARAM_STR);
    $stmt->bindParam(":ip", $ip_address, PDO::PARAM_STR);
    $stmt->bindParam(":now1", $now, PDO::PARAM_INT);
    $stmt->bindParam(":now2", $now, PDO::PARAM_INT);
    $stmt->execute();
}

/**
 * Retrieves the secret key for reCAPTCHA from a JSON configuration file.
 *
 * @return string The secret key for reCAPTCHA.
 * @throws Exception If the configuration file is not found, cannot be read, or contains invalid JSON.
 */
function getSecKeyCaptcha()
{
    $path = __DIR__ . '/../incl/reCaptcha.json';
    if (!file_exists($path)) {
        throw new Exception("Configuration file not found: $path");
    }

    $json = file_get_contents($path);
    if ($json === false) {
        throw new Exception("Failed to read configuration file: $path");
    }

    $dati = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Failed to decode JSON: " . json_last_error_msg());
    }

    return $dati['secKey'];
}

/**
 * Retrieves the site key for reCAPTCHA from a configuration file.
 *
 * @return string The site key for reCAPTCHA.
 * @throws Exception If the configuration file is not found, cannot be read, or contains invalid JSON.
 */
function getSiteKeyCaptcha()
{
    $path = __DIR__ . '/../incl/reCaptcha.json';
    if (!file_exists($path)) {
        throw new Exception("Configuration file not found: $path");
    }

    $json = file_get_contents($path);
    if ($json === false) {
        throw new Exception("Failed to read configuration file: $path");
    }

    $dati = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Failed to decode JSON: " . json_last_error_msg());
    }

    return $dati['siteKey'];
}

/**
 * Logs an event to the events log file.
 *
 * @param string $event_type The type of event (e.g., Login, Logout, registration, password change, admin changes, errors).
 * @param string $details Additional details about the event.
 * @param string $usr The username associated with the event.
 *
 * @return void
 */
function logEvent($event_type, $details, $usr)
{
    // Events: Login, Logout, registration, password change, admin changes, errors,

    $logfile = __DIR__ . '/../logs/events.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = getIPAddress() ?? 'unknown';
    $log_entry = "[$timestamp] [$ip] [$usr] [$event_type] $details" . PHP_EOL;

    if (!file_put_contents($logfile, $log_entry, FILE_APPEND)) {
        error_log("Failed to write to log file: $logfile");
    }
}

/**
 * Validates if a given timestamp is within the allowed time range.
 *
 * @param int $timestamp The timestamp to validate.
 * @param int $maxTimeAllowed The maximum allowed time difference in seconds. Default is 180 seconds.
 * @return bool Returns true if the timestamp is within the allowed time range, false otherwise.
 */
function isTimestampValid($timestamp, $maxTimeAllowed = 3600)
{
    $currentTimestamp = time();
    $requestTimestamp = (int) $timestamp;

    // Ensure the timestamp is not in the future
    if ($requestTimestamp > $currentTimestamp) {
        return false;
    }

    return ($currentTimestamp - $requestTimestamp) <= $maxTimeAllowed;
}

/**
 * Redirects the user to the index.php page with a base64 encoded query parameter.
 *
 * @param string $code The code to be base64 encoded and appended as a query parameter.
 */
function redirect($code)
{
    header("Location: index.php?r=" . base64_encode($code));
    exit;
}

?>