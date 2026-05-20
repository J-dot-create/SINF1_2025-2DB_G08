<?php
include '../includes/header_admin.php';

$message = "";

// Criar faculdade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_faculty'])) {
    $name = trim($_POST['name'] ?? '');
    $acronym = trim($_POST['acronym'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $color = trim($_POST['color'] ?? '');

    if (!empty($name) && !empty($acronym)) {
        if ($bll->createFaculty($name, $acronym, $description, $color)) {
            $message = "Faculdade criada com sucesso.";
        } else {
            $message = "Erro ao criar faculdade.";
        }
    } else {
        $message = "Preenche os campos obrigatórios.";
    }
}

// Atualizar faculdade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_faculty'])) {
    $id_faculty = intval($_POST['id_faculty']);
    $name = trim($_POST['name'] ?? '');
    $acronym = trim($_POST['acronym'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $color = trim($_POST['color'] ?? '');

    if (!empty($id_faculty) && !empty($name) && !empty($acronym)) {
        if ($bll->updateFaculty($id_faculty, $name, $acronym, $description, $color)) {
            $message = "Faculdade atualizada com sucesso.";
        } else {
            $message = "Erro ao atualizar faculdade.";
        }
    } else {
        $message = "Preenche os campos obrigatórios.";
    }
}

// Apagar faculdade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_faculty'])) {
    if (!$bll->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Pedido inválido. Tenta novamente.";
    } else {
        $id_faculty = intval($_POST['id_faculty'] ?? 0);

        if ($bll->deleteFaculty($id_faculty)) {
            $message = "Faculdade apagada com sucesso.";
        } else {
            $message = "Erro ao apagar faculdade. Pode estar associada a uma ou mais barracas.";
        }
    }
}

$faculties = $bll->getAllFaculties();

$facultyToEdit = null;
if (isset($_GET['edit'])) {
    $facultyToEdit = $bll->getFacultyById(intval($_GET['edit']));
}
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Gerir Faculdades</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white">
        <?php echo $facultyToEdit ? 'Editar faculdade' : 'Criar nova faculdade'; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="manage_faculties.php<?php echo $facultyToEdit ? '?edit=' . $facultyToEdit['id_faculty'] : ''; ?>">
            <?php if ($facultyToEdit): ?>
                <input type="hidden" name="update_faculty" value="1">
                <input type="hidden" name="id_faculty" value="<?php echo $facultyToEdit['id_faculty']; ?>">
            <?php else: ?>
                <input type="hidden" name="create_faculty" value="1">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-5 mb-3">
                    <label class="form-label">Nome *</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($facultyToEdit['name'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Sigla *</label>
                    <input
                        type="text"
                        name="acronym"
                        class="form-control"
                        value="<?php echo htmlspecialchars($facultyToEdit['acronym'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Cor representativa</label>
                    <input
                        type="color"
                        name="color"
                        class="form-control form-control-color"
                        value="<?php echo htmlspecialchars($facultyToEdit['color'] ?? '#000000'); ?>"
                    >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($facultyToEdit['description'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $facultyToEdit ? 'Guardar alterações' : 'Criar faculdade'; ?>
            </button>

            <?php if ($facultyToEdit): ?>
                <a href="manage_faculties.php" class="btn btn-secondary">Cancelar edição</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        Faculdades existentes
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sigla</th>
                    <th>Nome</th>
                    <th>Cor</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($faculties && count($faculties) > 0): ?>
                    <?php foreach ($faculties as $faculty): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($faculty['id_faculty']); ?></td>

                            <td>
                                <strong><?php echo htmlspecialchars($faculty['acronym']); ?></strong>
                            </td>

                            <td><?php echo htmlspecialchars($faculty['name']); ?></td>

                            <td>
                                <?php if (!empty($faculty['color'])): ?>
                                    <span
                                        style="
                                            display:inline-block;
                                            width:24px;
                                            height:24px;
                                            border-radius:50%;
                                            border:1px solid #ccc;
                                            background-color: <?php echo htmlspecialchars($faculty['color']); ?>;
                                        "
                                    ></span>
                                    <?php echo htmlspecialchars($faculty['color']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Sem cor</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(substr($faculty['description'] ?? '', 0, 80)); ?>
                                <?php echo strlen($faculty['description'] ?? '') > 80 ? '...' : ''; ?>
                            </td>

                            <td>
                                <a
                                    href="manage_faculties.php?edit=<?php echo $faculty['id_faculty']; ?>"
                                    class="btn btn-sm btn-outline-warning"
                                >
                                    Editar
                                </a>

                                <form method="POST" action="manage_faculties.php" class="d-inline" data-confirm-delete="Tens a certeza que queres apagar esta faculdade?">
                                    <?php echo $bll->getCsrfInput(); ?>
                                    <input type="hidden" name="delete_faculty" value="1">
                                    <input type="hidden" name="id_faculty" value="<?php echo $faculty['id_faculty']; ?>">
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
                            Ainda não existem faculdades.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
