<?php
// register_action.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

// 1. COLETAR E PADRONIZAR VARIÁVEIS DO POST
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password_plain = $_POST['password'] ?? '';

// 2. Coletar os dados de endereço
$address_street = $_POST['address_street'] ?? '';
$address_number = $_POST['address_number'] ?? '';
$address_city = $_POST['address_city'] ?? '';
$address_state = $_POST['address_state'] ?? '';
$address_zipcode = $_POST['address_zipcode'] ?? '';

// Complemento é o único que permanece opcional
$address_complement = $_POST['address_complement'] ?? NULL;

// --- VALIDAÇÃO PHP DE CAMPOS OBRIGATÓRIOS ---
if (empty($name) || empty($email) || empty($password_plain) || 
    empty($address_street) || empty($address_number) || empty($address_city) || 
    empty($address_state) || empty($address_zipcode)) 
{
    // Redireciona com erro se algum campo obrigatório estiver vazio
    header('Location: register.php?error=missing_fields');
    exit;
}
// --- FIM DA VALIDAÇÃO ---

// Cria o hash seguro da senha
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// Insere na tabela 'users'
$stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, 
             address_street, address_number, address_complement, address_city, address_state, address_zipcode) VALUES (?, ?, ?, 'user', 
             ?, ?, ?, ?, ?, ?)");

try {
    $stmt->execute([
        $name, 
        $email, 
        $password_hash, 
        $address_street, 
        $address_number, 
        $address_complement, 
        $address_city, 
        $address_state, 
        $address_zipcode
    ]);
    
    // Redireciona para o login com sucesso
    header("Location: login.php?success=registered");
    exit;
} catch (PDOException $e) {
    // Código 23000 é geralmente violação de UNIQUE key (e-mail duplicado)
    if ($e->getCode() === '23000') {
        header("Location: register.php?error=email_exists");
        exit;
    }
    // Para debug, se necessário: die("Erro no banco de dados: " . $e->getMessage());
    header("Location: register.php?error=db_error");
    exit;
}