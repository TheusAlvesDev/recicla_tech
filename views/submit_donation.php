<!-- submit_donation.php -->
<?php
require 'config.php';
// 2. Redireciona para Login se NÃO estiver logado
if (!isset($_SESSION['user_id'])) {
    // Opcional: Adiciona um parâmetro 'redirect' para levar o usuário de volta 
    // a esta página após o login.
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'] ?? NULL;
$device_type = $_POST['type'] ?? null;
$brand = $_POST['brand'] ?? null;
$model = $_POST['model'] ?? null;
$condition = $_POST['condition'] ?? 'funcional';
$description = $_POST['description'] ?? null;


// upload simples (Atenção: em produção usar checks mais fortes)
$photoPath = null;

if (!empty($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
    $tmp = $_FILES['photo']['tmp_name'];
    $name = basename($_FILES['photo']['name']);
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array(strtolower($ext), $allowed)) {
        $newName = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
        if (!is_dir('uploads')) mkdir('uploads', 0755, true);
        move_uploaded_file($tmp, $newName);
        $photoPath = $newName;
    }
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO devices (user_id, device_type, brand, model, device_condition, description, photo, status) VALUES (:ui, :dt, :b, :m, :c, :d, :p, 'available')");

    // 2. Atualize o array de execução
    $stmt->execute([
        ':ui' => $user_id,
        ':dt' => $device_type,
        ':b' => $brand,
        ':m' => $model,
        ':c' => $condition, // O valor da variável '$condition' permanece o mesmo
        ':d' => $description,
        ':p' => $photoPath
    ]);

    $device_id = $pdo->lastInsertId();

    $stmtUser = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);


    // registrar doação

    $stmt2 = $pdo->prepare("
    INSERT INTO donations 
    (device_id, donor_name, donor_email, pickup_address) 
    VALUES (:did, :name, :email, :address)
");

    $stmt2->execute([
        ':did' => $device_id,
        ':name' => $user['name'] ?? '',
        ':email' => $user['email'] ?? '',
        ':address' => ''
    ]);


    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // 1. Buscar o valor de pontos por doação completa
        $stmt_points = $pdo->prepare("SELECT points_value FROM points_config WHERE action_key = 'doacao_completa'");
        $stmt_points->execute();
        $points_to_add = $stmt_points->fetchColumn() ?: 50;

        // 2. Atualizar a pontuação do usuário
        $stmt_update = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
        $stmt_update->execute([$points_to_add, $user_id]);
    }
    // --- FIM NOVA LÓGICA DE PONTOS ---


    // 3. Commit da transação (cerca da Linha 50)
    $pdo->commit();

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erro ao cadastrar doação: " . $e->getMessage());
}
