<!-- templates/header.php -->
<?php if(!isset($pdo)) require_once __DIR__ . '/../config.php'; ?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">ReciclaTech</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto">

                    <?php if(isset($_SESSION['user_id'])): // Usuário logado ?>
                    <li class="nav-item"><a class="nav-link" href="donate.php">Doar aparelho</a></li>
                    <li class="nav-item"><a class="nav-link" href="adote.php">Adotar aparelho</a></li>
                    <li class="nav-item"><a class="nav-link" href="ranking.php">Ranking</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="recycle.php">Reciclar</a></li>

                    <?php if(isset($_SESSION['user_id'])): // Usuário logado ?>
                    <li class="nav-item"><span class="nav-link text-white">Olá, <?= e($_SESSION['user_nome']); ?></span>
                    </li>
                    <?php if($_SESSION['user_role'] === 'admin'): // Link do Admin apenas para admins ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    <?php else: // Usuário deslogado ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-4">