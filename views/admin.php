<?php
require_once __DIR__ . '/config.php';
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}
$adminUserStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$adminUserStmt->execute([$_SESSION['user_id']]);
$adminUser = $adminUserStmt->fetch() ?: ['name' => 'Administrador', 'email' => ''];
$adminMetrics = $pdo->query("SELECT (SELECT COUNT(*) FROM devices) devices_total,
    (SELECT COUNT(*) FROM devices WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')) devices_month,
    (SELECT COUNT(*) FROM reservations WHERE status = 'pending') pending_total")->fetch();
$reservations = $pdo->query("SELECT r.*, d.device_type, d.brand, d.model FROM reservations r JOIN devices d ON d.id = r.device_id ORDER BY r.created_at DESC")->fetchAll();
$donations = $pdo->query("SELECT dn.*, d.device_type, d.brand, d.model, d.photo FROM donations dn JOIN devices d ON d.id = dn.device_id ORDER BY dn.created_at DESC")->fetchAll();
function adminStatusLabel($status)
{
    return ['pending' => 'Pendente', 'scheduled' => 'Agendada', 'completed' => 'Concluída', 'cancelled' => 'Cancelada', 'approved' => 'Aprovada', 'rejected' => 'Recusada'][$status] ?? ucfirst($status);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo | ReciclaTech</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="admin-page">
    <div class="admin-overlay" data-fechar-menu></div>

    <aside class="admin-sidebar" id="admin-sidebar" aria-label="Navegação administrativa">
        <div class="admin-marca">
            <img src="../img/ReciclaTech 1.png" alt="ReciclaTech">
            <div><strong>ReciclaTech</strong><span>Administração</span></div>
            <button class="admin-icone-botao admin-sidebar-fechar" type="button" title="Fechar menu" aria-label="Fechar menu" data-fechar-menu>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="admin-nav">
            <span class="admin-nav-titulo">Principal</span>
            <a href="#visao-geral" class="ativo" aria-current="page"><i class="bi bi-grid-1x2-fill"></i><span>Visão geral</span></a>
            <a href="#reservas"><i class="bi bi-clock-history"></i><span>Reservas</span><b>0</b></a>
            <a href="#doacoes"><i class="bi bi-box-seam-fill"></i><span>Doações</span><b>1</b></a>

            <span class="admin-nav-titulo">Gestão</span>
            <a href="#"><i class="bi bi-phone-fill"></i><span>Dispositivos</span></a>
            <a href="#"><i class="bi bi-people-fill"></i><span>Usuários</span></a>
            <a href="ranking.php"><i class="bi bi-trophy-fill"></i><span>Ranking</span></a>
            <a href="#"><i class="bi bi-geo-alt-fill"></i><span>Ecopontos</span></a>
        </nav>

        <div class="admin-sidebar-rodape">
            <a href="#"><i class="bi bi-gear-fill"></i><span>Configurações</span></a>
            <div class="admin-usuario">
                <img src="../img/profile.png" alt="Foto do administrador">
                <div><strong><?= e($adminUser['name']) ?></strong><span><?= e($adminUser['email']) ?></span></div>
                <a href="logout.php" class="admin-icone-botao" title="Sair do painel" aria-label="Sair do painel"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </aside>

    <div class="admin-shell">
        <header class="admin-topbar">
            <div class="admin-topbar-esquerda">
                <button class="admin-icone-botao admin-menu-toggle" type="button" title="Abrir menu" aria-label="Abrir menu" aria-controls="admin-sidebar" aria-expanded="false">
                    <i class="bi bi-list"></i>
                </button>
                <div class="admin-breadcrumb"><span>Painel administrativo</span><i class="bi bi-chevron-right"></i><strong>Visão geral</strong></div>
            </div>
            <div class="admin-topbar-acoes">
                <span class="admin-data"><i class="bi bi-calendar3"></i> 21 de junho de 2026</span>
                <button class="admin-icone-botao" type="button" title="Notificações" aria-label="Notificações">
                    <i class="bi bi-bell"></i><span class="admin-notificacao"></span>
                </button>
                <a href="index.php" class="admin-ver-site"><i class="bi bi-box-arrow-up-right"></i><span>Ver site</span></a>
            </div>
        </header>

        <main class="admin-conteudo">
            <section id="visao-geral" class="admin-intro">
                <div>
                    <span class="admin-eyebrow">Dashboard administrativo</span>
                    <h1>Visão geral</h1>
                    <p>Acompanhe os dispositivos cadastrados e as solicitações da plataforma.</p>
                </div>
                <button type="button" class="admin-atualizar" id="admin-atualizar"><i class="bi bi-arrow-clockwise"></i> Atualizar dados</button>
            </section>

            <section class="admin-metricas" aria-label="Indicadores gerais">
                <article class="admin-metrica">
                    <div class="admin-metrica-icone metrica-aparelhos"><i class="bi bi-cpu-fill"></i></div>
                    <div><span>Total de aparelhos cadastrados</span><strong>1</strong><small><i class="bi bi-arrow-up"></i> 1 novo neste mês</small></div>
                    <a href="#doacoes" title="Ver aparelhos" aria-label="Ver aparelhos cadastrados"><i class="bi bi-arrow-up-right"></i></a>
                </article>
                <article class="admin-metrica">
                    <div class="admin-metrica-icone metrica-reservas"><i class="bi bi-hourglass-split"></i></div>
                    <div><span>Reservas pendentes de ação</span><strong>0</strong><small>Nenhuma ação necessária</small></div>
                    <a href="#reservas" title="Ver reservas" aria-label="Ver reservas pendentes"><i class="bi bi-arrow-up-right"></i></a>
                </article>
            </section>

            <section id="reservas" class="admin-secao">
                <div class="admin-secao-cabecalho">
                    <div><span class="admin-eyebrow">Solicitações</span>
                        <h2>Gestão de novas reservas</h2>
                        <p>Pedidos que aguardam aprovação ou contato.</p>
                    </div>
                    <label class="admin-select-wrap">
                        <span class="sr-only">Filtrar reservas</span>
                        <select aria-label="Filtrar reservas por status">
                            <option>Todas as reservas</option>
                            <option>Pendentes</option>
                            <option>Aprovadas</option>
                            <option>Recusadas</option>
                        </select>
                        <i class="bi bi-chevron-down"></i>
                    </label>
                </div>

                <div class="admin-tabela-wrap">
                    <table class="admin-tabela">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dispositivo</th>
                                <th>Adotante</th>
                                <th>Finalidade</th>
                                <th>Status</th>
                                <th class="coluna-acoes">Ações</th>
                            </tr>
                        </thead>
                        <tbody> <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td data-label="#"><?= (int) $reservation['id'] ?></td>
                                    <td data-label="Dispositivo"><?= e(trim($reservation['device_type'] . ' ' . $reservation['brand'] . ' ' . $reservation['model'])) ?></td>
                                    <td data-label="Adotante"><?= e($reservation['adopter_name']) ?><br><small><?= e($reservation['adopter_email']) ?></small></td>
                                    <td data-label="Finalidade"><?= e($reservation['purpose'] ?: 'Não informada') ?></td>
                                    <td data-label="Status"><span class="admin-status <?= e($reservation['status']) ?>"><i></i> <?= e(adminStatusLabel($reservation['status'])) ?></span></td>
                                    <td data-label="Ações" class="coluna-acoes">
                                        <?php if ($reservation['status'] === 'pending'): ?>
                                            <form action="process_reservation.php" method="post" style="display:inline"><input type="hidden" name="reservation_id" value="<?= (int) $reservation['id'] ?>"><button class="admin-icone-botao" name="action" value="approve" title="Aprovar"><i class="bi bi-check-lg"></i></button><button class="admin-icone-botao" name="action" value="reject" title="Recusar"><i class="bi bi-x-lg"></i></button></form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                                if (!$reservations): ?>
                                <tr class="admin-tabela-vazia">
                                    <td colspan="6">
                                        <div class="admin-vazio-icone"><i class="bi bi-inbox"></i></div>
                                        <strong>Nenhuma reserva recente</strong>
                                        <span>Novas solicitações aparecerão aqui.</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="doacoes" class="admin-secao">
                <div class="admin-secao-cabecalho">
                    <div><span class="admin-eyebrow">Registros</span>
                        <h2>Histórico de doações</h2>
                        <p>Coletas e entregas de dispositivos cadastrados.</p>
                    </div>
                    <div class="admin-filtros">
                        <label class="admin-busca">
                            <span class="sr-only">Buscar dispositivo</span>
                            <i class="bi bi-search"></i>
                            <input id="admin-busca-doacoes" type="search" placeholder="Buscar dispositivo">
                        </label>
                        <button class="admin-icone-botao admin-exportar" id="admin-exportar" type="button" title="Exportar histórico" aria-label="Exportar histórico em CSV"><i class="bi bi-download"></i></button>
                    </div>
                </div>

                <div class="admin-tabela-wrap">
                    <table class="admin-tabela" id="admin-tabela-doacoes">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dispositivo</th>
                                <th>Doador</th>
                                <th>Doado em</th>
                                <th>Status</th>
                                <th class="coluna-acoes">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $donation): $deviceName = trim($donation['device_type'] . ' ' . $donation['brand'] . ' ' . $donation['model']); ?>
                                <tr data-dispositivo="<?= e($deviceName) ?>">
                                    <td data-label="#"><?= (int) $donation['id'] ?></td>
                                    <td data-label="Dispositivo">
                                        <div class="admin-dispositivo"><img src="<?= e($donation['photo'] ?: '../img/equipamentosQuebrados.webp') ?>" alt="">
                                            <div><strong><?= e($deviceName) ?></strong><span><?= e($donation['device_type']) ?></span></div>
                                        </div>
                                    </td>
                                    <td data-label="Doador"><?= e($donation['donor_name'] ?: 'Não informado') ?></td>
                                    <td data-label="Doado em"><time datetime="<?= e($donation['created_at']) ?>"><?= e(date('d/m/Y, H:i', strtotime($donation['created_at']))) ?></time></td>
                                    <td data-label="Status"><span class="admin-status <?= e($donation['status']) ?>"><i></i> <?= e(adminStatusLabel($donation['status'])) ?></span></td>
                                    <td data-label="Ações" class="coluna-acoes">
                                        <a class="admin-icone-botao" href="edit_device.php?id=<?= (int) $donation['device_id'] ?>" title="Visualizar doação"><i class="bi bi-eye"></i></a>
                                        <button class="admin-icone-botao" type="button" title="Mais opções" aria-label="Mais opções para doação 12"><i class="bi bi-three-dots-vertical"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div id="admin-sem-resultados" class="admin-sem-resultados" hidden>Nenhum dispositivo encontrado.</div>
                </div>
                <div class="admin-tabela-rodape"><span>Mostrando <?= count($donations) ?> de <?= count($donations) ?> registro(s)</span>
                    <div><button type="button" disabled aria-label="Página anterior"><i class="bi bi-chevron-left"></i></button><b>1</b><button type="button" disabled aria-label="Próxima página"><i class="bi bi-chevron-right"></i></button></div>
                </div>
            </section>
        </main>
    </div>

    <div class="admin-toast" id="admin-toast" role="status" aria-live="polite"><i class="bi bi-check-circle-fill"></i><span>Dados atualizados.</span></div>
    <script src="js/admin.js"></script>
</body>

</html>