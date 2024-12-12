<?php

require_once '../db/conn.php';
require_once 'session.php';
require_once 'auth_check.php';

const DOWNLOAD_DIR = '../uploads/long_novels/';

if (isset($_GET['file'])) {

    $filename = basename($_GET['file']);
    $filepath = DOWNLOAD_DIR . $filename;

    if (file_exists($filepath)) {

        // Verifica il tipo MIME del file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        // Controlla che il tipo MIME sia 'application/pdf'
        if ($mime === 'application/pdf') {
            // Imposta gli header per il download
            header('Content-Description: File Transfer');
            header('Content-Type: $mime');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            // Pulisce il buffer di output e legge il file
            flush();
            readfile($filepath);
            exit;
        } else {
            die("The file is not a valid PDF file.");
        }
    } else {
        die("File not found.");
    }
}

?>