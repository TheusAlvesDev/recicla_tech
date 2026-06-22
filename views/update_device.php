<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}

$deviceId = filter_input(INPUT_POST, 'device_id', FILTER_VALIDATE_INT);
$deviceType = trim($_POST['device_type'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$model = trim($_POST['model'] ?? '');
$condition = $_POST['device_condition'] ?? '';
$description = trim($_POST['description'] ?? '');
$allowedConditions = ['novo', 'bom', 'funcional', 'com_defeito', 'para_pecas'];

if (!$deviceId || $deviceType === '' || $description === '' || !in_array($condition, $allowedConditions, true)) {
    header('Location: edit_device.php?id=' . (int) $deviceId . '&error=invalid_data');
    exit;
}

$ownership = $pdo->prepare("SELECT photo FROM devices WHERE id = ? AND user_id = ? AND status = 'available'");
$ownership->execute([$deviceId, $_SESSION['user_id']]);
$device = $ownership->fetch();
if (!$device) {
    header('Location: perfil.php?error=edit_not_allowed');
    exit;
}

$photoPath = $device['photo'];
if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK || $_FILES['photo']['size'] > 5 * 1024 * 1024) {
        header('Location: edit_device.php?id=' . $deviceId . '&error=invalid_photo');
        exit;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['photo']['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        header('Location: edit_device.php?id=' . $deviceId . '&error=invalid_photo');
        exit;
    }

    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $fileName = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . '/' . $fileName)) {
        header('Location: edit_device.php?id=' . $deviceId . '&error=upload_failed');
        exit;
    }
    $photoPath = 'uploads/' . $fileName;
}

$stmt = $pdo->prepare("UPDATE devices SET device_type = ?, brand = ?, model = ?, device_condition = ?, description = ?, photo = ?
    WHERE id = ? AND user_id = ? AND status = 'available'");
$stmt->execute([$deviceType, $brand ?: null, $model ?: null, $condition, $description, $photoPath, $deviceId, $_SESSION['user_id']]);

header('Location: perfil.php?success=device_updated#donations');
exit;
