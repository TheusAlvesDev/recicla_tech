<?php
require 'config.php';
// 2. Redireciona para Login se NÃO estiver logado
if(!isset($_SESSION['user_id'])){
    // Opcional: Adiciona um parâmetro 'redirect' para levar o usuário de volta 
    // a esta página após o login.
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$device_id = $_POST['device_id'] ?? null;
$adopter_name = $_POST['adopter_name'] ?? null;
$adopter_email = $_POST['adopter_email'] ?? null;
$purpose = $_POST['purpose'] ?? null;

// Validação básica
if (empty($device_id) || empty($adopter_name) || empty($adopter_email)) {
    header('Location: adote.php?error=missing_fields');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Registrar a reserva
    $stmt_res = $pdo->prepare("INSERT INTO reservations (device_id, adopter_name, adopter_email, purpose) VALUES (?, ?, ?, ?)");
    $stmt_res->execute([$device_id, $adopter_name, $adopter_email, $purpose]);

    // 2. Mudar o status do dispositivo para 'reserved' (reservado)
    $stmt_dev = $pdo->prepare("UPDATE devices SET status = 'reserved' WHERE id = ? AND status = 'available'");
    $stmt_dev->execute([$device_id]);

    $pdo->commit();

    header('Location: adote.php?msg=reserved');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    // Em produção, você deve logar o $e->getMessage()
    header('Location: adote.php?error=db_error');
    exit;
}
?>