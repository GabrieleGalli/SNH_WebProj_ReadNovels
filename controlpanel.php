<?php

require_once 'db/conn.php';
require_once 'incl/session.php';
require_once 'incl/auth_check.php';

$title = 'Admin Control Panel';

$USER = $User->getUserByUsername($_SESSION['usr']);
$USERS = $User->getAllUsers();

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
                    <a class="nav-link active" aria-current="page" href="#">Admin Control Panel</a>
                    <a class="nav-link" href="incl/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Email</th>
                <th>Premium</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($USERS as $user) {
                if ($user['USERNAME'] != $_SESSION['usr']) { ?>
                    <tr>
                        <td><?= sanitize_input($user['USERNAME']) ?></td>
                        <td><?= sanitize_input($user['NAME']) ?></td>
                        <td><?= sanitize_input($user['SURNAME']) ?></td>
                        <td><?= sanitize_input($user['EMAIL']) ?></td>
                        <td>
                            <?php $premium = $user['PREMIUM'];
                            if ($premium == 1) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            } ?>
                            <div class="form-check form-switch">
                                <input class="form-check-input switch-premium" type="checkbox" role="switch"
                                    usr=<?= sanitize_input($user['USERNAME']) ?>         <?= $checked ?>>
                                <label class="form-check-label" for="flexSwitchCheck">Premium</label>
                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>

</body>

<script>
    // Necessario per inviare richieste POST con fetch Ajax 
    const csrfToken = '<?= $_SESSION['csrf_token'] ?>';

    document.querySelectorAll('.switch-premium').forEach(switchElement => {
        switchElement.addEventListener('change', function () {
            const isChecked = this.checked; const usrname = this.getAttribute('usr');

            // Ajax request to update premium status 
            fetch('incl/update_premium.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    username: usrname,
                    premium: isChecked ? 1 : 0,
                    csrf_token: csrfToken,
                }),
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    alert('Premium status updated successfully');
                } else {
                    //console.error('Error:', data.message);
                    alert('Error updating premium status');
                }
            }).catch((error) => {
                // Reset original state if fails
                this.checked = !isChecked;
                console.error(error);
            });
        });
    }) </script>


</html>