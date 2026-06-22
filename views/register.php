<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$msg = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'email_exists') {
        $msg = '<div class="alert alert-danger">Erro: Este e-mail já está cadastrado.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Erro ao cadastrar. Tente novamente.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReciclaTech - Cadastro</title>
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
                    <a href="login.php" id="voltar01" style="display: block;">
                        <img src="img/setaEsquerda.png" alt="setinha" class="setinha">
                    </a>
                    <a href="javascript:voltar()" id="voltar02" style="display: none;">
                        <img src="img/setaEsquerda.png" alt="setinha" class="setinha">
                    </a>
                    <img src="img/ReciclaTech.png" alt="Logo" class="logo">
                    <span>ReciclaTech</span>
                </div>

                <h2>Insira os dados abaixo</h2>

                <?= $msg ?>

                <form action="register_action.php" method="POST">
                    <div style="display: block;" id="register01">
                        <div class="grupo-campo">
                            <label>Nome completo</label>
                            <input type="text" id="nome" name="name" placeholder="Insira seu nome completo" required>
                        </div>

                        <div class="grupo-campo">
                            <label>Email</label>
                            <input type="email" id="email" name="email" placeholder="exemplo@gmail.com" required>
                        </div>

                        <div class="grupo-campo">
                            <label>Senha</label>
                            <div class="campo-senha">
                                <input id="senha" type="password" name="password" placeholder="Insira sua senha" required>
                                <button type="button" class="btn-olho" onclick="clicado(this)">
                                    <img id="olhoImg" src="img/olhinho.png" alt="Mostrar senha">
                                </button>
                            </div>
                        </div>

                        <button type="button" class="botao-login" onclick="mostrar()">
                            Avançar →
                        </button>
                    </div>

                    <div style="display: none;" id="register02">
                        <div class="row-campos">
                            <div class="grupo-campo">
                                <label>Rua/Avenida</label>
                                <input type="text" name="street" placeholder="Insira sua rua" required>
                            </div>
                            <div class="grupo-campo">
                                <label>Número</label>
                                <input type="text" name="number" placeholder="Número" required>
                            </div>
                        </div>

                        <div class="row-campos">
                            <div class="grupo-campo">
                                <label>Complemento (Opcional)</label>
                                <input type="text" name="complement" placeholder="Apto, Bloco, etc">
                            </div>
                            <div class="grupo-campo">
                                <label>CEP</label>
                                <input type="text" name="zipcode" placeholder="00000-000" required>
                            </div>
                        </div>

                        <div class="row-campos">
                            <div class="grupo-campo">
                                <label>Cidade</label>
                                <input type="text" name="city" placeholder="Sua cidade" required>
                            </div>
                            <div class="grupo-campo">
                                <label>Estado</label>
                                <input type="text" name="state" placeholder="UF" required>
                            </div>
                        </div>

                        <button type="submit" class="botao-login">
                            Cadastrar
                        </button>
                    </div>
                </form>

                <p class="link-cadastro" id="link-login-container" style="display: block;">
                    Já tem uma conta?
                    <a href="login.php">Log in</a>
                </p>
            </div>
        </main>
    </div>

    <script>
        function mostrar() {
            let nome = document.getElementById("nome").value;
            let email = document.getElementById("email").value;
            let pass = document.getElementById("senha").value;

            if (nome !== "" && email !== "" && pass !== "") {
                document.getElementById("register01").style.display = "none";
                document.getElementById("register02").style.display = "block";
                document.getElementById("voltar01").style.display = "none";
                document.getElementById("voltar02").style.display = "block";
                
                // Força o link a continuar aparecendo na segunda tela
                document.getElementById("link-login-container").style.display = "block";
                
                document.querySelector(".formulario-login h2").innerText = "Informe seu endereço";
            } else {
                alert("Preencha todos os campos!");
            }
        }

        function voltar() {
            document.getElementById("register01").style.display = "block";
            document.getElementById("register02").style.display = "none";
            document.getElementById("voltar01").style.display = "block";
            document.getElementById("voltar02").style.display = "none";
            document.getElementById("link-login-container").style.display = "block";
            
            document.querySelector(".formulario-login h2").innerText = "Insira os dados abaixo";
        }

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