<?php
$upcomingAlerts = $bll->getUpcomingAlerts(48);
?>

<?php if ($upcomingAlerts && count($upcomingAlerts) > 0): ?>
    <div class="alert alert-warning shadow-sm mb-4">
        <h5 class="mb-3">⚠️ Eventos nas próximas 48 horas</h5>

        <ul class="mb-0">
            <?php foreach ($upcomingAlerts as $alertEvent): ?>
                <li>
                    <strong><?php echo htmlspecialchars($alertEvent['name']); ?></strong>
                    —
                    <?php echo date('d/m/Y H:i', strtotime($alertEvent['event_date'])); ?>
                    Local:
                    <?php echo htmlspecialchars($alertEvent['location']); ?>

                    <a href="/SINF1_2025-2DB_G08/public/UserInterface/event_detail.php?id=<?php echo $alertEvent['id_event']; ?>">
                        Ver detalhes
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
