<?php
include '../includes/header_admin.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $type = $_POST['type'] ?? '';
    $file = $_FILES['csv_file']['tmp_name'];

    if (!is_uploaded_file($file)) {
        $message = "Erro ao carregar o ficheiro.";
    } else {
        $handle = fopen($file, "r");

        if ($handle !== false) {
            $rowNumber = 0;
            $imported = 0;

            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                $rowNumber++;

                // Ignorar cabeçalho
                if ($rowNumber == 1) {
                    continue;
                }

                if ($type === 'artists') {
                    // CSV esperado: Nome;Género Musical;País;Biografia
                    $name = trim($data[0] ?? '');
                    $musical_genre = trim($data[1] ?? '');
                    $country = trim($data[2] ?? '');
                    $biography = trim($data[3] ?? '');

                    if (!empty($name) && !empty($musical_genre) && !empty($country)) {
                        if ($bll->createArtist($name, $musical_genre, $country, $biography)) {
                            $imported++;
                        }
                    }
                }

                if ($type === 'events') {
                    // CSV esperado: Nome;Descrição;Data;Localização;Tipo;ID Barraca
                    $name = trim($data[0] ?? '');
                    $description = trim($data[1] ?? '');
                    $event_date = trim($data[2] ?? '');
                    $location = trim($data[3] ?? '');
                    $event_type = trim($data[4] ?? '');
                    $id_tent = !empty($data[5]) ? intval($data[5]) : null;

                    if (!empty($name) && !empty($event_date) && !empty($location) && !empty($event_type)) {
                        if ($bll->createEvent($name, $description, $event_date, $location, $event_type, $id_tent)) {
                            $imported++;
                        }
                    }
                }
            }

            fclose($handle);

            $message = "Importação concluída. Registos importados: " . $imported;
        } else {
            $message = "Não foi possível abrir o ficheiro CSV.";
        }
    }
}
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Importar CSV</h1>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        Importar dados
    </div>

    <div class="card-body">
        <form method="POST" action="import_csv.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Tipo de dados</label>
                <select name="type" class="form-select" required>
                    <option value="">Escolher tipo</option>
                    <option value="artists">Artistas</option>
                    <option value="events">Eventos</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ficheiro CSV</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
            </div>

            <button type="submit" class="btn btn-primary">
                Importar
            </button>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-dark text-white">
        Formato esperado dos ficheiros
    </div>

    <div class="card-body">
        <h5>Artistas</h5>
        <pre>Nome;Género Musical;País;Biografia
Dillaz;Hip-Hop;Portugal;Rapper português...</pre>

        <h5>Eventos</h5>
        <pre>Nome;Descrição;Data;Localização;Tipo;ID Barraca
Noite Académica;Concerto especial;2026-05-08 22:00:00;Palco Principal;Concert;</pre>
    </div>
</div>

<?php include '../includes/footer.php'; ?>