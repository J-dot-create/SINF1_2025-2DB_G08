<?php 
include '../includes/header_ui.php'; 

// Fetch events from the database via BLL
$events = $bll->getAllEvents();
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="display-5 border-bottom pb-2">Programa do Festival</h1>
        <p class="lead">Descobre todos os concertos, cerimónias e atividades culturais.</p>
    </div>
</div>

<?php include __DIR__ . '/../includes/upcoming_alerts.php'; ?>

<div class="row">
    <?php if ($events && count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card event-card h-100 shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <span class="badge bg-light text-primary float-end">
                            <?php echo htmlspecialchars($event['event_type']); ?>
                        </span>

                        <h5 class="mb-0 text-truncate" title="<?php echo htmlspecialchars($event['name']); ?>">
                            <?php echo htmlspecialchars($event['name']); ?>
                        </h5>
                    </div>

                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-muted">
                            📅 <?php echo date('d M Y, H:i', strtotime($event['event_date'])); ?>
                        </h6>

                        <p class="card-text text-truncate" style="max-height: 3rem;">
                            <?php echo htmlspecialchars($event['description']); ?>
                        </p>

                        <p class="card-text mb-0">
                            <strong>📍 Local:</strong> <?php echo htmlspecialchars($event['location']); ?>
                        </p>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0">
                        <a href="event_detail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-outline-primary w-100">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Ainda não existem eventos agendados. Por favor, volta mais tarde!
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>