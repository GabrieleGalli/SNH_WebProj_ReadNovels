<?php

/**
 * Sets various security headers to enhance the security of the web application.
 *
 * Headers set:
 * - Content-Security-Policy: Defines approved sources for content to prevent XSS attacks.
 *   - default-src: Allows content only from the same origin.
 *   - script-src: Allows scripts from the same origin and specified external sources.
 *   - style-src: Allows styles from the same origin and specified external sources.
 *   - font-src: Allows fonts from the same origin and specified external sources.
 *   - frame-src: Allows frames from specified external sources.
 * - X-Content-Type-Options: Prevents MIME type sniffing.
 * - X-Frame-Options: Prevents the page from being displayed in a frame to avoid clickjacking.
 * - X-XSS-Protection: Enables XSS filtering and prevents rendering of the page if an attack is detected.
 * 
 * Include this script in files that generate HTTP responses (e.g., index.php, dashboard.php), or in API endpoints that return JSON or other data via echo.
 * 
 */

//header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com https://www.googleusercontent.com; style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; frame-src https://www.google.com;");
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

?>