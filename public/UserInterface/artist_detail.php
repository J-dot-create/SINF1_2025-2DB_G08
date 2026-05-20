<?php
include '../includes/header_ui.php';

$id_artist = $_GET['id'] ?? null;

if (!$id_artist) {
    echo '<div class="alert alert-danger">Artista inválido.</div>';
    include '../includes/footer.php';
    exit;
}

$artist = $bll->getArtistById($id_artist);

if (!$artist) {
    echo '<div class="alert alert-danger">Artista não encontrado.</div>';
    include '../includes/footer.php';
    exit;
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">
            <?php echo htmlspecialchars($artist['name']); ?>
        </h2>
    </div>

    <div class="card-body">
        <p>
            <strong>Género musical:</strong>
            <?php echo htmlspecialchars($artist['musical_genre'] ?? 'Não definido'); ?>
        </p>

        <p>
            <strong>País:</strong>
            <?php echo htmlspecialchars($artist['country'] ?? 'Não definido'); ?>
        </p>

        <p>
            <strong>Biografia:</strong><br>
            <?php echo nl2br(htmlspecialchars($artist['biography'] ?? 'Sem biografia disponível.')); ?>
        </p>

        <a href="artists.php" class="btn btn-secondary mt-3">
            Voltar aos artistas
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
