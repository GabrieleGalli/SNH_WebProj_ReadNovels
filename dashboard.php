<?php

require_once 'db/conn.php';
require_once 'incl/session.php';
require_once 'incl/auth_check.php';

$USER = $User->getUserByUsername($_SESSION['usr']);
$_SESSION['premium_usr'] = $USER['PREMIUM'];
$premium = $USER['PREMIUM'];

if ($USER['PREMIUM'] == 0) {
    $title = "Dashboard";
} else if ($USER['PREMIUM'] == 1) {
    $title = "Dashboard - Premium";
}

$short_novels = $Crud->getShortNovels();
$long_novels = $Crud->getLongNovels();

$all_novels = [];
foreach ($short_novels as $sh) {
    $all_novels[] = array_merge($sh, ['type' => 'short']);
}
foreach ($long_novels as $lg) {
    $all_novels[] = array_merge($lg, ['type' => 'long']);
}

/**
 * Sorts an array of novels by their date in descending order.
 * The date is determined by the 'shnovel_date' or 'lgnovel_date' fields.
 * If both dates are null, the novels are considered equal.
 * If one date is null, the novel with the non-null date is considered smaller.
 *
 * @param array $all_novels Array of novels to be sorted.
 * @return void
 */
usort($all_novels, function ($a, $b) {
    $dateA = $a['shnovel_date'] ?? $a['lgnovel_date'] ?? null;
    $dateB = $b['shnovel_date'] ?? $b['lgnovel_date'] ?? null;

    if ($dateA === null && $dateB === null) {
        return 0;
    }
    if ($dateA === null) {
        return 1;
    }
    if ($dateB === null) {
        return -1;
    }

    return $dateB <=> $dateA;
});


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
            <a class="navbar-brand" href="#">Welcome <?= $USER['NAME'] ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
                    <?php if ($_SESSION['usr'] == 'admin') { ?>
                        <a class="nav-link" href="controlpanel.php">Admin Control Panel</a>
                    <?php } ?>
                    <?php if ($_SESSION['usr'] !== 'admin') { ?>
                        <a class="nav-link" href="insert_shnovel.php">Insert a new short novel</a>
                        <a class="nav-link" href="insert_lgnovel.php">Insert a new long novel</a>
                    <?php } ?>
                    <a class="nav-link" href="incl/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <?php
    foreach ($all_novels as $row) {
        $type = $row['type'];

        if ($type == 'short') {
            $NOVEL_ID = sanitize_input($row['shnovel_id']);
            $NOVEL_PREMIUM = sanitize_input($row['shnovel_premium']);
            $NOVEL_DATE = sanitize_input($row['shnovel_date']);
            $NOVEL_TITLE = sanitize_input($row['shnovel_title']);
            $NOVEL_CONTENT = sanitize_input($row['shnovel_content']);
        } elseif ($type == 'long') {
            $NOVEL_ID = sanitize_input($row['lgnovel_id']);
            $NOVEL_PREMIUM = sanitize_input($row['lgnovel_premium']);
            $NOVEL_DATE = sanitize_input($row['lgnovel_date']);
            $NOVEL_TITLE = sanitize_input($row['lgnovel_title']);
            $NOVEL_FILENAME = sanitize_input($row['lgnovel_filename']);
        }

        $USR_ID = sanitize_input($row['user_id']);
        $USR_NAME = sanitize_input($row['user_name']);
        $USR_SURNAME = sanitize_input($row['user_surname']);

        if ($premium == 0 && $NOVEL_PREMIUM == 0 || $premium == 1 || $_SESSION['usr'] == 'admin') {
            echo
                '<div class="card m-4">
                    <div class="card-header">';
            if ($NOVEL_PREMIUM == 1) {
                echo '<span class="badge rounded-pill text-bg-warning">PREMIUM</span>';
            }
            echo
                '<h5 class="card-title">"' . $NOVEL_TITLE . '" di ' . $USR_NAME . ' ' . $USR_SURNAME . ' </h5>
                    </div>
                    <div class="card-body">';
            if ($type == 'short') {
                echo
                    '<p class="card-text">' . $NOVEL_CONTENT . '</p>';
            } elseif ($type == 'long') {
                echo
                    '<p class="card-text">Download novel PDF:</p>
                        <a href="incl/download_pdf.php?file=' . urlencode($NOVEL_FILENAME) . '" class="btn btn-outline-dark" download>Download PDF</a>';
            }
            echo
                '</div>
                    <div class="card-footer">';

            // Show deleting button only if the user is the owner of the novel
            if ($_SESSION['id'] == $USR_ID || $_SESSION['usr'] == 'admin') {
                echo '
                <form method="post" action="delete_novel.php" class="mt-3" onsubmit="return confirmDeletion();">
                    <input type="hidden" name="novel_type" value="' . htmlspecialchars($type) . '">
                    <input type="hidden" name="novel_id" value="' . $NOVEL_ID . '">
                    <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                    <button type="submit" class="btn btn-danger">DELETE</button>
                </form>';
            }

            echo '
                    </div>
                </div>';

        }
    }

    ?>

</body>

<script>
    function confirmDeletion() {
        return confirm("Are you sure? This in not reversible.");
    }
</script>

</html>