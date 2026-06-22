<?php
// update_profile.php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Coleta dos dados de endereço
$name = $_POST['name'] ?? '';
$address_street = $_POST['address_street'] ?? '';
$address_number = $_POST['address_number'] ?? '';
$address_complement = $_POST['address_complement'] ?? NULL;
$address_city = $_POST['address_city'] ?? '';
$address_state = $_POST['address_state'] ?? '';
$address_zipcode = $_POST['address_zipcode'] ?? '';

// Coleta e validação da nova senha
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Array para armazenar os campos a serem atualizados
$update_fields = [
    'name' => $name,
    'address_street' => $address_street,
    'address_number' => $address_number,
    'address_complement' => $address_complement,
    'address_city' => $address_city,
    'address_state' => $address_state,
    'address_zipcode' => $address_zipcode,
];
$sql_sets = [];
$execute_params = [];

// Monta a query para os campos de endereço/nome
foreach ($update_fields as $key => $value) {
    $sql_sets[] = "{$key} = ?";
    $execute_params[] = $value;
}

// 1. Lógica de atualização de senha (se fornecida)
if (!empty($new_password)) {
    if ($new_password !== $confirm_password) {
        header("Location: perfil.php?error=password_mismatch");
        exit;
    }
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $sql_sets[] = "password_hash = ?";
    $execute_params[] = $password_hash;
}

// 2. Execução da atualização
if (!empty($sql_sets)) {
    $sql = "UPDATE users SET " . implode(', ', $sql_sets) . " WHERE id = ?";
    $execute_params[] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($execute_params);
        
        header("Location: perfil.php?success=true");
        exit;
    } catch (PDOException $e) {
        // Tratar erro do banco de dados
        // Para debug: die("Erro: " . $e->getMessage());
        header("Location: perfil.php?error=db_error");
        exit;
    }
} else {
    header("Location: perfil.php?error=no_data");
    exit;
}
?>