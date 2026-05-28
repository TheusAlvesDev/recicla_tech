<?php
// delete_device.php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_GET['id'] ?? null; 
$POINTS_DEDUCTION = 50; // Defina o valor de pontos a ser subtraído (Ajuste conforme sua regra)

if (!$device_id) {
    header("Location: perfil.php?error=no_device_id");
    exit;
}

try {
    // 1. BUSCAR PONTOS DO ITEM DELETADO (Assumindo que você usa uma pontuação fixa de dedução)
    // Se você tiver uma coluna 'points_earned' na tabela devices, busque ela aqui.
    // Usaremos a constante $POINTS_DEDUCTION (5 pontos) como exemplo.

    // INICIA TRANSAÇÃO: Garante que ou a deleção E a subtração de pontos aconteçam, ou nada aconteça.
    $pdo->beginTransaction();

    // 2. DELETAR ITEM
    // Deleta o item APENAS se ele pertencer ao usuário E se estiver 'available'
    $stmt_delete = $pdo->prepare("
        DELETE FROM devices 
        WHERE id = ? AND user_id = ? AND status = 'available'
    ");
    
    $stmt_delete->execute([$device_id, $user_id]);
    
    if ($stmt_delete->rowCount() > 0) {
        // Se o item foi deletado (status era 'available'), deduz os pontos.

        // 3. ATUALIZAR PONTUAÇÃO DO USUÁRIO
        // Subtrai o valor definido na constante $POINTS_DEDUCTION da coluna 'points' do usuário.
        $stmt_points = $pdo->prepare("
            UPDATE users 
            SET points = points - ? 
            WHERE id = ?
        ");
        $stmt_points->execute([$POINTS_DEDUCTION, $user_id]);

        // 4. CONFIRMAR TRANSAÇÃO
        $pdo->commit();

        // Sucesso na exclusão
        header("Location: perfil.php?success=device_deleted&deducted={$POINTS_DEDUCTION}#donations");
    } else {
        // Item não encontrado, status não permitia a exclusão, ou não pertence ao usuário.
        $pdo->rollBack(); // Cancela a transação (se algo começou)
        header("Location: perfil.php?error=delete_not_allowed#donations");
    }
    exit;
} catch (PDOException $e) {
    $pdo->rollBack(); // Em caso de erro, desfaz tudo
    // Tratar erro do banco de dados
    header("Location: perfil.php?error=db_delete_failed#donations");
    // Opcional para debug: die("Erro: " . $e->getMessage());
    exit;
}