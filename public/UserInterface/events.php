<?php
include '../includes/header_ui.php';

$allowedSorts = ['date', 'popularity', 'rating'];
$sortBy = $_GET['sort'] ?? 'date';

if (!in_array($sortBy, $allowedSorts, true)) {
    $sortBy = 'date';
}

$filters = [
    'search' => trim($_GET['search'] ?? ''),
    'date' => trim($_GET['date'] ?? ''),
    'faculty_id' => intval($_GET['faculty_id'] ?? 0),
    'event_type' => trim($_GET['event_type'] ?? ''),
    'minimum_rating' => intval($_GET['minimum_rating'] ?? 0),
    'sort' => $sortBy
];

$events = $bll->getFilteredEvents($filters);
$faculties = $bll->getAllFaculties();
$eventTypes = $bll->getEventTypes();
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 border-bottom pb-2">Programa do Festival</h1>
        <p class="lead">Descobre todos os concertos, cerimónias e atividades culturais.</p>
    </div>
</div>

<?php include __DIR__ . '/../includes/upcoming_alerts.php'; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white">
        Pesquisa e filtros
    </div>

    <div class="card-body">
        <form method="GET" action="events.php">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Pesquisar</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        placeholder="Nome, descrição ou local"
                        value="<?php echo htmlspecialchars($filters['search']); ?>"
                    >
                </div>

                <div class="col-md-2">
                    <label for="date" class="form-label">Data</label>
                    <input
                        type="date"
                        id="date"
                        name="date"
                        class="form-control"
                        value="<?php echo htmlspecialchars($filters['date']); ?>"
                    >
                </div>

                <div class="col-md-3">
                    <label for="faculty_id" class="form-label">Faculdade</label>
                    <select id="faculty_id" name="faculty_id" class="form-select">
                        <option value="0">Todas</option>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?php echo $faculty['id_faculty']; ?>" <?php echo $filters['faculty_id'] === (int)$faculty['id_faculty'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($faculty['acronym'] . ' - ' . $faculty['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="event_type" class="form-label">Tipo</label>
                    <select id="event_type" name="event_type" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($eventTypes as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['event_type']); ?>" <?php echo $filters['event_type'] === $type['event_type'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['event_type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="minimum_rating" class="form-label">Rating mínimo</label>
                    <select id="minimum_rating" name="minimum_rating" class="form-select">
                        <option value="0">Qualquer rating</option>
                        <?php for ($rating = 1; $rating <= 5; $rating++): ?>
                            <option value="<?php echo $rating; ?>" <?php echo $filters['minimum_rating'] === $rating ? 'selected' : ''; ?>>
                                <?php echo $rating; ?> ou mais
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="sort" class="form-label">Ordenar por</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="date" <?php echo $sortBy === 'date' ? 'selected' : ''; ?>>Data</option>
                        <option value="popularity" <?php echo $sortBy === 'popularity' ? 'selected' : ''; ?>>Popularidade</option>
                        <option value="rating" <?php echo $sortBy === 'rating' ? 'selected' : ''; ?>>Rating</option>
                    </select>
                </div>

                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        Aplicar filtros
                    </button>

                    <a href="events.php" class="btn btn-outline-secondary">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

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
                            <?php echo date('d M Y, H:i', strtotime($event['event_date'])); ?>
                        </h6>

                        <p class="card-text text-truncate" style="max-height: 3rem;">
                            <?php echo htmlspecialchars($event['description']); ?>
                        </p>

                        <p class="card-text mb-0">
                            <strong>Local:</strong> <?php echo htmlspecialchars($event['location']); ?>
                        </p>

                        <?php if (!empty($event['faculty_acronym'])): ?>
                            <p class="card-text mb-0">
                                <strong>Faculdade:</strong>
                                <?php echo htmlspecialchars($event['faculty_acronym']); ?>
                            </p>
                        <?php endif; ?>

                        <p class="card-text mb-0 mt-3">
                            <strong>Popularidade:</strong>
                            <?php echo (int)($event['popularity_count'] ?? 0); ?> na agenda
                        </p>

                        <p class="card-text mb-0">
                            <strong>Rating:</strong>
                            <?php if (!empty($event['rating_count'])): ?>
                                <?php echo number_format((float)$event['average_rating'], 1); ?>/5
                            <?php else: ?>
                                Ainda sem avaliações
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0">
                        <a href="event_detail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-outline-primary w-100">
                            Ver detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Não foram encontrados eventos com os filtros selecionados.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
