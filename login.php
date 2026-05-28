<?php 
// Garante que o config.php seja carregado e a sessão iniciada
require 'config.php'; 

// 1. Redireciona para Index se JÁ estiver logado
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Mensagens de erro ou sucesso (tratamento básico de URL params)
$msg = '';
if (isset($_GET['error'])) {
    $msg = '<div class="alert alert-danger">Credenciais inválidas ou conta não existe.</div>';
} elseif (isset($_GET['success']) && $_GET['success'] == 'registered') {
    $msg = '<div class="alert alert-success">Cadastro realizado com sucesso! Faça login.</div>';
}

// Se o usuário já estiver logado, redireciona para a página inicial
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
</head>

<br><br><br>

<div class="text-center">
    <img src="assets/img/ReciclaTech.png" width="90px" height="90px" alt="Logo ReciclaTech">
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-lg">
                <h3 class="text-center mb-3">Entrar no ReciclaTech</h3>

                <?= $msg ?>

                <form action="login_action.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button class="btn btn-success w-100">Entrar</button>
                </form>
                <p class="text-center mt-3">Não tem conta? <a href="register.php">Cadastre-se</a></p>
            </div>
        </div>
    </div>
</div>

<?php require 'templates/footer.php'; ?>