<?php
require_once __DIR__ . '/../src/bll/BusinessLogicLayer.php';

$bll = new BusinessLogicLayer();
$events = $bll->getAllEvents();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queima das Fitas Porto 2026</title>
    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<!-- Removing the hardcoded navbar from index.php and using header_ui.php -->
<?php include 'includes/header_ui.php'; ?>
<h1>Bem-vindo à Queima das Fitas do Porto</h1>
<p>Consulta os próximos eventos, concertos e atividades!</p>


<div class="container">
    <header class="text-center mb-5 mt-4">
        <h1 class="display-4">Bem-vindo à Queima das Fitas do Porto</h1>
        <p class="lead">Consulta os próximos eventos, concertos e atividades!</p>
    </header>

    <?php include __DIR__ . '/includes/upcoming_alerts.php'; ?>


    <div class="row">
        <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <h6 class="card-subtitle mb-3 text-muted">
                                📅 <?php echo date('d M Y, H:i', strtotime($event['event_date'])); ?>
                            </h6>
                            <p class="card-text text-truncate" style="max-height: 3rem;">
                                <?php echo htmlspecialchars($event['description']); ?>
                            </p>
                            <p class="small mb-0"><strong>📍 Local:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="UserInterface/event_detail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-outline-primary btn-sm w-100">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">Ainda não existem eventos agendados na base de dados.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>