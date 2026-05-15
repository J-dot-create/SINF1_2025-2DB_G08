<?php
include '../includes/header_admin.php';

$message = "";

// Associar artista a evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_artist_event'])) {
    $id_event = intval($_POST['id_event'] ?? 0);
    $id_artist = intval($_POST['id_artist'] ?? 0);

    if (!empty($id_event) && !empty($id_artist)) {
        if ($bll->addArtistToEvent($id_event, $id_artist)) {
            $message = "Artista associado ao evento com sucesso.";
        } else {
            $message = "Este artista já está associado a esse evento.";
        }
    } else {
        $message = "Escolhe um evento e um artista.";
    }
}

// Remover associação
if (isset($_GET['remove_event']) && isset($_GET['remove_artist'])) {
    $id_event = intval($_GET['remove_event']);
    $id_artist = intval($_GET['remove_artist']);

    if ($bll->removeArtistFromEvent($id_event, $id_artist)) {
        $message = "Associação removida com sucesso.";
    } else {
        $message = "Erro ao remover associação.";
    }
}

$events = $bll->getAllEvents();
$artists = $bll->getAllArtists();
$associations = $bll->getAllEventArtists();
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Associar Artistas a Eventos</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white">
        Nova associação
    </div>

    <div class="card-body">
        <form method="POST" action="manage_event_artists.php">
            <input type="hidden" name="add_artist_event" value="1">

            <div class="row">
                <div class="col-md-5 mb-3">
                    <label class="form-label">Evento *</label>
                    <select name="id_event" class="form-select" required>
                        <option value="">Escolher evento</option>

                        <?php foreach ($events as $event): ?>
                            <option value="<?php echo $event['id_event']; ?>">
                                <?php echo htmlspecialchars($event['name']); ?>
                                -
                                <?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-5 mb-3">
                    <label class="form-label">Artista *</label>
                    <select name="id_artist" class="form-select" required>
                        <option value="">Escolher artista</option>

                        <?php foreach ($artists as $artist): ?>
                            <option value="<?php echo $artist['id_artist']; ?>">
                                <?php echo htmlspecialchars($artist['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        Associar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        Associações existentes
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Data</th>
                    <th>Artista</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($associations && count($associations) > 0): ?>
                    <?php foreach ($associations as $association): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($association['event_name']); ?>
                            </td>

                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($association['event_date'])); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($association['artist_name']); ?>
                            </td>

                            <td>
                                <a
                                    href="../UserInterface/event_detail.php?id=<?php echo $association['id_event']; ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Ver evento
                                </a>

                                <a
                                    href="manage_event_artists.php?remove_event=<?php echo $association['id_event']; ?>&remove_artist=<?php echo $association['id_artist']; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    data-confirm-delete="Tens a certeza que queres remover este artista do evento?"
                                >
                                    Remover
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Ainda não existem artistas associados a eventos.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>