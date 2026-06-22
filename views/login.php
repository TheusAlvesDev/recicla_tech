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

    <link rel="stylesheet" href="css/styleslogin.css">


</head>

<br><br><br>


<div class="container-login">

    <div class="cartao-login">

        <div class="imagem-login">
            <img src="img/image-left.png" alt="ReciclaTech">
        </div>

        <div class="formulario-login">

            <div class="logo-pequena">
                <img src="img/ReciclaTech.png" alt="Logo">
                <span>ReciclaTech</span>
            </div>

            <h2>Bem vindo(a) de volta!</h2>

            <?= $msg ?>

            <form action="login_action.php" method="POST">

                <div class="grupo-campo">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Digite seu email" required>
                </div>

                <div class="grupo-campo">
                    <label>Senha</label>
                    <input type="password" name="senha" placeholder="Digite sua senha" required>
                </div>

                <div class="opcoes-login">

                    <label class="lembrar-login">
                        <input type="checkbox">
                        Lembre de mim
                    </label>

                    <a href="" class="esqueci-senha">
                        Esqueceu sua senha?
                    </a>

                </div>

                <button type="submit" class="botao-login">
                    Log in
                </button>

            </form>

            <p class="link-cadastro">
                Não tem uma conta?
                <a href="register.php">Se cadastre</a>
            </p>

        </div>

    </div>

</div>