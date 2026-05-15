<?php 
include '../includes/header_ui.php'; 
$tents = $bll->getAllTents();
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="display-5 border-bottom pb-2">Barracas das Faculdades</h1>
        <p class="lead">Explora as barracas das diferentes faculdades da Academia.</p>
    </div>
</div>

<div class="row">
    <?php if ($tents && count($tents) > 0): ?>
        <?php foreach ($tents as $tent): ?>
            <?php $average = $bll->getAverageTentRating($tent['id_tent']); ?>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-success text-white">
                        <span class="badge bg-light text-success float-end">
                            <?php echo htmlspecialchars($tent['acronym']); ?>
                        </span>
                        <h5 class="mb-0">
                            <?php echo htmlspecialchars($tent['name']); ?>
                        </h5>
                    </div>

                    <div class="card-body">
                        <p>
                            <strong>Faculdade:</strong><br>
                            <?php echo htmlspecialchars($tent['faculty_name']); ?>
                        </p>

                        <p>
                            <strong>Localização:</strong><br>
                            <?php echo htmlspecialchars($tent['location']); ?>
                        </p>

                        <p>
                            <strong>Horário:</strong><br>
                            <?php echo htmlspecialchars($tent['open_time']); ?> - 
                            <?php echo htmlspecialchars($tent['close_time']); ?>
                        </p>

                        <p>
                            <strong>Rating médio:</strong>
                            <?php echo $average ? number_format($average, 1) . '/5' : 'Ainda sem avaliações'; ?>
                        </p>
                    </div>

                    <div class="card-footer bg-white border-top-0">
                        <a href="tent_detail.php?id=<?php echo $tent['id_tent']; ?>" class="btn btn-outline-success w-100">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Ainda não existem barracas registadas.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>