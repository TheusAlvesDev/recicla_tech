<?php
// config.php — ajuste conforme seu ambiente
session_start();


$db_host = 'localhost';
$db_name = 'reciclatech';
$db_user = 'root';
$db_pass = ''; // coloque a senha do seu MySQL


$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];


try {
$pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, $options);
} catch (PDOException $e) {
die('DB Connection failed: ' . $e->getMessage());
}


// Funções utilitárias simples
function e($str){ return htmlspecialchars($str, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }


?>