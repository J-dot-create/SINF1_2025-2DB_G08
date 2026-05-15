<?php
include '../includes/header_ui.php';

$id_tent = $_GET['id'] ?? null;

if (!$id_tent) {
    echo '<div class="alert alert-danger">Barraca inválida.</div>';
    include '../includes/footer.php';
    exit;
}

$tent = $bll->getTentById($id_tent);
$average = $bll->getAverageTentRating($id_tent);

if (!$tent) {
    echo '<div class="alert alert-danger">Barraca não encontrada.</div>';
    include '../includes/footer.php';
    exit;
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white">
        <h2 class="mb-0">
            <?php echo htmlspecialchars($tent['name']); ?>
        </h2>
    </div>

    <div class="card-body">
        <p>
            <strong>Faculdade:</strong><br>
            <?php echo htmlspecialchars($tent['faculty_name'] ?? 'Não definida'); ?>

            <?php if (!empty($tent['acronym'])): ?>
                — <?php echo htmlspecialchars($tent['acronym']); ?>
            <?php endif; ?>
        </p>

        <?php if (!empty($tent['color'])): ?>
            <p>
                <strong>Cor representativa:</strong><br>
                <span 
                    style="
                        display:inline-block;
                        width:24px;
                        height:24px;
                        border-radius:50%;
                        border:1px solid #ccc;
                        background-color: <?php echo htmlspecialchars($tent['color']); ?>;
                        vertical-align:middle;
                    "
                ></span>
                <?php echo htmlspecialchars($tent['color']); ?>
            </p>
        <?php endif; ?>

        <p>
            <strong>Localização:</strong><br>
            <?php echo htmlspecialchars($tent['location'] ?? 'Não definida'); ?>
        </p>

        <p>
            <strong>Horário:</strong><br>
            <?php echo htmlspecialchars($tent['open_time'] ?? ''); ?>
            -
            <?php echo htmlspecialchars($tent['close_time'] ?? ''); ?>
        </p>

        <p>
            <strong>Descrição da barraca:</strong><br>
            <?php echo nl2br(htmlspecialchars($tent['description'] ?? 'Sem descrição disponível.')); ?>
        </p>

        <?php if (!empty($tent['faculty_description'])): ?>
            <p>
                <strong>Descrição da faculdade:</strong><br>
                <?php echo nl2br(htmlspecialchars($tent['faculty_description'])); ?>
            </p>
        <?php endif; ?>

        <p>
            <strong>Rating médio:</strong>
            <?php echo $average ? number_format($average, 1) . '/5' : 'Ainda sem avaliações'; ?>
        </p>

        <?php if (isset($_SESSION['id_user']) || isset($_SESSION['user_id'])): ?>
            <a href="rate_tent.php?id=<?php echo $tent['id_tent']; ?>" class="btn btn-warning mt-3">
                Avaliar barraca
            </a>
        <?php endif; ?>

        <a href="tents.php" class="btn btn-secondary mt-3">
            Voltar às barracas
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>