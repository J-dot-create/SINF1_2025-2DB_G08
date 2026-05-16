<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/bll/BusinessLogicLayer.php';

$bll = new BusinessLogicLayer();

if (!isset($_SESSION['id_user']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? $_SESSION['user_id'];
$message = "";
$error = "";

$user = $bll->getUserById($id_user);

if (!$user) {
    die("Erro: utilizador não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error = "Preenche todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Insere um email válido.";
    } else {
        $updated = $bll->updateUserProfile($id_user, $name, $email);

        if ($updated) {
            $_SESSION['user_name'] = $name;
            $message = "Perfil atualizado com sucesso.";
            $user = $bll->getUserById($id_user);
        } else {
            $error = "Não foi possível atualizar o perfil.";
        }
    }
}

include __DIR__ . '/../includes/header_ui.php';
?>

<div class="container mt-4">
    <h1 class="display-5 border-bottom pb-2 mb-4">O meu perfil</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="profile.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input 
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de utilizador</label>
                    <input 
                        type="text"
                        class="form-control"
                        value="<?php echo htmlspecialchars($user['role_name'] ?? ''); ?>"
                        disabled
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Guardar alterações
                </button>

                <a href="../index.php" class="btn btn-secondary">
                    Voltar
                </a>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
