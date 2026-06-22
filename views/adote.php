<?php require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$userStmt = $pdo->prepare('SELECT name, email FROM users WHERE id = ?');
$userStmt->execute([$_SESSION['user_id']]);
$currentUser = $userStmt->fetch() ?: ['name' => '', 'email' => ''];

$stmt = $pdo->query("SELECT d.id, d.device_type, d.brand, d.model, d.description,
    d.device_condition, d.photo, d.created_at, u.address_city, u.address_state
    FROM devices d
    LEFT JOIN users u ON u.id = d.user_id
    WHERE d.status = 'available'
    ORDER BY d.created_at DESC");
$devices = $stmt->fetchAll();

$conditionLabels = [
    'novo' => 'Novo',
    'bom' => 'Bom estado',
    'funcional' => 'Funcional',
    'com_defeito' => 'Com defeito',
    'para_pecas' => 'Para peças',
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adote um aparelho | ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/adote.css">
</head>

<body class="adote-page">
    <section class="adote-topo">
        <?php require 'templates/header.php'; ?>
        <div class="adote-hero">
            <div class="adote-hero-texto">
                <span class="adote-etiqueta"><i class="bi bi-recycle"></i> Reutilize e transforme</span>
                <h2>Adote um aparelho</h2>
                <p>Encontre equipamentos disponíveis, dê uma nova vida à tecnologia e ajude a reduzir o descarte eletrônico.</p>
            </div>
            <aside class="adote-resumo">
                <div><i class="bi bi-phone"></i></div>
                <span>Disponíveis agora<strong><?= count($devices) ?> <?= count($devices) === 1 ? 'aparelho' : 'aparelhos' ?></strong><small>Prontos para uma nova história</small></span>
            </aside>
        </div>
    </section>

    <main class="adote-conteudo">
        <?php if (($_GET['msg'] ?? '') === 'reserved'): ?>
            <div class="adote-mensagem sucesso" role="status"><i class="bi bi-check-circle-fill"></i>
                <div><strong>Reserva enviada!</strong><span>Sua solicitação será analisada pela equipe.</span></div>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="adote-mensagem erro" role="alert"><i class="bi bi-exclamation-circle-fill"></i>
                <div><strong>Não foi possível reservar.</strong><span>Confira os dados ou tente novamente em instantes.</span></div>
            </div>
        <?php endif; ?>

        <div class="adote-cabecalho-lista">
            <div><span class="adote-sobretitulo">Tecnologia circular</span>
                <h1>Dispositivos disponíveis</h1>
                <p>Escolha um item para conhecer os detalhes e enviar sua solicitação.</p>
            </div>
            <?php if ($devices): ?><label class="adote-busca"><i class="bi bi-search"></i><span class="visually-hidden">Buscar aparelho</span><input id="busca-aparelho" type="search" placeholder="Buscar aparelho"></label><?php endif; ?>
        </div>

        <?php if ($devices): ?>
            <div class="adote-grid" id="lista-aparelhos">
                <?php foreach ($devices as $device):
                    $deviceName = trim($device['device_type'] . ' ' . $device['brand'] . ' ' . $device['model']);
                    $location = trim(($device['address_city'] ?: 'Local não informado') . ($device['address_state'] ? ', ' . $device['address_state'] : ''));
                    $condition = $conditionLabels[$device['device_condition']] ?? ucfirst(str_replace('_', ' ', $device['device_condition']));
                ?>
                    <article class="adote-card" data-aparelho="<?= e(mb_strtolower($deviceName)) ?>">
                        <div class="adote-card-imagem">
                            <img src="<?= e($device['photo'] ?: '../img/equipamentosQuebrados.webp') ?>" alt="Foto de <?= e($deviceName) ?>">
                            <span class="adote-condicao"><i></i><?= e($condition) ?></span>
                        </div>
                        <div class="adote-card-corpo">
                            <span class="adote-tipo"><?= e($device['device_type']) ?></span>
                            <h2><?= e($deviceName) ?></h2>
                            <p><?= e($device['description'] ?: 'O doador não adicionou uma descrição para este aparelho.') ?></p>
                            <div class="adote-card-info">
                                <span><i class="bi bi-geo-alt"></i><?= e($location) ?></span>
                                <span><i class="bi bi-calendar3"></i><?= e(date('d/m/Y', strtotime($device['created_at']))) ?></span>
                            </div>
                        </div>
                        <div class="adote-card-rodape">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#reserva-<?= (int) $device['id'] ?>">Quero adotar <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </article>

                    <div class="modal fade adote-modal" id="reserva-<?= (int) $device['id'] ?>" tabindex="-1" aria-labelledby="titulo-reserva-<?= (int) $device['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="submit_reservation.php" method="post">
                                    <div class="modal-header">
                                        <div><span>Solicitação de adoção</span>
                                            <h2 class="modal-title" id="titulo-reserva-<?= (int) $device['id'] ?>"><?= e($deviceName) ?></h2>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="device_id" value="<?= (int) $device['id'] ?>">
                                        <div class="adote-modal-resumo"><img src="<?= e($device['photo'] ?: '../img/equipamentosQuebrados.webp') ?>" alt="">
                                            <div><strong><?= e($condition) ?></strong><span><i class="bi bi-geo-alt"></i><?= e($location) ?></span></div>
                                        </div>
                                        <p class="adote-modal-intro">Confirme seus dados e conte brevemente como pretende utilizar o aparelho.</p>
                                        <div class="adote-modal-campo"><label for="nome-<?= (int) $device['id'] ?>">Seu nome</label><input id="nome-<?= (int) $device['id'] ?>" type="text" name="adopter_name" value="<?= e($currentUser['name']) ?>" maxlength="150" required></div>
                                        <div class="adote-modal-campo"><label for="email-<?= (int) $device['id'] ?>">Seu e-mail</label><input id="email-<?= (int) $device['id'] ?>" type="email" name="adopter_email" value="<?= e($currentUser['email']) ?>" maxlength="150" required></div>
                                        <div class="adote-modal-campo"><label for="finalidade-<?= (int) $device['id'] ?>">Finalidade</label><textarea id="finalidade-<?= (int) $device['id'] ?>" name="purpose" rows="4" maxlength="1000" placeholder="Ex.: estudos, trabalho, projeto social..."></textarea></div>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="adote-modal-cancelar" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="adote-modal-confirmar"><i class="bi bi-check-lg"></i> Confirmar reserva</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="adote-sem-busca" id="sem-resultados" hidden><i class="bi bi-search"></i>
                <h2>Nenhum aparelho encontrado</h2>
                <p>Tente buscar por outro nome, tipo, marca ou modelo.</p>
            </div>
        <?php else: ?>
            <section class="adote-vazio">
                <div><i class="bi bi-box-seam"></i></div>
                <h2>Nenhum aparelho disponível agora</h2>
                <p>Novas doações aparecem por aqui assim que são cadastradas. Volte em breve para encontrar novas oportunidades.</p><a href="index.php"><i class="bi bi-arrow-left"></i> Voltar ao início</a>
            </section>
        <?php endif; ?>
    </main>

    <?php require 'templates/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/infoFooter.js"></script>
    <script src="js/dropdown.js"></script>
    <script>
        const busca = document.getElementById('busca-aparelho');
        const cards = [...document.querySelectorAll('.adote-card')];
        const semResultados = document.getElementById('sem-resultados');
        busca?.addEventListener('input', () => {
            const termo = busca.value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
            let visiveis = 0;
            cards.forEach(card => {
                const conteudo = card.textContent.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                card.hidden = !conteudo.includes(termo);
                if (!card.hidden) visiveis++;
            });
            if (semResultados) semResultados.hidden = visiveis !== 0;
        });
    </script>
</body>

</html>