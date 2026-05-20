<?php
require_once __DIR__ . '/../src/bll/BusinessLogicLayer.php';

$bll = new BusinessLogicLayer();
$upcomingEvents = $bll->getEventsByTimeStatus('upcoming');
$pastEvents = $bll->getEventsByTimeStatus('past');
$events = !empty($upcomingEvents) ? $upcomingEvents : $pastEvents;
$eventsSectionTitle = !empty($upcomingEvents) ? 'Próximos eventos' : 'Eventos anteriores';
$emptyEventsMessage = !empty($upcomingEvents)
    ? 'Ainda não existem eventos futuros agendados na base de dados.'
    : 'Ainda não existem eventos registados na base de dados.';
$publicStats = $bll->getPublicEventStats();
$mostPopularEvent = $publicStats['most_popular_event'] ?? null;
$highestRatedEvent = $publicStats['highest_rated_event'] ?? null;

include 'includes/header_ui.php';
?>

<header class="text-center mb-5 mt-4">
    <h1 class="display-4">Bem-vindo à Queima das Fitas do Porto</h1>
    <p class="lead">Consulta os próximos eventos, concertos e atividades!</p>
</header>

<?php include __DIR__ . '/includes/upcoming_alerts.php'; ?>

<section class="mb-5">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    Evento mais popular
                </div>
                <div class="card-body">
                    <?php if ($mostPopularEvent && (int)($mostPopularEvent['popularity_count'] ?? 0) > 0): ?>
                        <h4 class="card-title"><?php echo htmlspecialchars($mostPopularEvent['name']); ?></h4>
                        <p class="mb-2">
                            <?php echo (int)$mostPopularEvent['popularity_count']; ?>
                            utilizador(es) adicionaram este evento à agenda.
                        </p>
                        <p class="text-muted mb-3">
                            <?php echo date('d/m/Y H:i', strtotime($mostPopularEvent['event_date'])); ?>
                            · Local: <?php echo htmlspecialchars($mostPopularEvent['location']); ?>
                        </p>
                        <a href="UserInterface/event_detail.php?id=<?php echo $mostPopularEvent['id_event']; ?>" class="btn btn-outline-primary">
                            Ver detalhes
                        </a>
                    <?php else: ?>
                        <p class="mb-0 text-muted">
                            Ainda não há eventos adicionados às agendas dos utilizadores.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    Evento melhor cotado
                </div>
                <div class="card-body">
                    <?php if ($highestRatedEvent): ?>
                        <h4 class="card-title"><?php echo htmlspecialchars($highestRatedEvent['name']); ?></h4>
                        <p class="mb-2">
                            Rating médio:
                            <?php echo number_format((float)$highestRatedEvent['average_rating'], 1); ?>/5
                            com <?php echo (int)$highestRatedEvent['rating_count']; ?> avaliação(ões).
                        </p>
                        <p class="text-muted mb-3">
                            <?php echo date('d/m/Y H:i', strtotime($highestRatedEvent['event_date'])); ?>
                            · Local: <?php echo htmlspecialchars($highestRatedEvent['location']); ?>
                        </p>
                        <a href="UserInterface/event_detail.php?id=<?php echo $highestRatedEvent['id_event']; ?>" class="btn btn-outline-warning">
                            Ver detalhes
                        </a>
                    <?php else: ?>
                        <p class="mb-0 text-muted">
                            Ainda não existem avaliações de eventos.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h3 mb-0"><?php echo $eventsSectionTitle; ?></h2>
        <a href="UserInterface/events.php" class="btn btn-outline-primary btn-sm">
            Ver programa completo
        </a>
    </div>

    <div class="row">
        <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <h6 class="card-subtitle mb-3 text-muted">
                                <?php echo date('d M Y, H:i', strtotime($event['event_date'])); ?>
                            </h6>
                            <p class="card-text text-truncate" style="max-height: 3rem;">
                                <?php echo htmlspecialchars($event['description']); ?>
                            </p>
                            <p class="small mb-1">
                                <strong>Local:</strong> <?php echo htmlspecialchars($event['location']); ?>
                            </p>
                            <p class="small mb-1">
                                <strong>Popularidade:</strong>
                                <?php echo (int)($event['popularity_count'] ?? 0); ?> na agenda
                            </p>
                            <p class="small mb-0">
                                <strong>Rating:</strong>
                                <?php if (!empty($event['rating_count'])): ?>
                                    <?php echo number_format((float)$event['average_rating'], 1); ?>/5
                                <?php else: ?>
                                    Ainda sem avaliações
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="UserInterface/event_detail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-outline-primary btn-sm w-100">
                                Ver detalhes
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <?php echo $emptyEventsMessage; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
