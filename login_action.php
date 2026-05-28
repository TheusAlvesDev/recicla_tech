<?php
// login_action.php
require 'config.php'; 
// session_start() é chamado via config.php

// Redireciona se não for POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Busca o usuário pelo e-mail
$stmt = $pdo->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 1. Verifica se o usuário foi encontrado E 2. Se a senha está correta
if($user && password_verify($senha, $user['password_hash'])){
    // Credenciais válidas: inicia a sessão
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nome'] = $user['name']; 
    $_SESSION['user_role'] = $user['role'];
    
    // Redireciona para a página inicial
    header("Location: index.php");
    exit;
}

// Credenciais inválidas
header("Location: login.php?error=invalid");
exit;