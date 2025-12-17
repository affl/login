<?php
// profile.php
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/user.php';
require_once __DIR__ . '/app/helpers.php';

authRequired();

$conn = getConnection();

$errors = [];
$success = null;

// Default avatar recomendado (estático)
$defaultAvatar = 'assets/images/avatars/default.png';

// 1) Tomar el usuario logueado desde sesión
$userId = (int)($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
    redirect('login.php');
}

// 2) Obtener datos del usuario
$user = getUserById($userId);
if (!$user) {
    redirect('login.php');
}

// 3) POST: actualizar perfil o cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'update_profile';

    // ============================
    // A) ACTUALIZAR PERFIL / AVATAR
    // ============================
    if ($action === 'update_profile') {

        $first_name  = trim($_POST['first_name'] ?? '');
        $last_name   = trim($_POST['last_name'] ?? '');
        $middle_name = trim($_POST['middle_name'] ?? '');

        // Validaciones mínimas
        if ($first_name === '') $errors[] = 'El nombre es obligatorio.';
        if ($last_name === '')  $errors[] = 'El apellido paterno es obligatorio.';

        // Flags de avatar
        $removeAvatar = isset($_POST['remove_avatar']) && $_POST['remove_avatar'] === '1';

        // Preparar validación de avatar (sin mover todavía)
        $avatarPending = null;

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Ocurrió un error al subir el avatar.';
            } else {

                $maxBytes = 2 * 1024 * 1024; // 2MB
                if ($_FILES['avatar']['size'] > $maxBytes) {
                    $errors[] = 'El avatar excede el tamaño máximo permitido (2MB).';
                } else {

                    $tmpPath = $_FILES['avatar']['tmp_name'];

                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime  = $finfo->file($tmpPath);

                    $allowed = [
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp',
                    ];

                    if (!isset($allowed[$mime])) {
                        $errors[] = 'Formato de avatar no permitido. Usa JPG, PNG o WEBP.';
                    } else {
                        $avatarPending = [
                            'tmp' => $tmpPath,
                            'ext' => $allowed[$mime],
                        ];
                    }
                }
            }
        }

        // Si marcó quitar avatar pero también subió uno nuevo, gana el nuevo
        if ($removeAvatar && $avatarPending) {
            $removeAvatar = false;
        }

        if (empty($errors)) {
            try {
                $conn->beginTransaction();

                // 1) Actualizar datos del perfil
                $sql = "UPDATE users
                        SET first_name = :first_name,
                            last_name = :last_name,
                            middle_name = :middle_name
                        WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':first_name'  => $first_name,
                    ':last_name'   => $last_name,
                    ':middle_name' => $middle_name,
                    ':id'          => $userId,
                ]);

                // 2) Quitar avatar (borrar archivo y dejar NULL)
                if ($removeAvatar) {
                    if (!empty($user['avatar'])) {
                        $oldPath = __DIR__ . '/' . $user['avatar'];
                        if (is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $sql = "UPDATE users SET avatar = NULL WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':id' => $userId]);

                    $_SESSION['user_avatar'] = null;
                    $user['avatar'] = null;
                }

                // 3) Subir avatar nuevo (reemplazar)
                if ($avatarPending) {

                    // borrar anteriores (por si cambió extensión)
                    foreach (glob(__DIR__ . "/uploads/avatars/user_{$userId}.*") as $old) {
                        @unlink($old);
                    }

                    $uploadDir = __DIR__ . '/uploads/avatars';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $filename = "user_{$userId}." . $avatarPending['ext'];
                    $dest = $uploadDir . '/' . $filename;

                    if (!move_uploaded_file($avatarPending['tmp'], $dest)) {
                        throw new Exception('No se pudo guardar el avatar en el servidor.');
                    }

                    $avatarPath = 'uploads/avatars/' . $filename;

                    $sql = "UPDATE users SET avatar = :avatar WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':avatar' => $avatarPath,
                        ':id'     => $userId,
                    ]);
                }

                $conn->commit();

                // 4) Refrescar $user y actualizar sesión para navbar
                $user = getUserById($userId);

                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_avatar'] = $user['avatar'] ?? null;

                $success = 'Perfil actualizado correctamente.';

            } catch (Throwable $e) {
                if ($conn->inTransaction()) $conn->rollBack();
                $errors[] = 'No se pudo actualizar el perfil. Intenta de nuevo.';
            }
        }
    }

    // ============================
    // B) CAMBIAR CONTRASEÑA
    // ============================
    if ($action === 'change_password') {

        $current = $_POST['current_password'] ?? '';
        $new1    = $_POST['new_password'] ?? '';
        $new2    = $_POST['new_password_confirmation'] ?? '';

        if ($current === '' || $new1 === '' || $new2 === '') {
            $errors[] = 'Completa los tres campos de contraseña.';
        }

        if (strlen($new1) < 8) {
            $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        }

        if ($new1 !== $new2) {
            $errors[] = 'La nueva contraseña y su confirmación no coinciden.';
        }

        if (empty($errors)) {
            // Traer hash actual
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $userId]);
            $hash = $stmt->fetchColumn();

            if (!$hash || !password_verify($current, $hash)) {
                $errors[] = 'La contraseña actual es incorrecta.';
            } else {
                $newHash = password_hash($new1, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = :pwd WHERE id = :id");
                $stmt->execute([
                    ':pwd' => $newHash,
                    ':id'  => $userId
                ]);

                $success = 'Contraseña actualizada correctamente.';
            }
        }
    }
}

// Avatar actual para mostrar en vista
$currentAvatar = !empty($user['avatar']) ? $user['avatar'] : $defaultAvatar;

include("partials/header.php");
?>
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Mi perfil</h1>
        <a href="home.php" class="btn btn-secondary">Volver</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <!-- =========================
         FORM 1: PERFIL + AVATAR
         ========================= -->
    <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <input type="hidden" name="action" value="update_profile">

        <div class="row">

            <div class="mb-3 col-md-4">
                <label class="form-label">Nombre</label>
                <input name="first_name" type="text" class="form-control"
                       value="<?= htmlspecialchars($user['first_name']) ?>" required>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">Apellido Paterno</label>
                <input name="last_name" type="text" class="form-control"
                       value="<?= htmlspecialchars($user['last_name']) ?>" required>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">Apellido Materno</label>
                <input name="middle_name" type="text" class="form-control"
                       value="<?= htmlspecialchars($user['middle_name']) ?>">
            </div>

            <div class="mb-3 col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" class="form-control"
                       value="<?= htmlspecialchars($user['email']) ?>" disabled>
            </div>

            <div class="mb-3 col-md-6">
                <label class="form-label">Rol</label>
                <input type="text" class="form-control"
                       value="<?= htmlspecialchars($user['role_description'] ?? '') ?>" disabled>
            </div>

            <!-- Avatar -->
            <div class="mb-3 col-md-12">
                <label class="form-label d-block">Avatar</label>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <img src="<?= htmlspecialchars($currentAvatar) ?>"
                         alt="Avatar"
                         width="64" height="64"
                         class="rounded-circle border"
                         style="object-fit: cover;">

                    <div>
                        <input type="file" id="avatar" name="avatar" class="form-control"
                               accept="image/png,image/jpeg,image/webp">
                        <div class="form-text">JPG, PNG o WEBP. Máximo 2MB.</div>

                        <?php if (!empty($user['avatar'])): ?>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox"
                                       name="remove_avatar" id="remove_avatar" value="1">
                                <label class="form-check-label" for="remove_avatar">
                                    Quitar avatar (usar imagen por defecto)
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-3 col-md-12">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>

        </div>
    </form>

    <!-- =========================
         FORM 2: SEGURIDAD
         ========================= -->
    <form method="post" class="card p-4 shadow-sm mt-4">
        <input type="hidden" name="action" value="change_password">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Seguridad</h5>
        </div>

        <div class="row">
            <div class="mb-3 col-md-4">
                <label class="form-label">Contraseña actual</label>
                <input type="password" name="current_password" class="form-control" autocomplete="current-password">
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" name="new_password" class="form-control" autocomplete="new-password">
                <div class="form-text">Mínimo 8 caracteres.</div>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">Repetir nueva contraseña</label>
                <input type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password">
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-outline-secondary">
                Actualizar contraseña
            </button>
        </div>
    </form>

</div>

<?php include("partials/footer.php"); ?>