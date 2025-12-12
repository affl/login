<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4 mb-4">
    <div class="container-fluid">

        <!-- Logo + Nombre -->
        <a class="navbar-brand d-flex align-items-center" href="home.php">
            <img src="assets/images/logo.png" alt="Logo" width="60" class="me-3">

            <div class="d-flex flex-column lh-1">
                <span class="fw-bold fs-5">Company</span>
                <small class="text-muted mt-1">Tu confianza, nuestra prioridad</small>
            </div>
        </a>

        <!-- Botones del menú -->
        <div class="ms-auto">
            <a href="home.php" class="btn btn-outline-primary me-1">Home</a>

            <?php if (userHasRole('admin')): ?>
                <a href="admin_users.php" class="btn btn-outline-primary me-1">Usuarios</a>
            <?php endif; ?>

            <?php if (userHasRole(['admin', 'coordinator'])): ?>
                <a href="portafolio.php" class="btn btn-outline-primary me-1">Portafolio</a>
            <?php endif; ?>

            <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
        </div>

    </div>
</nav>