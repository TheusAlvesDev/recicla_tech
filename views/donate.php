<?php require 'config.php';
// 2. Redireciona para Login se NÃO estiver logado
if (!isset($_SESSION['user_id'])) {
    // Opcional: Adiciona um parâmetro 'redirect' para levar o usuário de volta 
    // a esta página após o login.
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova Doação</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/stylesdonate.css">

</head>

<body>

    <?php //require 'templates/header.php'; 
    ?>

    <div class="container py-5">


        <div class="cartao-doacao">

            <h1 class="titulo-pagina">
                Fazer nova doação
            </h1>

            <form action="submit_donation.php" method="post" enctype="multipart/form-data">

                <div class="row">

                    <div class="col-md-3">

                        <div class="area-foto">

                            <img id="previewFoto" class="preview-foto" style="display: none;">

                            <i class="bi bi-card-image icone-foto" id="iconeFoto"></i>

                            <input
                                type="file"
                                id="selecionarFoto"
                                name="photo"
                                accept="image/*"
                                hidden>

                            <button
                                type="button"
                                class="btn botao-foto"
                                onclick="document.getElementById('selecionarFoto').click()">
                                Selecionar Fotos
                            </button>

                        </div>

                    </div>

                    <div class="col-md-9">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label>Nome</label>
                                <input type="text" name="nome" class="form-control campo" required>
                            </div>

                            <div class="col-md-6">
                                <label>Tipo de aparelho</label>
                                <input type="text" name="type" class="form-control campo" required>
                            </div>

                            <div class="col-md-4">
                                <label>Marca</label>
                                <input type="text" name="brand" class="form-control campo" required>
                            </div>

                            <div class="col-md-4">
                                <label>Modelo</label>
                                <input type="text" name="model" class="form-control campo" required>
                            </div>

                            <div class="col-md-4">
                                <label>Condição</label>

                                <select class="form-select campo" name="condition" required>
                                    <option value="funcional">Funcional</option>
                                    <option value="com_defeito">Com defeito</option>
                                    <option value="para_pecas">Para peças</option>
                                </select>
                            </div>

                            <div class="col-12">

                                <label>Descrição</label>

                                <textarea name="description"
                                    class="form-control campo descricao"
                                    placeholder="Descreva o aparelho" required></textarea>

                            </div>

                        </div>

                        <div class="area-botoes">

                            <a href="javascript:history.back()"><button
                                    type="button"
                                    class="btn botao-cancelar">
                                    Cancelar
                                </button></a>


                            <button
                                type="submit"
                                class="btn botao-enviar">
                                Enviar
                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>
</body>

<script>
    const inputFoto = document.getElementById('selecionarFoto');
    const previewFoto = document.getElementById('previewFoto');
    const iconeFoto = document.getElementById('iconeFoto');

    inputFoto.addEventListener('change', function() {
        const arquivo = this.files[0];

        if (arquivo) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewFoto.src = e.target.result;
                previewFoto.style.display = 'block';
                iconeFoto.style.display = 'none';
            };

            reader.readAsDataURL(arquivo);
        }
    });

    function voltar() {
        window.location.href = "index.php";
    }
</script>

</html>