<?php
require_once __DIR__ . '/config.php';

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

$types = [];
foreach ($devices as $device) {
    if ($device['device_type'] !== '') $types[$device['device_type']] = $device['device_type'];
}
natcasesort($types);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispositivos disponíveis | ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/adote.css">
    <link rel="stylesheet" href="css/donation-list.css">
</head>

<body class="adote-page donation-list-page">
    <section class="adote-topo donation-list-topo">
        <?php require 'templates/header.php'; ?>
        <div class="adote-hero">
            <div class="adote-hero-texto">
                <span class="adote-etiqueta"><i class="bi bi-grid"></i> Catálogo ReciclaTech</span>
                <h2>Doações disponíveis</h2>
                <p>Explore todos os equipamentos que estão esperando uma nova oportunidade de uso.</p>
            </div>
            <aside class="adote-resumo">
                <div><i class="bi bi-box-seam"></i></div>
                <span>Catálogo atualizado<strong><?= count($devices) ?> <?= count($devices) === 1 ? 'dispositivo' : 'dispositivos' ?></strong><small>Disponíveis para adoção</small></span>
            </aside>
        </div>
    </section>

    <main class="adote-conteudo donation-list-conteudo">
        <div class="adote-cabecalho-lista donation-list-cabecalho">
            <div><span class="adote-sobretitulo">Escolha consciente</span>
                <h1>Encontre seu próximo aparelho</h1>
                <p>Pesquise e filtre os dispositivos cadastrados pela comunidade.</p>
            </div>
        </div>

        <?php if ($devices): ?>
            <section class="donation-list-filtros" aria-label="Filtros do catálogo">
                <label class="adote-busca donation-list-busca"><i class="bi bi-search"></i><span class="visually-hidden">Buscar dispositivo</span><input id="busca-dispositivo" type="search" placeholder="Buscar por nome, marca ou modelo"></label>
                <label><span class="visually-hidden">Filtrar por tipo</span><select id="filtro-tipo">
                        <option value="">Todos os tipos</option><?php foreach ($types as $type): ?><option value="<?= e(mb_strtolower($type)) ?>"><?= e($type) ?></option><?php endforeach; ?>
                    </select></label>
                <label><span class="visually-hidden">Filtrar por condição</span><select id="filtro-condicao">
                        <option value="">Todas as condições</option><?php foreach ($conditionLabels as $value => $label): ?><option value="<?= e($value) ?>"><?= e($label) ?></option><?php endforeach; ?>
                    </select></label>
                <button id="limpar-filtros" type="button"><i class="bi bi-arrow-counterclockwise"></i> Limpar</button>
            </section>

            <div class="donation-list-resultado"><span id="contagem-dispositivos"><?= count($devices) ?> dispositivo(s) encontrado(s)</span><span><i class="bi bi-arrow-down-up"></i> Mais recentes primeiro</span></div>

            <div class="adote-grid" id="lista-dispositivos">
                <?php foreach ($devices as $device):
                    $deviceName = trim($device['device_type'] . ' ' . $device['brand'] . ' ' . $device['model']);
                    $location = trim(($device['address_city'] ?: 'Local não informado') . ($device['address_state'] ? ', ' . $device['address_state'] : ''));
                    $condition = $conditionLabels[$device['device_condition']] ?? ucfirst(str_replace('_', ' ', $device['device_condition']));
                ?>
                    <article class="adote-card" data-texto="<?= e(mb_strtolower($deviceName . ' ' . $device['description'] . ' ' . $location)) ?>" data-tipo="<?= e(mb_strtolower($device['device_type'])) ?>" data-condicao="<?= e($device['device_condition']) ?>">
                        <div class="adote-card-imagem">
                            <img src="<?= e($device['photo'] ?: '../img/equipamentosQuebrados.webp') ?>" alt="Foto de <?= e($deviceName) ?>">
                            <span class="adote-condicao"><i></i><?= e($condition) ?></span>
                        </div>
                        <div class="adote-card-corpo">
                            <span class="adote-tipo"><?= e($device['device_type']) ?></span>
                            <h2><?= e($deviceName) ?></h2>
                            <p><?= e($device['description'] ?: 'O doador não adicionou uma descrição para este aparelho.') ?></p>
                            <div class="adote-card-info"><span><i class="bi bi-geo-alt"></i><?= e($location) ?></span><span><i class="bi bi-calendar3"></i><?= e(date('d/m/Y', strtotime($device['created_at']))) ?></span></div>
                        </div>
                        <div class="adote-card-rodape"><a href="adote.php?device=<?= (int) $device['id'] ?>">Ver e adotar <i class="bi bi-arrow-right"></i></a></div>
                    </article>
                <?php endforeach; ?>
            </div>

            <section class="adote-sem-busca" id="sem-resultados" hidden><i class="bi bi-search"></i>
                <h2>Nenhum dispositivo encontrado</h2>
                <p>Altere os filtros ou pesquise por outro termo.</p><button type="button" data-limpar-filtros>Limpar filtros</button>
            </section>
        <?php else: ?>
            <section class="adote-vazio">
                <div><i class="bi bi-box-seam"></i></div>
                <h2>Nenhum dispositivo disponível</h2>
                <p>Assim que novas doações forem cadastradas, elas aparecerão neste catálogo.</p><a href="index.php"><i class="bi bi-arrow-left"></i> Voltar ao início</a>
            </section>
        <?php endif; ?>
    </main>

    <?php require 'templates/footer.php'; ?>
    <script src="js/infoFooter.js"></script>
    <script src="js/dropdown.js"></script>
    <script>
        const busca = document.getElementById('busca-dispositivo');
        const tipo = document.getElementById('filtro-tipo');
        const condicao = document.getElementById('filtro-condicao');
        const cards = [...document.querySelectorAll('#lista-dispositivos .adote-card')];
        const contagem = document.getElementById('contagem-dispositivos');
        const vazio = document.getElementById('sem-resultados');
        const normalizar = texto => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

        function filtrar() {
            const termo = normalizar(busca?.value.trim() || '');
            let total = 0;
            cards.forEach(card => {
                const exibir = normalizar(card.dataset.texto || '').includes(termo) &&
                    (!tipo?.value || card.dataset.tipo === tipo.value) &&
                    (!condicao?.value || card.dataset.condicao === condicao.value);
                card.hidden = !exibir;
                if (exibir) total++;
            });
            if (contagem) contagem.textContent = `${total} ${total === 1 ? 'dispositivo encontrado' : 'dispositivos encontrados'}`;
            if (vazio) vazio.hidden = total !== 0;
        }

        function limpar() {
            if (busca) busca.value = '';
            if (tipo) tipo.value = '';
            if (condicao) condicao.value = '';
            filtrar();
        }
        busca?.addEventListener('input', filtrar);
        tipo?.addEventListener('change', filtrar);
        condicao?.addEventListener('change', filtrar);
        document.getElementById('limpar-filtros')?.addEventListener('click', limpar);
        document.querySelector('[data-limpar-filtros]')?.addEventListener('click', limpar);
    </script>
</body>

</html>