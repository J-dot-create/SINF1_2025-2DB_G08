<?php
include '../includes/header_ui.php';

$id_event = $_GET['id'] ?? null;

if (!$id_event) {
    echo '<div class="alert alert-danger">Evento inválido.</div>';
    include '../includes/footer.php';
    exit;
}

$event = $bll->getEventById($id_event);
$artists = $bll->getArtistsByEvent($id_event);
$average = $bll->getAverageEventRating($id_event);

if (!$event) {
    echo '<div class="alert alert-danger">Evento não encontrado.</div>';
    include '../includes/footer.php';
    exit;
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0"><?php echo htmlspecialchars($event['name']); ?></h2>
    </div>

    <div class="card-body">
        <p>
            <strong>Tipo:</strong>
            <?php echo htmlspecialchars($event['event_type']); ?>
        </p>

        <p>
            <strong>Data e hora:</strong>
            <?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?>
        </p>

        <p>
            <strong>Local:</strong>
            <?php echo htmlspecialchars($event['location']); ?>
        </p>

        <?php if (!empty($event['tent_name'])): ?>
            <p>
                <strong>Barraca associada:</strong>
                <?php echo htmlspecialchars($event['tent_name']); ?>
            </p>
        <?php endif; ?>

        <p>
            <strong>Descrição:</strong><br>
            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
        </p>

        <p>
            <strong>Rating médio:</strong>
            <?php echo $average ? number_format($average, 1) . '/5' : 'Ainda sem avaliações'; ?>
        </p>

        <?php if ($artists && count($artists) > 0): ?>
            <h4 class="mt-4">Artistas associados</h4>
            <ul>
                <?php foreach ($artists as $artist): ?>
                    <li>
                        <a href="artist_detail.php?id=<?php echo $artist['id_artist']; ?>">
                            <?php echo htmlspecialchars($artist['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (isset($_SESSION['id_user'])): ?>
    <form method="POST" action="agenda.php" class="d-inline">
        <?php echo $bll->getCsrfInput(); ?>
        <input type="hidden" name="add_event" value="1">
        <input type="hidden" name="id_event" value="<?php echo $event['id_event']; ?>">
        <button type="submit" class="btn btn-success mt-3">
        Adicionar à minha agenda
        </button>
    </form>

    <a href="rate_event.php?id=<?php echo $event['id_event']; ?>" class="btn btn-warning mt-3">
        Avaliar evento
    </a>
<?php endif; ?>

<a href="events.php" class="btn btn-secondary mt-3">
    Voltar aos eventos
</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
