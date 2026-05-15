<?php
include '../includes/header_admin.php';

$message = "";

// Criar utilizador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $id_role = intval($_POST['id_role'] ?? 0);

    if (!empty($name) && !empty($email) && !empty($password) && !empty($id_role)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Email inválido.";
        } elseif ($bll->createUserByAdmin($name, $email, $password, $id_role)) {
            $message = "Utilizador criado com sucesso.";
        } else {
            $message = "Erro ao criar utilizador. O email pode já existir.";
        }
    } else {
        $message = "Preenche todos os campos obrigatórios.";
    }
}

// Atualizar utilizador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id_user = intval($_POST['id_user'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $id_role = intval($_POST['id_role'] ?? 0);
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($id_user) && !empty($name) && !empty($email) && !empty($id_role)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Email inválido.";
        } elseif ($bll->updateUserByAdmin($id_user, $name, $email, $id_role)) {
            if (!empty($new_password)) {
                $bll->updateUserPasswordByAdmin($id_user, $new_password);
            }

            $message = "Utilizador atualizado com sucesso.";
        } else {
            $message = "Erro ao atualizar utilizador.";
        }
    } else {
        $message = "Preenche os campos obrigatórios.";
    }
}

// Apagar utilizador
if (isset($_GET['delete'])) {
    $id_user = intval($_GET['delete']);

    $currentUserId = $_SESSION['id_user'] ?? $_SESSION['user_id'] ?? null;

    if ($currentUserId == $id_user) {
        $message = "Não podes apagar o teu próprio utilizador enquanto estás autenticado.";
    } else {
        if ($bll->deleteUser($id_user)) {
            $message = "Utilizador apagado com sucesso.";
        } else {
            $message = "Erro ao apagar utilizador. Pode ter agenda ou avaliações associadas.";
        }
    }
}

$users = $bll->getAllUsers();
$roles = $bll->getAllRoles();

$userToEdit = null;
if (isset($_GET['edit'])) {
    $userToEdit = $bll->getUserById(intval($_GET['edit']));
}
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Gerir Utilizadores</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white">
        <?php echo $userToEdit ? 'Editar utilizador' : 'Criar novo utilizador'; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="manage_users.php<?php echo $userToEdit ? '?edit=' . $userToEdit['id_user'] : ''; ?>">
            <?php if ($userToEdit): ?>
                <input type="hidden" name="update_user" value="1">
                <input type="hidden" name="id_user" value="<?php echo $userToEdit['id_user']; ?>">
            <?php else: ?>
                <input type="hidden" name="create_user" value="1">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nome *</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($userToEdit['name'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Email *</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?php echo htmlspecialchars($userToEdit['email'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Role *</label>
                    <select name="id_role" class="form-select" required>
                        <option value="">Escolher role</option>

                        <?php foreach ($roles as $role): ?>
                            <option
                                value="<?php echo $role['id_role']; ?>"
                                <?php echo ($userToEdit && $userToEdit['id_role'] == $role['id_role']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($role['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if ($userToEdit): ?>
                <div class="mb-3">
                    <label class="form-label">Nova password</label>
                    <input
                        type="password"
                        name="new_password"
                        class="form-control"
                        placeholder="Deixa vazio para manter a password atual"
                    >
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Password *</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">
                <?php echo $userToEdit ? 'Guardar alterações' : 'Criar utilizador'; ?>
            </button>

            <?php if ($userToEdit): ?>
                <a href="manage_users.php" class="btn btn-secondary">Cancelar edição</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        Utilizadores existentes
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($users && count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id_user']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role_name']); ?></td>

                            <td>
                                <a
                                    href="manage_users.php?edit=<?php echo $user['id_user']; ?>"
                                    class="btn btn-sm btn-outline-warning"
                                >
                                    Editar
                                </a>

                                <a
                                    href="manage_users.php?delete=<?php echo $user['id_user']; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    data-confirm-delete="Tens a certeza que queres apagar este utilizador?"
                                >
                                    Apagar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Ainda não existem utilizadores.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>