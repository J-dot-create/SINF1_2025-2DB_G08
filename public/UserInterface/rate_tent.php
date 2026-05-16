<?php
include '../includes/header_ui.php';

if (!isset($_SESSION['id_user']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? $_SESSION['user_id'];

$id_tent = $_GET['id'] ?? null;

if (!$id_tent) {
    echo '<div class="alert alert-danger">Barraca inválida.</div>';
    include '../includes/footer.php';
    exit;
}

$tent = $bll->getTentById($id_tent);
$message = "";

if (!$tent) {
    echo '<div class="alert alert-danger">Barraca não encontrada.</div>';
    include '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = intval($_POST['score']);

    if ($score >= 1 && $score <= 5) {
        $bll->rateTent($id_user, $id_tent, $score);
        $message = "Avaliação registada com sucesso.";
    } else {
        $message = "A avaliação tem de estar entre 1 e 5.";
    }
}
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Avaliar barraca</h1>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4><?php echo htmlspecialchars($tent['name']); ?></h4>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info mt-3">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Avaliação</label>
                <select name="score" class="form-select" required>
                    <option value="">Escolhe uma avaliação</option>
                    <option value="1">1 - Muito má</option>
                    <option value="2">2 - Má</option>
                    <option value="3">3 - Razoável</option>
                    <option value="4">4 - Boa</option>
                    <option value="5">5 - Excelente</option>
                </select>
            </div>

            <button type="submit" class="btn btn-warning">
                Submeter avaliação
            </button>

            <a href="tent_detail.php?id=<?php echo $id_tent; ?>" class="btn btn-secondary">
                Voltar
            </a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
