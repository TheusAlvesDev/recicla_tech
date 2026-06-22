<?php
$paginaAtual = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$inicioAtivo = $paginaAtual === 'index.php';
$rankingAtivo = $paginaAtual === 'ranking.php';
$doacoesAtivo = in_array($paginaAtual, [
    'donation_list.php',
    'adote.php',
    'donate.php',
    'edit_device.php',
], true);
?>
<header>
    <div class="logo">
        <a href="index.php"><img src="../img/ReciclaTech 1.png" alt="logo"></a>
        <h1 style="color: white;">ReciclaTech</h1>
    </div>
    <button class="menu-mobile-toggle" type="button" onclick="alternarMenuMobile()" aria-label="Abrir menu" aria-expanded="false" aria-controls="menu-principal">
        <span></span><span></span><span></span>
    </button>
    <div class="menu-direita" id="menu-principal">
        <nav>
            <a class="<?= $inicioAtivo ? 'menu-ativo' : '' ?>" href="index.php" <?= $inicioAtivo ? 'aria-current="page"' : '' ?>>Início</a>
            <a class="<?= $doacoesAtivo ? 'menu-ativo' : '' ?>" href="donation_list.php" <?= $doacoesAtivo ? 'aria-current="page"' : '' ?>>Doações</a>
            <a class="<?= $rankingAtivo ? 'menu-ativo' : '' ?>" href="ranking.php" <?= $rankingAtivo ? 'aria-current="page"' : '' ?>>Ranking</a>
            <a href="index.php#footer">Contato</a>
        </nav>

        <?php if (isset($_SESSION['user_id'])): // Usuário logado 
        ?>
            <div class="perfil-menu">
                <img onclick="mostrarDropdown()" class="profile-icon" src="../img/profile.png" alt="Foto de perfil do usuario">
                <div class="dropdown">

                    <?php if ($_SESSION['user_role'] !== 'admin'): // Link do Admin apenas para admins 
                    ?><p class="pontuacao">Pontuação</p>
                        <a href="perfil.php">Perfil</a>
                    <?php endif; ?>
                    <?php if ($_SESSION['user_role'] === 'admin'): // Link do Admin apenas para admins 
                    ?><p class="pontuacao">Admin</p>
                        <a class="dropdown-item" href="admin.php">Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php" class="saida">Sair<img src="../img/iconeSaida.svg" alt="Icone de saida"></a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])): // Usuário não logado 
        ?>
            <div class="botoes">
                <a href="register.php" class="cadastro borda-gradiente botao-transicao">Cadastrar</a>
                <a href="login.php" class="login gradiente-login borda-gradiente botao-transicao">Login</a>
            </div>
        <?php endif; ?>

    </div>
</header>