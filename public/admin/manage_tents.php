<?php
include '../includes/header_admin.php';

$message = "";

// Criar barraca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_tent'])) {
    $name = trim($_POST['name'] ?? '');
    $id_faculty = intval($_POST['id_faculty'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $open_time = $_POST['open_time'] ?? '';
    $close_time = $_POST['close_time'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (!empty($name) && !empty($id_faculty) && !empty($location) && !empty($open_time) && !empty($close_time)) {
        if ($bll->createTent($name, $id_faculty, $location, $open_time, $close_time, $description)) {
            $message = "Barraca criada com sucesso.";
        } else {
            $message = "Erro ao criar barraca.";
        }
    } else {
        $message = "Preenche os campos obrigatórios.";
    }
}

// Atualizar barraca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tent'])) {
    $id_tent = intval($_POST['id_tent']);
    $name = trim($_POST['name'] ?? '');
    $id_faculty = intval($_POST['id_faculty'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $open_time = $_POST['open_time'] ?? '';
    $close_time = $_POST['close_time'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (!empty($id_tent) && !empty($name) && !empty($id_faculty) && !empty($location) && !empty($open_time) && !empty($close_time)) {
        if ($bll->updateTent($id_tent, $name, $id_faculty, $location, $open_time, $close_time, $description)) {
            $message = "Barraca atualizada com sucesso.";
        } else {
            $message = "Erro ao atualizar barraca.";
        }
    } else {
        $message = "Preenche os campos obrigatórios.";
    }
}

// Apagar barraca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tent'])) {
    if (!$bll->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Pedido inválido. Tenta novamente.";
    } else {
        $id_tent = intval($_POST['id_tent'] ?? 0);

        if ($bll->deleteTent($id_tent)) {
            $message = "Barraca apagada com sucesso.";
        } else {
            $message = "Erro ao apagar barraca. Pode estar associada a eventos ou avaliações.";
        }
    }
}

$tents = $bll->getAllTents();
$faculties = $bll->getAllFaculties();

$tentToEdit = null;
if (isset($_GET['edit'])) {
    $tentToEdit = $bll->getTentById(intval($_GET['edit']));
}
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Gerir Barracas</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white">
        <?php echo $tentToEdit ? 'Editar barraca' : 'Criar nova barraca'; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="manage_tents.php<?php echo $tentToEdit ? '?edit=' . $tentToEdit['id_tent'] : ''; ?>">
            <?php if ($tentToEdit): ?>
                <input type="hidden" name="update_tent" value="1">
                <input type="hidden" name="id_tent" value="<?php echo $tentToEdit['id_tent']; ?>">
            <?php else: ?>
                <input type="hidden" name="create_tent" value="1">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nome *</label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($tentToEdit['name'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Faculdade *</label>
                    <select name="id_faculty" class="form-select" required>
                        <option value="">Escolher faculdade</option>

                        <?php foreach ($faculties as $faculty): ?>
                            <option 
                                value="<?php echo $faculty['id_faculty']; ?>"
                                <?php echo ($tentToEdit && $tentToEdit['id_faculty'] == $faculty['id_faculty']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($faculty['acronym'] . ' - ' . $faculty['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Localização *</label>
                    <input 
                        type="text" 
                        name="location" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($tentToEdit['location'] ?? ''); ?>"
                        required
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Hora de abertura *</label>
                    <input 
                        type="time" 
                        name="open_time" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($tentToEdit['open_time'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Hora de fecho *</label>
                    <input 
                        type="time" 
                        name="close_time" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($tentToEdit['close_time'] ?? ''); ?>"
                        required
                    >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($tentToEdit['description'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">
                <?php echo $tentToEdit ? 'Guardar alterações' : 'Criar barraca'; ?>
            </button>

            <?php if ($tentToEdit): ?>
                <a href="manage_tents.php" class="btn btn-secondary">Cancelar edição</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        Barracas existentes
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Faculdade</th>
                    <th>Localização</th>
                    <th>Horário</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($tents && count($tents) > 0): ?>
                    <?php foreach ($tents as $tent): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tent['id_tent']); ?></td>

                            <td><?php echo htmlspecialchars($tent['name']); ?></td>

                            <td>
                                <?php echo htmlspecialchars(($tent['acronym'] ?? '') . ' - ' . ($tent['faculty_name'] ?? '')); ?>
                            </td>

                            <td><?php echo htmlspecialchars($tent['location']); ?></td>

                            <td>
                                <?php echo htmlspecialchars($tent['open_time']); ?> -
                                <?php echo htmlspecialchars($tent['close_time']); ?>
                            </td>

                            <td>
                                <a 
                                    href="../UserInterface/tent_detail.php?id=<?php echo $tent['id_tent']; ?>" 
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Ver
                                </a>

                                <a 
                                    href="manage_tents.php?edit=<?php echo $tent['id_tent']; ?>" 
                                    class="btn btn-sm btn-outline-warning"
                                >
                                    Editar
                                </a>

                                <form method="POST" action="manage_tents.php" class="d-inline" data-confirm-delete="Tens a certeza que queres apagar esta barraca?">
                                    <?php echo $bll->getCsrfInput(); ?>
                                    <input type="hidden" name="delete_tent" value="1">
                                    <input type="hidden" name="id_tent" value="<?php echo $tent['id_tent']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Apagar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Ainda não existem barracas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
