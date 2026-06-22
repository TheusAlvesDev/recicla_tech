<?php if (!isset($pdo)) require_once __DIR__ . '/config.php';
$rankingStmt = $pdo->query("SELECT u.id, u.name, u.address_city, u.address_state, u.points,
    COUNT(d.id) AS donations_total,
    SUM(CASE WHEN d.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS donations_week,
    SUM(CASE WHEN d.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 ELSE 0 END) AS donations_month
    FROM users u LEFT JOIN devices d ON d.user_id = u.id
    WHERE u.role = 'user' GROUP BY u.id
    ORDER BY u.points DESC, donations_total DESC, u.name ASC");
$rankingUsers = $rankingStmt->fetchAll();
$donationPoints = (int) ($pdo->query("SELECT points_value FROM points_config WHERE action_key = 'doacao_completa'")->fetchColumn() ?: 50);
$rankingData = array_map(static function ($row) use ($donationPoints) {
    $total = (int) $row['donations_total'];
    return [
        'id' => (int) $row['id'],
        'nome' => $row['name'],
        'cidade' => trim(($row['address_city'] ?: 'Cidade não informada') . ($row['address_state'] ? ', ' . $row['address_state'] : '')),
        'doacoes' => $total,
        'impacto' => $total . ($total === 1 ? ' item reaproveitado' : ' itens reaproveitados'),
        'mes' => (int) $row['donations_month'] * $donationPoints,
        'semana' => (int) $row['donations_week'] * $donationPoints,
        'geral' => (int) $row['points'],
        'atual' => isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $row['id']
    ];
}, $rankingUsers);
$topThree = array_slice($rankingData, 0, 3);
$currentPosition = null;
foreach ($rankingData as $index => $rankedUser) if ($rankedUser['atual']) {
    $currentPosition = $index + 1;
    break;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking de Doadores | ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/ranking.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="ranking-page">
    <section class="ranking-topo">

        <?php require 'templates/header.php'; ?>

        <div class="ranking-hero">
            <div class="ranking-hero-texto">
                <span class="ranking-etiqueta"><i class="bi bi-trophy-fill"></i> Comunidade em ação</span>
                <h2>Ranking de doadores</h2>
                <p>Cada equipamento doado gera pontos e fortalece uma rede mais sustentável. Acompanhe sua posição e continue fazendo a diferença.</p>
            </div>
            <aside class="minha-posicao" aria-label="Sua posição no ranking">
                <div class="minha-posicao-icone"><i class="bi bi-person-fill"></i></div>
                <div>
                    <span>Sua posição</span>
                    <strong><?= $currentPosition ? e($currentPosition . 'º lugar') : 'Entre para participar' ?></strong>
                    <small><?= $currentPosition ? e(number_format($rankingData[$currentPosition - 1]['geral'], 0, ',', '.') . ' pontos') : 'Ranking atualizado' ?></small>
                </div>
                <i class="bi bi-arrow-up-right"></i>
            </aside>
        </div>
    </section>

    <main class="ranking-conteudo">
        <section class="podio-secao" aria-labelledby="titulo-podio">
            <div class="secao-cabecalho">
                <div>
                    <span class="secao-sobretitulo">Destaques do mês</span>
                    <h2 id="titulo-podio">Maiores doadores</h2>
                </div>
                <img src="../asset/img/trophy.png" alt="" aria-hidden="true">
            </div>

            <div class="podio">
                <?php foreach ([1, 0, 2] as $podiumIndex): if (!isset($topThree[$podiumIndex])) continue;
                    $person = $topThree[$podiumIndex]; ?>
                    <article class="podio-card <?= ['primeiro-lugar', 'segundo-lugar', 'terceiro-lugar'][$podiumIndex] ?>">
                        <?php if ($podiumIndex === 0): ?><span class="podio-coroa"><i class="bi bi-trophy-fill"></i></span><?php endif; ?>
                        <span class="podio-posicao"><?= $podiumIndex + 1 ?></span>
                        <img class="podio-avatar" src="../img/User.png" alt="">
                        <h3><?= e($person['nome']) ?></h3>
                        <p><?= $person['doacoes'] ?> doaç<?= $person['doacoes'] === 1 ? 'ão' : 'ões' ?></p>
                        <strong><?= e(number_format($person['geral'], 0, ',', '.')) ?> pts</strong>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="classificacao-secao" aria-labelledby="titulo-classificacao">
            <div class="classificacao-topo">
                <div>
                    <span class="secao-sobretitulo">Classificação geral</span>
                    <h2 id="titulo-classificacao">Doadores da comunidade</h2>
                </div>
                <div class="ranking-filtros">
                    <label class="ranking-busca">
                        <span class="visually-hidden">Buscar doador</span>
                        <i class="bi bi-search"></i>
                        <input id="busca-ranking" type="search" placeholder="Buscar doador">
                    </label>
                    <label>
                        <span class="visually-hidden">Selecionar período</span>
                        <select id="periodo-ranking" aria-label="Selecionar período">
                            <option value="mes">Este mês</option>
                            <option value="semana">Esta semana</option>
                            <option value="geral">Todo o período</option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="ranking-tabela" role="table" aria-label="Classificação dos doadores">
                <div class="ranking-tabela-cabecalho" role="row">
                    <span role="columnheader">Posição</span>
                    <span role="columnheader">Doador(a)</span>
                    <span role="columnheader">Doações</span>
                    <span role="columnheader">Impacto</span>
                    <span role="columnheader">Pontuação</span>
                </div>
                <div id="ranking-lista" class="ranking-lista"></div>
            </div>
            <p id="ranking-contagem" class="ranking-contagem" aria-live="polite"></p>
        </section>
    </main>

    <?php require 'templates/footer.php'; ?>

    <script src="js/infoFooter.js"></script>
    <script src="js/dropdown.js"></script>
    <script>
        window.rankingData = <?= json_encode($rankingData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="js/ranking.js"></script>
</body>

</html>