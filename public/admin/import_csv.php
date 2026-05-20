<?php
include '../includes/header_admin.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $type = $_POST['type'] ?? '';
    $file = $_FILES['csv_file']['tmp_name'];

    if (!is_uploaded_file($file)) {
        $message = "Erro ao carregar o ficheiro.";
    } else {
        $result = $bll->importCSV($type, $file);
        $message = $result['message'];
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

Exemplo: Dillaz;Hip-Hop;Portugal;Rapper português famoso</pre>

        <h5>Eventos</h5>
        <pre>Nome;Descrição;Data;Localização;Tipo;ID Barraca

Exemplo: Noite Académica;Concerto especial;2026-05-08 22:00:00;Palco Principal;Concert;</pre>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
