<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$deviceId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$deviceId) {
    header('Location: perfil.php?error=no_device_id');
    exit;
}

$stmt = $pdo->prepare("SELECT id, device_type, brand, model, device_condition, description, photo, status
    FROM devices WHERE id = ? AND user_id = ? AND status = 'available'");
$stmt->execute([$deviceId, $_SESSION['user_id']]);
$device = $stmt->fetch();

if (!$device) {
    header('Location: perfil.php?error=edit_not_allowed');
    exit;
}

$conditions = [
    'novo' => 'Novo',
    'bom' => 'Bom estado',
    'funcional' => 'Funcional',
    'com_defeito' => 'Com defeito',
    'para_pecas' => 'Para peças',
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar doação | ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/edit-device.css">
</head>

<body class="edit-device-page">
    <section class="edit-device-topo">
        <?php require 'templates/header.php'; ?>
        <div class="edit-device-hero">
            <a href="perfil.php#donations" class="edit-device-voltar"><i class="bi bi-arrow-left"></i> Voltar ao perfil</a>
            <span class="edit-device-eyebrow">Minhas doações</span>
            <h2>Editar dispositivo</h2>
            <p>Atualize as informações do item para que quem deseja adotá-lo saiba exatamente o que esperar.</p>
        </div>
    </section>

    <main class="edit-device-conteudo">
        <?php if (isset($_GET['error'])): ?>
            <div class="edit-device-alerta" role="alert"><i class="bi bi-exclamation-circle"></i> Não foi possível salvar as alterações. Confira os dados e tente novamente.</div>
        <?php endif; ?>

        <form class="edit-device-card" action="update_device.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="device_id" value="<?= (int) $device['id'] ?>">

            <aside class="edit-device-foto">
                <div class="edit-device-preview-wrap">
                    <img id="preview-foto" src="<?= e($device['photo'] ?: '../img/equipamentosQuebrados.webp') ?>" alt="Foto atual do dispositivo">
                    <span><i class="bi bi-image"></i> Foto do anúncio</span>
                </div>
                <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" hidden>
                <button class="edit-device-trocar-foto" type="button" id="selecionar-foto"><i class="bi bi-camera"></i> Alterar foto</button>
                <small>JPG, PNG ou WEBP. A foto atual será mantida se nenhuma nova imagem for escolhida.</small>
            </aside>

            <section class="edit-device-formulario">
                <div class="edit-device-card-titulo">
                    <div><span>Informações do anúncio</span>
                        <h1><?= e(trim($device['device_type'] . ' ' . $device['brand'] . ' ' . $device['model'])) ?></h1>
                    </div>
                    <span class="edit-device-status"><i></i> Disponível</span>
                </div>

                <div class="edit-device-campos">
                    <div class="edit-device-campo campo-largo">
                        <label for="device_type">Tipo de aparelho</label>
                        <input id="device_type" name="device_type" type="text" value="<?= e($device['device_type']) ?>" maxlength="100" required>
                    </div>
                    <div class="edit-device-campo">
                        <label for="brand">Marca</label>
                        <input id="brand" name="brand" type="text" value="<?= e($device['brand']) ?>" maxlength="100">
                    </div>
                    <div class="edit-device-campo">
                        <label for="model">Modelo</label>
                        <input id="model" name="model" type="text" value="<?= e($device['model']) ?>" maxlength="100">
                    </div>
                    <div class="edit-device-campo campo-largo">
                        <label for="device_condition">Condição</label>
                        <select id="device_condition" name="device_condition" required>
                            <?php foreach ($conditions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $device['device_condition'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="edit-device-campo campo-largo">
                        <label for="description">Descrição</label>
                        <textarea id="description" name="description" rows="6" maxlength="2000" required><?= e($device['description']) ?></textarea>
                        <small>Descreva o funcionamento, sinais de uso, defeitos e acessórios inclusos.</small>
                    </div>
                </div>

                <div class="edit-device-acoes">
                    <a href="perfil.php#donations" class="edit-device-cancelar">Cancelar</a>
                    <button type="submit" class="edit-device-salvar"><i class="bi bi-check-lg"></i> Salvar alterações</button>
                </div>
            </section>
        </form>
    </main>

    <?php require 'templates/footer.php'; ?>
    <script src="js/dropdown.js"></script>
    <script>
        const inputFoto = document.getElementById('photo');
        const previewFoto = document.getElementById('preview-foto');
        document.getElementById('selecionar-foto')?.addEventListener('click', () => inputFoto.click());
        inputFoto?.addEventListener('change', () => {
            const arquivo = inputFoto.files[0];
            if (arquivo) previewFoto.src = URL.createObjectURL(arquivo);
        });
    </script>
</body>

</html>