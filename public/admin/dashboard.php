<?php
include '../includes/header_admin.php';

$stats = $bll->getAdminStats();
$topEvent = $stats['most_popular_event'] ?? null;
$topTent = $stats['highest_rated_tent'] ?? null;
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Painel Principal</h1>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white p-3 h-100">
            <h5>Total de Eventos</h5>
            <h2><?php echo htmlspecialchars($stats['total_events'] ?? 0); ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white p-3 h-100">
            <h5>Total de Artistas</h5>
            <h2><?php echo htmlspecialchars($stats['total_artists'] ?? 0); ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-dark p-3 h-100">
            <h5>Total de Barracas</h5>
            <h2><?php echo htmlspecialchars($stats['total_tents'] ?? 0); ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-dark text-white p-3 h-100">
            <h5>Total de Utilizadores</h5>
            <h2><?php echo htmlspecialchars($stats['total_users'] ?? 0); ?></h2>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light">
                Evento mais popular
            </div>
            <div class="card-body">
                <h4><?php echo htmlspecialchars($topEvent['name'] ?? 'Sem eventos'); ?></h4>
                <p class="mb-0">
                    <?php echo htmlspecialchars($topEvent['agenda_count'] ?? 0); ?> utilizador(es) adicionaram este evento à agenda.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light">
                Barraca melhor avaliada
            </div>
            <div class="card-body">
                <h4><?php echo htmlspecialchars($topTent['name'] ?? 'Sem barracas'); ?></h4>
                <p class="mb-0">
                    Rating médio:
                    <?php echo !empty($topTent['average_rating']) ? number_format($topTent['average_rating'], 1) . '/5' : 'Ainda sem avaliações'; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
