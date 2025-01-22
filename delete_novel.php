<?php
require_once 'db/conn.php';
require_once 'incl/session.php';
require_once 'incl/auth_check.php';
require_once 'incl/utils.php';
require_once 'incl/security_headers.php';

const DOWNLOAD_DIR = './uploads/long_novels/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //** Check CSRF Token */
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logEvent('Delete novel', 'Insuccess - invalid token', '');
        die('Invalid or missing CSRF token.');
    }
    refreshToken();

    $novel_id = sanitize_input($_POST['novel_id']);
    $novel_type = sanitize_input($_POST['novel_type']);
    $user = $User->getUserByID($_SESSION['id']);

    if (
        !filter_var($novel_id, FILTER_VALIDATE_INT) ||
        !in_array($novel_type, ['short', 'long']) ||
        !$novel_type
    ) {
        logEvent('Delete novel', 'Insuccess - invalid input data', $user['USERNAME']);
        die('Invalid input data.');
    }

    if ($novel_type === 'short') {
        $novel = $Crud->getShortNovel($novel_id);
    } elseif ($novel_type === 'long') {
        $novel = $Crud->getLongNovel($novel_id);
    }

    //** Access control - only the admin or the user who made the novel can delete it */
    if (!$novel || ($novel['ID_U'] !== $_SESSION['id'] && $_SESSION['usr'] !== 'admin')) {
        logEvent('Delete novel', 'Insuccess - permission denied', $user['USERNAME']);
        die('Permission denied or novel non-existent.');
    }

    if ($novel_type === 'short') {
        $result = $Crud->deleteShortNovel($novel_id);
    } elseif ($novel_type === 'long') {
        $result = $Crud->deleteLongNovel($novel_id);
    }

    if (!$result) {
        logEvent('Delete novel', 'Insuccess - error in deleting', $user['USERNAME']);
        die('Error processing the request.');
    }

    if ($novel_type === 'long') {
        $filename = basename($novel['FILENAME']);
        $filepath = DOWNLOAD_DIR . $filename;
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                error_log("Error in deleting the file: $filepath");
                die('Error deleting file.');
            }
        } else {
            error_log("File not found: $filepath");
            die('File not found.');
        }
    }
    header('Location: dashboard.php?r=' . base64_encode('0'));
    exit;

} else {
    http_response_code(405); // Metodo non consentito
    die('Method not supported.');
}
