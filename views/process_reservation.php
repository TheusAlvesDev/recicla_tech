<?php
require_once __DIR__ . '/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
$redirect = $isAdmin ? 'admin.php' : 'perfil.php?aba=doacoes';
$fragment = $isAdmin ? '#reservas' : '';
$separator = str_contains($redirect, '?') ? '&' : '?';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect . $fragment);
    exit;
}

$reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$reservationId || !in_array($action, ['approve', 'reject'], true)) {
    header('Location: ' . $redirect . $separator . 'reservation_error=invalid_action' . $fragment);
    exit;
}

try {
    $pdo->beginTransaction();

    // Impede que admin e doador processem o mesmo pedido simultaneamente.
    $stmt = $pdo->prepare("SELECT r.id, r.device_id, r.adopter_email, r.status, d.user_id AS owner_id
        FROM reservations r
        JOIN devices d ON d.id = r.device_id
        WHERE r.id = ?
        FOR UPDATE");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();

    if (!$reservation || (!$isAdmin && (int) $reservation['owner_id'] !== (int) $_SESSION['user_id'])) {
        $pdo->rollBack();
        header('Location: ' . $redirect . $separator . 'reservation_error=not_allowed' . $fragment);
        exit;
    }

    if ($reservation['status'] !== 'pending') {
        $pdo->rollBack();
        header('Location: ' . $redirect . $separator . 'reservation_error=already_processed' . $fragment);
        exit;
    }

    $newReservationStatus = $action === 'approve' ? 'approved' : 'rejected';
    $newDeviceStatus = $action === 'approve' ? 'reserved' : 'available';

    $updateReservation = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ? AND status = 'pending'");
    $updateReservation->execute([$newReservationStatus, $reservationId]);
    if ($updateReservation->rowCount() !== 1) {
        throw new RuntimeException('Solicitação já processada.');
    }

    $pdo->prepare('UPDATE devices SET status = ? WHERE id = ?')->execute([$newDeviceStatus, $reservation['device_id']]);

    if ($action === 'approve') {
        $adopterStmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $adopterStmt->execute([$reservation['adopter_email']]);
        $adopterId = $adopterStmt->fetchColumn();

        if ($adopterId) {
            $pointsStmt = $pdo->prepare("SELECT points_value FROM points_config WHERE action_key = 'reserva_aprovada'");
            $pointsStmt->execute();
            $points = (int) ($pointsStmt->fetchColumn() ?: 30);
            $pdo->prepare('UPDATE users SET points = points + ? WHERE id = ?')->execute([$points, $adopterId]);
        }
    }

    $pdo->commit();
    header('Location: ' . $redirect . $separator . 'reservation_result=' . ($action === 'approve' ? 'approved' : 'rejected') . $fragment);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: ' . $redirect . $separator . 'reservation_error=db_failure' . $fragment);
    exit;
}
