<?php
// 403.php
http_response_code(403);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Acceso denegado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container text-center mt-5">
    <h1 class="display-4 text-danger">403</h1>
    <p class="lead">No tienes permiso para acceder a esta sección.</p>

    <a href="home.php" class="btn btn-primary mt-3">Volver al inicio</a>
</div>

</body>
</html>