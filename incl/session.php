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

//** Session Cookie - active for 1h (server side) or until browser is open (client side) */

ini_set('session.gc_maxlifetime', 3600); // Durata massima della sessione di 1 ora
ini_set('session.cookie_secure', '1'); // Cookie solo su HTTPS

session_set_cookie_params([
    "SameSite" => "Strict",   // Protezione da richieste cross-site
    "Secure" => true,       // Solo HTTPS
    "HttpOnly" => true,       // Accessibile solo dal server
    "Path" => "/",        // Valido per tutte le directory
    "Domain" => "",         // Valido solo per il dominio corrente
    "Lifetime" => "0"         // Cookie di sessione
]);


session_start();

if (empty($_SESSION['csrf_token'])) {
    refreshToken();
}

session_regenerate_id(true);

?>