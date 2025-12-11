<?php
    require 'libs/functions.php';   // aquí está getUserById()
    authRequired(); // ¡Listo! Protegida.
?>
<?php include("partials/header.php"); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Datos del proyecto
                    </div>
                    <div class="card-body">
                        <form action="porafolio" method="post">
                            <label for="name">Nombre del proyecto:</label>
                            <input class="form-control" type="text" name="name" id="name">
                            <label for="file">Imagen del Proyecto: </label>
                            <br>
                            <input class="form-control" type="file" name="file" id="file">
                            <br>
                            <input type="text" class="btn btn-outline-success" type="submit" value="Enviar proyecto">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <thead>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>3</td>
                            <td>Favián</td>
                            <td>favian@gmail.com</td>
                            <td>Admin</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include("partials/footer.php"); ?>