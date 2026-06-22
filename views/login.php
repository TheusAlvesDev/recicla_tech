<?php
// Garante que o config.php seja carregado e a sessão iniciada
require 'config.php';

// Redireciona para Index se JÁ estiver logado
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Mensagens de erro ou sucesso
$msg = '';
if (isset($_GET['error'])) {
    $msg = '<div class="alert alert-danger">Credenciais inválidas ou conta não existe.</div>';
} elseif (isset($_GET['success']) && $_GET['success'] == 'registered') {
    $msg = '<div class="alert alert-success">Cadastro realizado com sucesso! Faça login.</div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReciclaTech - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleslogin.css">
</head>

<body>
    <div class="container-login">
        <main class="cartao-login">
            <div class="imagem-login">
                <img src="img/image-left.png" alt="ReciclaTech">
            </div>

            <div class="formulario-login">
                <div class="logo-pequena">
                    <img src="img/ReciclaTech.png" alt="Logo" class="logo">
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
                        <div class="campo-senha">
                            <input id="senha" type="password" name="senha" placeholder="Digite sua senha" required>
                            <button type="button" class="btn-olho" onclick="clicado(this)">
                                <img id="olhoImg" src="img/olhinho.png" alt="Mostrar senha">
                            </button>
                        </div>
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
        </main>
    </div>

    <script>
        function clicado(botao) {
            const senha = document.getElementById("senha");
            const olhoImg = botao.querySelector('img');

            if (senha.type === 'password') {
                senha.type = 'text';
                olhoImg.src = 'img/olhinhoFechado.png';
            } else {
                senha.type = 'password';
                olhoImg.src = 'img/olhinho.png';
            }
        }
    </script>
</body>

</html>