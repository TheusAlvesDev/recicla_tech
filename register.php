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
    if ($_GET['error'] == 'email_exists') {
        $msg = '<div class="alert alert-danger">Erro: Este e-mail já está cadastrado.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Erro ao cadastrar. Tente novamente.</div>';
    }
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
</head>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Criar Nova Conta</h4>
                </div>
                <div class="card-body">
                    <form action="register_action.php" method="POST">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <h5 class="mt-4 mb-3 text-primary">Informações de Endereço</h5>

                        <div class="row">
                            <div class="col-md-9 mb-3">
                                <label for="address_street" class="form-label">Rua / Avenida</label>
                                <input type="text" class="form-control" id="address_street" name="address_street"
                                    required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="address_number" class="form-label">Número</label>
                                <input type="text" class="form-control" id="address_number" name="address_number"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="address_complement" class="form-label">Complemento (Opcional)</label>
                                <input type="text" class="form-control" id="address_complement"
                                    name="address_complement">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address_zipcode" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="address_zipcode" name="address_zipcode"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="address_city" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="address_city" name="address_city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address_state" class="form-label">Estado</label>
                                <input type="text" class="form-control" id="address_state" name="address_state"
                                    required>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Cadastrar</button>
                        </div>

                        <p class="mt-3 text-center">Já tem conta? <a href="login.php">Faça Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'templates/footer.php'; ?>