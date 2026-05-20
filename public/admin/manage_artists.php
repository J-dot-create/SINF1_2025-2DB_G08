<?php
include '../includes/header_admin.php';

$message = "";

// Criar artista
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_artist'])) {
    $name = trim($_POST['name'] ?? '');
    $musical_genre = trim($_POST['musical_genre'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $biography = trim($_POST['biography'] ?? '');

    if (!empty($name) && !empty($musical_genre) && !empty($country)) {
        if ($bll->createArtist($name, $musical_genre, $country, $biography)) {
            $message = "Artista criado com sucesso.";
        } else {
            $message = "Erro ao criar artista.";
        }
    } else {
        $message = "Preenche os campos obrigatórios.";
    }
}

// Atualizar artista
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_artist'])) {
    $id_artist = intval($_POST['id_artist']);
    $name = trim($_POST['name'] ?? '');
    $musical_genre = trim($_POST['musical_genre'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $biography = trim($_POST['biography'] ?? '');

    if (!empty($id_artist) && !empty($name) && !empty($musical_genre) && !empty($country)) {
        if ($bll->updateArtist($id_artist, $name, $musical_genre, $country, $biography)) {
            $message = "Artista atualizado com sucesso.";
        } else {
            $message = "Erro ao atualizar artista.";
        }
    } else {
        $message = "Preenche os campos obrigatórios.";
    }
}

// Apagar artista
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_artist'])) {
    if (!$bll->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Pedido inválido. Tenta novamente.";
    } else {
        $id_artist = intval($_POST['id_artist'] ?? 0);

        if ($bll->deleteArtist($id_artist)) {
            $message = "Artista apagado com sucesso.";
        } else {
            $message = "Erro ao apagar artista. Pode estar associado a um evento.";
        }
    }
}

$artists = $bll->getAllArtists();

$artistToEdit = null;
if (isset($_GET['edit'])) {
    $artistToEdit = $bll->getArtistById(intval($_GET['edit']));
}
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Gerir Artistas</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white">
        <?php echo $artistToEdit ? 'Editar artista' : 'Criar novo artista'; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="manage_artists.php<?php echo $artistToEdit ? '?edit=' . $artistToEdit['id_artist'] : ''; ?>">
            <?php if ($artistToEdit): ?>
                <input type="hidden" name="update_artist" value="1">
                <input type="hidden" name="id_artist" value="<?php echo $artistToEdit['id_artist']; ?>">
            <?php else: ?>
                <input type="hidden" name="create_artist" value="1">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nome *</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($artistToEdit['name'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Género musical *</label>
                    <input
                        type="text"
                        name="musical_genre"
                        class="form-control"
                        value="<?php echo htmlspecialchars($artistToEdit['musical_genre'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">País *</label>
                    <input
                        type="text"
                        name="country"
                        class="form-control"
                        value="<?php echo htmlspecialchars($artistToEdit['country'] ?? ''); ?>"
                        required
                    >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Biografia</label>
                <textarea name="biography" class="form-control" rows="4"><?php echo htmlspecialchars($artistToEdit['biography'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $artistToEdit ? 'Guardar alterações' : 'Criar artista'; ?>
            </button>

            <?php if ($artistToEdit): ?>
                <a href="manage_artists.php" class="btn btn-secondary">Cancelar edição</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        Artistas existentes
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Género</th>
                    <th>País</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($artists && count($artists) > 0): ?>
                    <?php foreach ($artists as $artist): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($artist['id_artist']); ?></td>
                            <td><?php echo htmlspecialchars($artist['name']); ?></td>
                            <td><?php echo htmlspecialchars($artist['musical_genre']); ?></td>
                            <td><?php echo htmlspecialchars($artist['country']); ?></td>
                            <td>
                                <a href="../UserInterface/artist_detail.php?id=<?php echo $artist['id_artist']; ?>" class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>

                                <a href="manage_artists.php?edit=<?php echo $artist['id_artist']; ?>" class="btn btn-sm btn-outline-warning">
                                    Editar
                                </a>

                                <form method="POST" action="manage_artists.php" class="d-inline" data-confirm-delete="Tens a certeza que queres apagar este artista?">
                                    <?php echo $bll->getCsrfInput(); ?>
                                    <input type="hidden" name="delete_artist" value="1">
                                    <input type="hidden" name="id_artist" value="<?php echo $artist['id_artist']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Apagar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Ainda não existem artistas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
