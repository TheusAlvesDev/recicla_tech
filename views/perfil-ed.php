<?php
require 'config.php';



// 1. VERIFICAR AUTENTICAÇÃO
/*if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. BUSCAR DADOS DO USUÁRIO
try {
    $stmt = $pdo->prepare("
        SELECT 
            name, 
            email, 
            address_street, 
            address_number, 
            address_complement, 
            address_city, 
            address_state, 
            address_zipcode 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Se o usuário foi deletado do DB, destrói a sessão
        session_destroy();
        header("Location: login.php?error=user_not_found");
        exit;
    }

    // 3. BUSCAR HISTÓRICO DE DOAÇÕES (Itens que o usuário doou)
    $donated_stmt = $pdo->prepare("SELECT * FROM devices WHERE user_id = ? ORDER BY created_at DESC");
    $donated_stmt->execute([$user_id]);
    $donated_items = $donated_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. BUSCAR HISTÓRICO DE ADOÇÕES (Itens que o usuário reservou/adotou)
    $adopted_stmt = $pdo->prepare("
        SELECT r.*, d.description AS device_name, d.status AS device_status 
        FROM reservations r
        JOIN devices d ON r.device_id = d.id
        WHERE r.device_id = ? 
        ORDER BY r.created_at DESC
    ");
    $adopted_stmt->execute([$user_id]);
    $adopted_items = $adopted_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Tratar erro de banco de dados
    die("Erro ao carregar dados do usuário: " . $e->getMessage());
}

*/

$user = [
    'name' => 'Nome Teste',
    'email' => 'teste@email.com',
    'address_street' => 'Rua Teste',
    'address_number' => '123',
    'address_complement' => '',
    'address_city' => 'Cidade',
    'address_state' => 'CE',
    'address_zipcode' => '00000-000'
];

$donated_items = [];
$adopted_items = [];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Perfil - ReciclaTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/stylesperfil-ed.css">

</head>

<body>

<div class="perfil-container">

    <div class="perfil-header">

        <div class="perfil-avatar">
            <i class="bi bi-person-circle perfil-avatar-icon"></i>
        </div>

        <div class="perfil-info">

            <span class="perfil-tag">
                Meu perfil
            </span>

            <h1 class="perfil-nome">
                <?= htmlspecialchars($user['name']) ?>

                <i class="bi bi-pencil-square editar-icon"></i>
            </h1>

        </div>

    </div>

    <div class="perfil-card">

        <h2>Editar dados</h2>

        <form action="update_profile.php" method="POST">

            <div class="grid-2">

                <div class="campo">
                    <label>Nome completo</label>
                    <input
                        type="text"
                        name="name"
                        value="<?= htmlspecialchars($user['name']) ?>">
                </div>

                <div class="campo">
                    <label>Email</label>
                    <input
                        type="email"
                        value="<?= htmlspecialchars($user['email']) ?>"
                        disabled>
                </div>

            </div>

            <div class="grid-3">

                <div class="campo campo-rua">
                    <label>Rua / Avenida</label>
                    <input
                        type="text"
                        name="address_street"
                        value="<?= htmlspecialchars($user['address_street']) ?>">
                </div>

                <div class="campo">
                    <label>CEP</label>
                    <input
                        type="text"
                        name="address_zipcode"
                        value="<?= htmlspecialchars($user['address_zipcode']) ?>">
                </div>

                <div class="campo">
                    <label>Número</label>
                    <input
                        type="text"
                        name="address_number"
                        value="<?= htmlspecialchars($user['address_number']) ?>">
                </div>

            </div>

            <div class="grid-3">

                <div class="campo">
                    <label>Complemento (Opcional)</label>
                    <input
                        type="text"
                        name="address_complement"
                        value="<?= htmlspecialchars($user['address_complement']) ?>">
                </div>

                <div class="campo">
                    <label>Cidade</label>
                    <input
                        type="text"
                        name="address_city"
                        value="<?= htmlspecialchars($user['address_city']) ?>">
                </div>

                <div class="campo">
                    <label>Estado</label>
                    <input
                        type="text"
                        name="address_state"
                        value="<?= htmlspecialchars($user['address_state']) ?>">
                </div>

            </div>

            <div class="senha-area">

                <h2>Alterar senha</h2>

                <p>
                    Preencha os campos abaixo apenas se desejar mudar sua senha.
                </p>

                <div class="grid-2">

                    <div class="campo">
                        <label>Nova senha</label>
                        <input
                            type="password"
                            name="new_password">
                    </div>

                    <div class="campo">
                        <label>Confirmar nova senha</label>
                        <input
                            type="password"
                            name="confirm_password">
                    </div>

                </div>

            </div>

            <div class="acoes">

                <button
                    type="button"
                    class="btn-cancelar">

                    Cancelar

                </button>

                <button
                    type="submit"
                    class="btn-salvar">

                    Salvar alterações

                </button>

            </div>

        </form>

    </div>

</div>

</body>
