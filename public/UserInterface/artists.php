<?php 
include '../includes/header_ui.php'; 
$artists = $bll->getAllArtists();
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="display-5 border-bottom pb-2">Artistas</h1>
        <p class="lead">Conhece os artistas associados aos eventos da Queima das Fitas.</p>
    </div>
</div>

<div class="row">
    <?php if ($artists && count($artists) > 0): ?>
        <?php foreach ($artists as $artist): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <?php echo htmlspecialchars($artist['name']); ?>
                        </h5>

                        <p class="mb-1">
                            <strong>Género:</strong> 
                            <?php echo htmlspecialchars($artist['musical_genre'] ?? 'Não definido'); ?>
                        </p>

                        <p class="mb-3">
                            <strong>País:</strong> 
                            <?php echo htmlspecialchars($artist['country'] ?? 'Não definido'); ?>
                        </p>

                        <p class="card-text text-truncate">
                            <?php echo htmlspecialchars($artist['biography'] ?? 'Sem biografia disponível.'); ?>
                        </p>
                    </div>

                    <div class="card-footer bg-white border-top-0">
                        <a href="artist_detail.php?id=<?php echo $artist['id_artist']; ?>" class="btn btn-outline-primary w-100">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Ainda não existem artistas registados.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>