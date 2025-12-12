<?php
    session_start();

    if (isset($_SESSION['user_id'])) {
        // Ya tiene sesión, no debe ver el login
        header("Location: home.php");
        exit;
    }

    // Leer mensaje de error (si existe) y luego limpiarlo
    $error = $_SESSION['error'] ?? null;
    $old_user = $_SESSION['old_user'] ?? '';
    unset($_SESSION['old_user']);
    unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">    
</head>
<body>

    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-12 col-sm-8 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h4 class="text-center mb-4">Iniciar sesión</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="post">
                        <!-- Correo -->
                        <div class="mb-3">
                            <label for="user" class="form-label">Correo electrónico</label>
                            <input 
                                type="email" 
                                id="user" 
                                name="user" 
                                class="form-control" 
                                placeholder="tu@correo.com"
                                value="<?= htmlspecialchars($old_user) ?>"
                                required>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control" 
                                placeholder="••••••••" 
                                required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">
                                Mantener sesión iniciada
                            </label>
                        </div>

                        <!-- Botón Ingresar -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                Ingresar
                            </button>
                        </div>

                        <div class="mt-3 text-center">
                            <a href="register.php">¿No tienes cuenta? Crear una nueva</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    
</body>
</html>