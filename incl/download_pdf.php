<?php

require_once '../db/conn.php';
require_once 'session.php';
require_once 'auth_check.php';

const DOWNLOAD_DIR = '../uploads/long_novels/';

if (isset($_GET['file'])) {
    // Get the file name from the URL and use basename to prevent directory traversal attacks
    $filename = basename($_GET['file']);
    $filepath = DOWNLOAD_DIR . $filename;

    $novel = $Crud->getLongNovelByFilename($filename);

    //** Access control */
    if ($novel['PREMIUM'] == 1) {
        // Check if the user is premium
        $USER = $User->getUserByUsername($_SESSION['usr']);
        if ($USER['PREMIUM'] == 0) {
            die("You need to be a premium user to download this file.");
        }
    }

    if (file_exists($filepath)) {

        // Check the file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        // Check that MIME type is 'application/pdf'
        if ($mime === 'application/pdf') {
            // Set the headers for the file download
            header('Content-Description: File Transfer');
            header('Content-Type: $mime');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            // Flushes the output buffer and sends the file to the user
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