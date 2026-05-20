<?php
include '../includes/header_admin.php';

$message = "";
$eventToEdit = null;

if (isset($_GET['edit'])) {
    $eventToEdit = $bll->getEventById(intval($_GET['edit']));
}

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

// Atualizar evento existente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    $id_event = intval($_POST['id_event'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $event_type = trim($_POST['event_type'] ?? '');
    $id_tent = !empty($_POST['id_tent']) ? intval($_POST['id_tent']) : null;

    if (!empty($id_event) && !empty($name) && !empty($event_date) && !empty($location) && !empty($event_type)) {
        if ($bll->updateEvent($id_event, $name, $description, $event_date, $location, $event_type, $id_tent)) {
            $message = "Evento atualizado com sucesso.";
        } else {
            $message = "Erro ao atualizar evento.";
        }
    } else {
        $message = "Preenche todos os campos obrigatórios.";
    }
}

// Apagar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event'])) {
    if (!$bll->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "Pedido inválido. Tenta novamente.";
    } else {
        $id_event = intval($_POST['id_event'] ?? 0);

        if ($bll->deleteEvent($id_event)) {
            $message = "Evento apagado com sucesso.";
        } else {
            $message = "Erro ao apagar evento.";
        }
    }
}

$events = $bll->getAllEvents();
$tents = $bll->getAllTents();
$eventTypes = $bll->getEventTypes();
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Gerir Eventos</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white">
        <?php echo $eventToEdit ? 'Editar evento' : 'Criar novo evento'; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="manage_events.php">
            <?php if ($eventToEdit): ?>
                <input type="hidden" name="update_event" value="1">
                <input type="hidden" name="id_event" value="<?php echo htmlspecialchars($eventToEdit['id_event']); ?>">
            <?php else: ?>
                <input type="hidden" name="create_event" value="1">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome *</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($eventToEdit['name'] ?? ''); ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Data e hora *</label>
                    <input type="datetime-local" name="event_date" class="form-control" value="<?php echo $eventToEdit ? date('Y-m-d\TH:i', strtotime($eventToEdit['event_date'])) : ''; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($eventToEdit['description'] ?? ''); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Local *</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($eventToEdit['location'] ?? ''); ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo *</label>
                    <select name="event_type" class="form-select" required>
                        <option value="">Escolher tipo</option>
                        <?php foreach ($eventTypes as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['event_type']); ?>" <?php echo (($eventToEdit['event_type'] ?? '') === $type['event_type']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['event_type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Barraca associada</label>
                    <select name="id_tent" class="form-select">
                        <option value="">Sem barraca</option>

                        <?php if ($tents): ?>
                            <?php foreach ($tents as $tent): ?>
                                <option value="<?php echo $tent['id_tent']; ?>" <?php echo (($eventToEdit['id_tent'] ?? '') == $tent['id_tent']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tent['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $eventToEdit ? 'Guardar alterações' : 'Criar evento'; ?>
            </button>
            <?php if ($eventToEdit): ?>
                <a href="manage_events.php" class="btn btn-secondary">
                    Cancelar
                </a>
            <?php endif; ?>
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
                            <td><?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?></td>
                            <td><?php echo htmlspecialchars($event['event_type']); ?></td>
                            <td><?php echo htmlspecialchars($event['location']); ?></td>
                            <td>
                                <a href="manage_events.php?edit=<?php echo $event['id_event']; ?>" class="btn btn-sm btn-outline-secondary">
                                    Editar
                                </a>

                                <a href="../UserInterface/event_detail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>

                                <form method="POST" action="manage_events.php" class="d-inline" data-confirm-delete="Tens a certeza que queres apagar este evento?">
                                    <?php echo $bll->getCsrfInput(); ?>
                                    <input type="hidden" name="delete_event" value="1">
                                    <input type="hidden" name="id_event" value="<?php echo $event['id_event']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Apagar
                                    </button>
                                </form>
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
