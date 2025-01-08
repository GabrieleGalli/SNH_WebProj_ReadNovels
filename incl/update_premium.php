<?php

require_once '../db/conn.php';
require_once 'session.php';
require_once 'auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the request's body and decode it from JSON 
    $data = json_decode(file_get_contents('php://input'), true);

    //** Check CSRF Token */ 
    if (!isset($data['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
        logEvent('Update Premium', 'Insuccess - invalid token', $_SESSION['usr']);
        die('Invalid CSRF token');
    }

    if (isset($data['premium'])) {
        $premiumStatus = $data['premium'] ? 1 : 0;
        $usr = $data['username'];
        $USER = $User->getUserByUsername($usr);
        $ID = $USER['ID'];
        if ($User->updatePremium($ID, $premiumStatus)) {
            logEvent('Update Premium Reset', 'Success', $_SESSION['usr']);
            echo json_encode(['success' => true]);
        } else {
            logEvent('Update Premium Reset', 'Insuccess - DB error', $_SESSION['usr']);
            echo json_encode(['success' => false, 'error' => 'DB error']);
        }
    } else {
        logEvent('Update Premium Reset', 'Insuccess - invalid data', $_SESSION['usr']);
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
    }
} else {
    http_response_code(405); // Metodo non consentito 
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

?>