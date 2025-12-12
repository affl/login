<?php
// user_edit.php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/user.php';
require_once __DIR__ . '/app/helpers.php';

requireRole('admin'); // solo admin

$conn = getConnection();

// 1. Obtener ID de usuario
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    redirect('admin_users.php');
}

// 2. Si viene por POST, actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_id = (int)($_POST['role_id'] ?? 0);
    $status  = $_POST['status'] ?? 'active';

    // Validar valores básicos
    if (!in_array($status, ['active', 'inactive'], true)) {
        $status = 'active';
    }

    $sql = "UPDATE users 
            SET role_id = :role_id,
                status  = :status
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':role_id' => $role_id,
        ':status'  => $status,
        ':id'      => $id,
    ]);

    redirect('admin_users.php');
}

// 3. Obtener usuario actual
$user = getUserById($id);
if (!$user) {
    redirect('admin_users.php');
}

// 4. Obtener lista de roles
$sql = "SELECT id, name FROM roles ORDER BY name ASC";
$roles = $conn->query($sql)->fetchAll();
?>
<?php include("partials/header.php"); ?>

<div class="container mt-4">

    <h1 class="mb-4">Editar usuario</h1>

    <form method="post" class="card p-4 shadow-sm">

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" 
                   class="form-control" 
                   value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>" 
                   disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" 
                   class="form-control" 
                   value="<?= htmlspecialchars($user['email']) ?>" 
                   disabled>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Rol</label>
            <select name="role_id" id="role_id" class="form-select" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"
                        <?= $role['id'] == $user['role_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label d-block">Estatus</label>

            <div class="form-check form-check-inline">
                <input class="form-check-input" 
                       type="radio" 
                       name="status" 
                       id="status_active" 
                       value="active"
                       <?= $user['status'] === 'active' ? 'checked' : '' ?>>
                <label class="form-check-label" for="status_active">Activo</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" 
                       type="radio" 
                       name="status" 
                       id="status_inactive" 
                       value="inactive"
                       <?= $user['status'] === 'inactive' ? 'checked' : '' ?>>
                <label class="form-check-label" for="status_inactive">Baja</label>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="admin_users.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>

    </form>

</div>

<?php include("partials/footer.php"); ?>