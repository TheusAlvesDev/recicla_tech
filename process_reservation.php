<?php
require 'config.php';

// 1. Proteção de Acesso
// Garante que apenas administradores logados possam executar este script
if(empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){ 
    header('Location: login.php'); 
    exit; 
}

// Garante que o método seja POST (segurança básica)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$reservation_id = $_POST['reservation_id'] ?? null;
$action = $_POST['action'] ?? null; // 'approve' ou 'reject'

// Validação de entrada
if (!$reservation_id || !in_array($action, ['approve', 'reject'])) {
    header('Location: dashboard.php?error=invalid_action');
    exit;
}

try {
    // Inicia uma Transação para garantir que ambas as operações (reserva e dispositivo) sejam feitas ou nenhuma seja feita.
    $pdo->beginTransaction();

    // 1. Pega o device_id ANTES de atualizar
    $stmt = $pdo->prepare("SELECT device_id FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        $pdo->rollBack();
        header('Location: dashboard.php?error=reservation_not_found');
        exit;
    }

    $device_id = $reservation['device_id'];
    $device_new_status = '';
    $reservation_new_status = '';
    $message = '';

// ...
    if ($action === 'approve') {
        $reservation_new_status = 'approved';
        $device_new_status = 'reserved'; 
        $message = 'Reserva aprovada com sucesso! O dispositivo está agora marcado como reservado.';

        // --- NOVA LÓGICA DE PONTOS PARA ADOTANTE ---
        
        // 1. Buscar o ID do usuário (adotante) pelo email na tabela reservations
        $stmt_user_email = $pdo->prepare("SELECT adopter_email FROM reservations WHERE id = ?");
        $stmt_user_email->execute([$reservation_id]);
        $adopter_email = $stmt_user_email->fetchColumn();

        $stmt_user_id = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_user_id->execute([$adopter_email]);
        $adopter_user_id = $stmt_user_id->fetchColumn();

        if ($adopter_user_id) {
            // 2. Buscar o valor de pontos por reserva aprovada
            $stmt_points = $pdo->prepare("SELECT points_value FROM points_config WHERE action_key = 'reserva_aprovada'");
            $stmt_points->execute();
            $points_to_add = $stmt_points->fetchColumn() ?: 30; // Usa 30 como fallback
            
            // 3. Atualizar a pontuação do adotante
            $stmt_update = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
            $stmt_update->execute([$points_to_add, $adopter_user_id]);
        }
        // --- FIM NOVA LÓGICA DE PONTOS ---

    } else { // 'reject'

        $reservation_new_status = 'rejected';
        $device_new_status = 'available'; // Volta a ser listado para adoção
        $message = 'Reserva rejeitada. O dispositivo voltou a ser listado como disponível.';
    }

    // 2. Atualiza o status da Reserva
    $stmt_res = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt_res->execute([$reservation_new_status, $reservation_id]);

    // 3. Atualiza o status do Dispositivo
    $stmt_dev = $pdo->prepare("UPDATE devices SET status = ? WHERE id = ?");
    $stmt_dev->execute([$device_new_status, $device_id]);

    $pdo->commit();
    
    header('Location: dashboard.php?success=' . urlencode($message));
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    // Em um sistema real, você registraria $e->getMessage() em logs
    header('Location: dashboard.php?error=db_failure');
    exit;
}
?>