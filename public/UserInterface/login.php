<?php
// Start session BEFORE any HTML output
session_start();
require_once __DIR__ . '/../../src/bll/BusinessLogicLayer.php';
$bll = new BusinessLogicLayer();

// If user is already logged in, redirect them away from login page
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role_id'] == 1) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = $bll->loginUser($email, $password);
    
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role_id'] = $user['id_role'];
        
        // Redirect based on user role (1 = Admin, 2 = Student)
        if ($user['id_role'] == 1) {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } else {
        $message = "Email ou password inválidos.";
    }
}

// Now we can safely include the UI header
include '../includes/header_ui.php'; 
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Iniciar Sessão</h4>
            </div>
            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
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
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dark btn-lg">Entrar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                Ainda não tens conta? <a href="register.php">Regista-te aqui</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
