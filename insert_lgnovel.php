<?php

require_once 'db/conn.php';
require_once 'incl/session.php';
require_once 'incl/auth_check.php';
require_once 'incl/utils.php';

$USER = $User->getUserByUsername($_SESSION['usr']);

$title = "Insert a new long novel";
$premium = $_SESSION['premium_usr'];
$id_u = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //** Check CSRF Token */
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logEvent('Insert LG Novel', 'Insuccess - invalid token', '');
        die('Invalid CSRF token');
    }
    refreshToken();

    $novel_title = sanitize_input($_POST['title']);
    $premium_set = isset($_POST['premium']) ? 1 : 0;
    $file = $_FILES['pdf'];

    if ($novel_title == '' || $file['error'] !== UPLOAD_ERR_OK) {
        echo '<div class="alert alert-danger">Please provide a title and upload a valid PDF file.</div>';
    } else if ($premium_set == 1 && $premium == 0) {
        //** Access control */
        echo '<div class="alert alert-danger">You cannot insert a premium novel if you are not a premium user.</div>';
    } else {
        // Check that the file is a PDF
        $fileType = mime_content_type($file['tmp_name']);
        $allowed_extensions = ['pdf'];
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($fileType !== 'application/pdf' || !in_array(strtolower($file_extension), $allowed_extensions)) {
            logEvent('Insert LG Novel', 'Insuccess - invalid file extension', '');
            echo '<div class="alert alert-danger">Only PDF files are allowed.</div>';
        } else {
            if ($file['size'] > 5 * 1024 * 1024) { // Check max size of file: 5 MB
                logEvent('Insert LG Novel', 'Insuccess - exceeded the maximum file size', '');
                echo '<div class="alert alert-danger">File is too large. Maximum allowed size is 5 MB.</div>';
            } else {
                $targetDir = 'uploads/long_novels/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Generate unique name for the file and move it to the target directory. We use basename in order to prevent 
                // directory traversal attacks. 
                $fileName = uniqid() . '_' . basename($file['name']);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    // Save information on the db
                    $result = $Crud->createLongNovel($id_u, $premium_set, $novel_title, $fileName);
                    if ($result) {
                        echo '<div class="alert alert-success">Long novel uploaded successfully.</div>';
                        logEvent('Insert LG Novel', 'Success - long novel uploaded successfully', '');

                    } else {
                        echo '<div class="alert alert-danger">There was an error saving the novel.</div>';
                        logEvent('Insert LG Novel', 'Insuccess - error saving the novel', '');
                    }
                } else {
                    echo '<div class="alert alert-danger">Failed to upload the file.</div>';
                    logEvent('Insert LG Novel', 'Insuccess - failed to upload the file', '');
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once 'incl/bootstrap.php'; ?>
    <title><?= $title ?></title>
</head>

<body>
    <div class="container title text-center">
        <h1 class="text-center">
            <?= $title ?><br />
        </h1>
    </div>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Welcome <?= sanitize_input($USER['NAME']) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                    <a class="nav-link" href="insert_shnovel.php">Insert a new short novel</a>
                    <a class="nav-link active" aria-current="page" href="#">Insert a new long novel</a>
                    <a class="nav-link" href="incl/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container p-5 border border-light border-3 rounded-3 shadow">
        <form method="post" enctype="multipart/form-data" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>">
            <div class="form-floating">
                <input required type="text" class="form-control" id="title" name="title" placeholder="Title">
                <label for="title" style="color:black">Title</label>
            </div><br>
            <div class="form-group">
                <label for="pdf" style="color:black">Upload PDF</label>
                <input required type="file" class="form-control" id="pdf" name="pdf" accept=".pdf">
            </div><br>
            <?php if ($premium == 1) { ?>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="premium" name="premium">
                    <label class="form-check-label" for="premium" style="color:black">Premium</label>
                </div><br>
            <?php } ?>
            <div class="d-grid gap-2 col-6 mx-auto">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-outline-dark" name="submitBTN">UPLOAD NEW LONG NOVEL</button><br>
            </div>
        </form>
    </div>
</body>

</html>