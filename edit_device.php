<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_GET['id'] ?? null;

if (!$device_id) {
    header("Location: perfil.php?error=no_device_id");
    exit;
}

try {
    // Busca o item e verifica se ele pertence ao usuário E se está 'available'
    $stmt = $pdo->prepare("
        SELECT * FROM devices 
        WHERE id = ? AND user_id = ? AND status = 'available'
    ");
    $stmt->execute([$device_id, $user_id]);
    $device = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$device) {
        // Se não encontrar ou o status não permitir edição
        header("Location: perfil.php?error=edit_not_allowed");
        exit;
    }

} catch (PDOException $e) {
    die("Erro ao carregar dados do dispositivo: " . $e->getMessage());
}

require 'templates/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4">Editar Doação: <?= htmlspecialchars($device['device_type']) ?></h2>

    <form action="update_device.php" method="POST">
        <input type="hidden" name="device_id" value="<?= htmlspecialchars($device['id']) ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Nome do Dispositivo</label>
            <input type="text" class="form-control" id="name" name="name"
                value="<?= htmlspecialchars($device['device_type']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Categoria</label>
            <input type="text" class="form-control" id="category" name="category"
                value="<?= htmlspecialchars($device['device_condition']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="details" class="form-label">Detalhes / Condição</label>
            <textarea class="form-control" id="details" name="details" rows="4"
                required><?= htmlspecialchars($device['description']) ?></textarea>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="perfil.php#donations" class="btn btn-secondary">Voltar ao Perfil</a>
            <button type="submit" class="btn btn-success btn-lg">Salvar Alterações</button>
        </div>
    </form>
</div>

<?php require 'templates/footer.php'; ?>