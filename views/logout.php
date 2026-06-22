<?php
// logout.php
// Garante que a sessão existente seja iniciada
require 'config.php';
// session_start() é chamado via config.php

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se o cookie de sessão for usado, ele também deve ser apagado.
// Nota: Isto destruirá o cookie de sessão e não apenas os dados da sessão.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Finalmente, destrói a sessão
session_destroy();

// Redireciona o usuário para a página de login
header("Location: index.php?logged_out=1");
exit;
