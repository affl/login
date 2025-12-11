<?php
    // public/home.php
    require_once __DIR__ . '/app/auth.php';
    require_once __DIR__ . '/app/user.php';
    require_once __DIR__ . '/app/helpers.php';

    authRequired(); // protege la página

    $user = getUserById($_SESSION['user_id']);
?>
<?php include("partials/header.php"); ?>
    <div class="container">

        <div class="p-5 bg-light">
            <h1 class="display-3">Bienvenido</h1>
            <p class="lead">Este es un portafolio de <strong><?= htmlspecialchars(fullName($user)); ?></strong> [<?= $user['role_name'];?>]</p>
            <hr class="my-2">
            <p>Más información</p>
        </div>

    </div>
<?php include("partials/footer.php"); ?>