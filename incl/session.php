<?php

/**
 * This script initializes and manages the session settings for the web application.
 * 
 * It performs the following tasks:
 * - Requires the HTTPS check script to ensure the connection is secure.
 * - Sets the session garbage collection maximum lifetime to 3600 seconds (1 hour).
 * - Ensures the session cookie is only sent over HTTPS connections.
 * - Starts the session.
 * - Generates a CSRF token if it does not already exist in the session.
 * - Regenerates the session ID to prevent session fixation attacks.
 * 
 */

require_once __DIR__ . '/../incl/https_check.php';
require_once __DIR__ . '/../incl/security_headers.php';

ini_set('session.gc_maxlifetime', 3600); // 1 ora
ini_set('session.cookie_secure', '1'); // Cookie della sessione solo su HTTPS

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

session_regenerate_id(true);

?>