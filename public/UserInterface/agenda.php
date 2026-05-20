<?php
include '../includes/header_ui.php';

if (!isset($_SESSION['id_user']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    if (!$bll->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Pedido inválido. Tenta novamente.";
    } else {
    $id_event = intval($_POST['id_event'] ?? 0);

    if ($bll->addEventToAgenda($id_user, $id_event)) {
        $message = "Evento adicionado à agenda.";
    } else {
        $message = "Este evento já está na tua agenda.";
    }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_event'])) {
    if (!$bll->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Pedido inválido. Tenta novamente.";
    } else {
    $id_event = intval($_POST['id_event'] ?? 0);
    $bll->removeEventFromAgenda($id_user, $id_event);
    $message = "Evento removido da agenda.";
    }
}

$agenda = $bll->getUserAgenda($id_user);
?>

<h1 class="display-5 border-bottom pb-2 mb-4">A minha agenda</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if ($agenda && count($agenda) > 0): ?>
    <div class="row">
        <?php foreach ($agenda as $event): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <?php echo htmlspecialchars($event['name']); ?>
                        </h5>

                        <p>
                            <strong>Data:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?>
                        </p>

                        <p>
                            <strong>Local:</strong><br>
                            <?php echo htmlspecialchars($event['location']); ?>
                        </p>

                        <p class="text-truncate">
                            <?php echo htmlspecialchars($event['description']); ?>
                        </p>
                    </div>

                    <div class="card-footer bg-white border-top-0">
                        <a href="event_detail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-outline-primary w-100 mb-2">
                            Ver detalhes
                        </a>

                        <form method="POST" action="agenda.php" data-confirm-delete="Tens a certeza que queres remover este evento da agenda?">
                            <?php echo $bll->getCsrfInput(); ?>
                            <input type="hidden" name="remove_event" value="1">
                            <input type="hidden" name="id_event" value="<?php echo $event['id_event']; ?>">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                Remover da agenda
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        Ainda não adicionaste eventos à tua agenda.
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
