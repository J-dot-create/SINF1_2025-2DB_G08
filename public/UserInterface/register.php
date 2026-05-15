<?php 
include '../includes/header_ui.php'; 

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $message = "As passwords não coincidem!";
        $messageType = "danger";
    } else {
        $result = $bll->registerUser($name, $email, $password);
        
        if ($result === true) {
            $message = "Registo efetuado com sucesso! Já podes iniciar sessão.";
            $messageType = "success";
        } else {
            $message = $result;
            $messageType = "danger";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Criar Conta de Estudante</h4>
            </div>
            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Endereço de Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
    <label for="password" class="form-label">Password</label>

    <div class="input-group">
        <input 
            type="password" 
            class="form-control" 
            id="password" 
            name="password" 
            required
        >

        <button 
            type="button" 
            class="btn btn-outline-secondary" 
            data-toggle-password="password"
        >
            Mostrar
        </button>
    </div>
</div>

<div class="mb-3">
    <label for="confirm_password" class="form-label">Confirmar Password</label>

    <div class="input-group">
        <input 
            type="password" 
            class="form-control" 
            id="confirm_password" 
            name="confirm_password" 
            required
        >

        <button 
            type="button" 
            class="btn btn-outline-secondary" 
            data-toggle-password="confirm_password"
        >
            Mostrar
        </button>
    </div>
</div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Registar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                Já tens conta? <a href="login.php">Inicia Sessão aqui</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
