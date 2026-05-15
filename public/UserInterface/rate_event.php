<?php
include '../includes/header_ui.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_event = $_GET['id'] ?? null;

if (!$id_event) {
    echo '<div class="alert alert-danger">Evento inválido.</div>';
    include '../includes/footer.php';
    exit;
}

$event = $bll->getEventById($id_event);
$message = "";

if (!$event) {
    echo '<div class="alert alert-danger">Evento não encontrado.</div>';
    include '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = intval($_POST['score']);
    $comment = trim($_POST['comment']);

    if ($score >= 1 && $score <= 5) {
        $bll->rateEvent($_SESSION['id_user'], $id_event, $score, $comment);
        $message = "Avaliação registada com sucesso.";
    } else {
        $message = "A avaliação tem de estar entre 1 e 5.";
    }
}
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Avaliar evento</h1>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4><?php echo htmlspecialchars($event['name']); ?></h4>

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
                    <option value="1">1 - Muito mau</option>
                    <option value="2">2 - Mau</option>
                    <option value="3">3 - Razoável</option>
                    <option value="4">4 - Bom</option>
                    <option value="5">5 - Excelente</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Comentário</label>
                <textarea name="comment" class="form-control" rows="4" placeholder="Comentário opcional"></textarea>
            </div>

            <button type="submit" class="btn btn-warning">
                Submeter avaliação
            </button>

            <a href="event_detail.php?id=<?php echo $id_event; ?>" class="btn btn-secondary">
                Voltar
            </a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>