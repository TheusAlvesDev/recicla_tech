<?php
require 'config.php';
// 2. Redireciona para Login se NÃO estiver logado
if(!isset($_SESSION['user_id'])){
    // Opcional: Adiciona um parâmetro 'redirect' para levar o usuário de volta 
    // a esta página após o login.
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adote.php');
    exit;
}

$device_id = filter_input(INPUT_POST, 'device_id', FILTER_VALIDATE_INT);
$adopter_name = trim($_POST['adopter_name'] ?? '');
$adopter_email = trim($_POST['adopter_email'] ?? '');
$purpose = trim($_POST['purpose'] ?? '');

// Validação básica
if (!$device_id || $adopter_name === '' || !filter_var($adopter_email, FILTER_VALIDATE_EMAIL)) {
    header('Location: adote.php?error=missing_fields');
    exit;
}

try {
    $pdo->beginTransaction();

    // Reserva o dispositivo de forma atômica, impedindo duas solicitações simultâneas.
    $stmt_dev = $pdo->prepare("UPDATE devices SET status = 'reserved' WHERE id = ? AND status = 'available'");
    $stmt_dev->execute([$device_id]);
    if ($stmt_dev->rowCount() !== 1) {
        $pdo->rollBack();
        header('Location: adote.php?error=unavailable');
        exit;
    }

    $stmt_res = $pdo->prepare("INSERT INTO reservations (device_id, adopter_name, adopter_email, purpose) VALUES (?, ?, ?, ?)");
    $stmt_res->execute([$device_id, $adopter_name, $adopter_email, $purpose ?: null]);

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
