<?php

/**
 * This script checks if the current request is using HTTPS.
 * If the request is not using HTTPS, it redirects the user to the HTTPS version of the URL.
 *
 * It checks the 'HTTPS' key in the $_SERVER superglobal array to determine if the request is secure.
 * If the 'HTTPS' key is empty or set to 'off', it constructs the HTTPS URL using the 'HTTP_HOST' and 'REQUEST_URI' keys from the $_SERVER array.
 * The user is then redirected to the constructed HTTPS URL using the header() function.
 * The script terminates execution after the redirect using the exit function.
 * 
 */

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirect");
    exit;
}

?>