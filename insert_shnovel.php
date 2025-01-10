<?php

require_once 'db/conn.php';
require_once 'incl/session.php';
require_once 'incl/auth_check.php';
require_once 'incl/utils.php';

$USER = $User->getUserByUsername($_SESSION['usr']);

$title = "Insert a new short novel";
$premium = $_SESSION['premium_usr'];
$id_u = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //** Check CSRF Token */
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logEvent('Insert SH Novel', 'Insuccess - invalid token', '');
        die('Invalid CSRF token');
    }
    refreshToken();

    $novel_title = sanitize_input($_POST['title']);
    $novel_content = sanitize_input($_POST['content']);
    $premium_set = isset($_POST['premium']) ? 1 : 0;

    if ($novel_title == '' || $novel_content == '') {
        echo '<div class="alert alert-danger">Please fill in all the fields.</div>';
    } else if ($premium_set == 1 && $premium == 0) {
        //** Access control */
        echo '<div class="alert alert-danger">You cannot insert a premium novel if you are not a premium user.</div>';
    } else {
        $result = $Crud->createShortNovel($id_u, $premium_set, $novel_title, $novel_content);
        if ($result) {
            echo '<div class="alert alert-success">Novel inserted successfully.</div>';
        } else {
            echo '<div class="alert alert-danger">There was an error inserting the novel.</div>';
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
                    <a class="nav-link active" aria-current="page" href="#">Insert a new short novel</a>
                    <a class="nav-link" href="insert_lgnovel.php">Insert a new long novel</a>
                    <a class="nav-link" href="incl/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container p-5 border border-light border-3 rounded-3 shadow">
        <form method="post" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>">
            <div class="form-floating">
                <input required type="text" class="form-control" id="title" name="title" placeholder="Title">
                <label for="title" style="color:black">Title</label>
            </div><br>
            <div class="form-floating">
                <textarea required type="text" class="form-control" id="content" name="content"
                    placeholder="Content"></textarea>
                <label for="content" style="color:black">Content</label>
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
                <button type="submit" class="btn btn-outline-dark" name="submitBTN">INSERT NEW NOVEL</button><br>
            </div>
        </form>
    </div>
</body>

</html>