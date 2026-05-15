<?php
include '../includes/header_admin.php';

$message = "";

// Criar novo evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $event_type = trim($_POST['event_type'] ?? '');
    $id_tent = !empty($_POST['id_tent']) ? intval($_POST['id_tent']) : null;

    if (!empty($name) && !empty($event_date) && !empty($location) && !empty($event_type)) {
        if ($bll->createEvent($name, $description, $event_date, $location, $event_type, $id_tent)) {
            $message = "Evento criado com sucesso.";
        } else {
            $message = "Erro ao criar evento.";
        }
    } else {
        $message = "Preenche todos os campos obrigatórios.";
    }
}

// Apagar evento
if (isset($_GET['delete'])) {
    $id_event = intval($_GET['delete']);

    if ($bll->deleteEvent($id_event)) {
        $message = "Evento apagado com sucesso.";
    } else {
        $message = "Erro ao apagar evento.";
    }
}

$events = $bll->getAllEvents();
$tents = $bll->getAllTents();
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Gerir Eventos</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white">
        Criar novo evento
    </div>

    <div class="card-body">
        <form method="POST" action="manage_events.php">
            <input type="hidden" name="create_event" value="1">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Data e hora *</label>
                    <input type="datetime-local" name="event_date" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Local *</label>
                    <input type="text" name="location" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo *</label>
                    <select name="event_type" class="form-select" required>
                        <option value="">Escolher tipo</option>
                        <option value="Academic ceremony">Cerimónia académica</option>
                        <option value="Concert">Concerto</option>
                        <option value="Cultural activity">Atividade cultural</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Barraca associada</label>
                    <select name="id_tent" class="form-select">
                        <option value="">Sem barraca</option>

                        <?php if ($tents): ?>
                            <?php foreach ($tents as $tent): ?>
                                <option value="<?php echo $tent['id_tent']; ?>">
                                    <?php echo htmlspecialchars($tent['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Criar evento
            </button>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        Eventos existentes
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Local</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($events && count($events) > 0): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['id_event']); ?></td>

                            <td><?php echo htmlspecialchars($event['name']); ?></td>

                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?>
                            </td>

                            <td><?php echo htmlspecialchars($event['event_type']); ?></td>

                            <td><?php echo htmlspecialchars($event['location']); ?></td>

                            <td>
                                <a 
                                    href="../UserInterface/event_detail.php?id=<?php echo $event['id_event']; ?>" 
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Ver
                                </a>

                                <a 
                                    href="manage_events.php?delete=<?php echo $event['id_event']; ?>" 
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Tens a certeza que queres apagar este evento?');"
                                >
                                    Apagar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Ainda não existem eventos.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>