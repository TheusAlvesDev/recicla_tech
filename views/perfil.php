<?php if (!isset($pdo)) require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. BUSCAR DADOS DO USUÁRIO
try {
    $stmt = $pdo->prepare("
        SELECT 
            name, 
            email, 
            address_street, 
            address_number, 
            address_complement, 
            address_city, 
            address_state, 
            address_zipcode,
            points,
            created_at
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Se o usuário foi deletado do DB, destrói a sessão
        session_destroy();
        header("Location: login.php?error=user_not_found");
        exit;
    }

    // 3. BUSCAR HISTÓRICO DE DOAÇÕES (Itens que o usuário doou)
    $donated_stmt = $pdo->prepare("SELECT * FROM devices WHERE user_id = ? ORDER BY created_at DESC");
    $donated_stmt->execute([$user_id]);
    $donated_items = $donated_stmt->fetchAll(PDO::FETCH_ASSOC);

    $incoming_stmt = $pdo->prepare("SELECT r.*
        FROM reservations r
        JOIN devices d ON d.id = r.device_id
        WHERE d.user_id = ?
        ORDER BY (r.status = 'pending') DESC, r.created_at DESC");
    $incoming_stmt->execute([$user_id]);
    $incoming_requests = [];
    foreach ($incoming_stmt->fetchAll(PDO::FETCH_ASSOC) as $request) {
        $incoming_requests[(int) $request['device_id']][] = $request;
    }

    // 4. BUSCAR HISTÓRICO DE ADOÇÕES (Itens que o usuário reservou/adotou)
    $adopted_stmt = $pdo->prepare("
        SELECT r.*, d.description AS device_name, d.status AS device_status 
        FROM reservations r
        JOIN devices d ON r.device_id = d.id
        WHERE r.adopter_email = ?
        ORDER BY r.created_at DESC
    ");
    $adopted_stmt->execute([$user['email']]);
    $adopted_items = $adopted_stmt->fetchAll(PDO::FETCH_ASSOC);
    $ranking_stmt = $pdo->prepare("SELECT COUNT(*) + 1 FROM users WHERE role = 'user' AND points > ?");
    $ranking_stmt->execute([$user['points']]);
    $ranking_position = (int) $ranking_stmt->fetchColumn();
} catch (PDOException $e) {
    // Tratar erro de banco de dados
    die("Erro ao carregar dados do usuário: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="perfil-page">
    <section class="perfil-topo">

        <?php require 'templates/header.php'; ?>

        <div class="perfil-identidade">
            <div class="perfil-avatar-wrap">
                <img src="../img/User.png" alt="Foto de <?= e($user['name']) ?>" class="perfil-avatar">
                <button type="button" class="perfil-avatar-editar" title="Alterar foto" aria-label="Alterar foto do perfil">
                    <i class="bi bi-camera-fill"></i>
                </button>
            </div>
            <div class="perfil-identidade-texto">
                <span class="perfil-eyebrow">Meu perfil</span>
                <h2><?= htmlspecialchars($user['name']) ?></h2>
                <p><i class="bi bi-geo-alt"></i> <?= e(trim(($user['address_city'] ?: 'Cidade não informada') . ($user['address_state'] ? ', ' . $user['address_state'] : ''))) ?> <span></span> Membro desde junho de 2026</p>
            </div>
            <div class="perfil-pontos">
                <i class="bi bi-trophy-fill"></i>
                <div><span>Pontuação</span><strong><?= e(number_format($user['points'], 0, ',', '.')) ?></strong><small><?= $ranking_position ?>º no ranking</small></div>
            </div>
        </div>
    </section>

    <main class="perfil-conteudo">
        <?php if (isset($_GET['reservation_result'])): ?>
            <div class="perfil-alerta-reserva sucesso" role="status">
                <i class="bi bi-check-circle-fill"></i>
                <?= $_GET['reservation_result'] === 'approved' ? 'Solicitação aceita. O dispositivo ficou reservado para o adotante.' : 'Solicitação recusada. O dispositivo voltou a ficar disponível.' ?>
            </div>
        <?php elseif (isset($_GET['reservation_error'])): ?>
            <div class="perfil-alerta-reserva erro" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> Não foi possível processar essa solicitação. Ela pode já ter sido respondida ou não pertencer a você.
            </div>
        <?php endif; ?>
        <div class="perfil-tabs-wrap">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    Dados atualizados com sucesso!
                </div>
            <?php endif; ?>
            <div class="perfil-tabs" role="tablist" aria-label="Seções do perfil">
                <button class="perfil-tab ativo" id="tab-visao" type="button" role="tab" aria-selected="true" aria-controls="painel-visao" data-aba="visao">
                    <i class="bi bi-grid"></i><span>Visão geral</span>
                </button>
                <button class="perfil-tab" id="tab-doacoes" type="button" role="tab" aria-selected="false" aria-controls="painel-doacoes" data-aba="doacoes">
                    <i class="bi bi-box-seam"></i><span>Minhas doações</span><b><?= count($donated_items) ?></b>
                </button>
                <button class="perfil-tab" id="tab-adocoes" type="button" role="tab" aria-selected="false" aria-controls="painel-adocoes" data-aba="adocoes">
                    <i class="bi bi-handbag"></i><span>Minhas adoções</span><b><?= count($adopted_items) ?></b>
                </button>
                <button class="perfil-tab" id="tab-editar" type="button" role="tab" aria-selected="false" aria-controls="painel-editar" data-aba="editar">
                    <i class="bi bi-pencil-square"></i><span>Editar dados</span>
                </button>
            </div>
        </div>

        <section class="perfil-painel ativo" id="painel-visao" role="tabpanel" aria-labelledby="tab-visao" data-painel="visao">
            <div class="perfil-secao-titulo">
                <div><span>Resumo da conta</span>
                    <h2>Estatísticas rápidas</h2>
                </div>
                <button type="button" class="perfil-link-botao" data-abrir-aba="editar"><i class="bi bi-pencil"></i> Editar perfil</button>
            </div>

            <div class="perfil-estatisticas">
                <article class="perfil-stat stat-doacoes">
                    <div class="perfil-stat-icone"><i class="bi bi-box-seam"></i></div>
                    <div><span>Itens doados</span><strong>1</strong><small><?= count($donated_items) ?></small></div>
                </article>
                <article class="perfil-stat stat-adocoes">
                    <div class="perfil-stat-icone"><i class="bi bi-handbag"></i></div>
                    <div><span>Itens adotados</span><strong><?= count($adopted_items) ?></strong><small><?= count(array_filter($adopted_items, function ($item) {
                                                                                                            return $item['status'] === 'approved';
                                                                                                        })) ?></small></div>
                </article>
                <article class="perfil-stat stat-reservas">
                    <div class="perfil-stat-icone"><i class="bi bi-clock-history"></i></div>
                    <div><span>Reservas pendentes</span><strong><?= count(array_filter($adopted_items, fn($item) => in_array($item['status'], ['approved', 'completed'], true))) ?></strong><small><?= count(array_filter($adopted_items, function ($item) {
                                                                                                                                                                                                        return $item['status'] === 'pending';
                                                                                                                                                                                                    })) ?></small></div>
                </article>
            </div>

            <div class="perfil-resumo-grid">
                <section class="perfil-bloco">
                    <div class="perfil-bloco-cabecalho">
                        <div><span>Atividade recente</span>
                            <h3>Sua última doação</h3>
                        </div>
                        <button type="button" class="perfil-link-botao" data-abrir-aba="doacoes">Ver todas <i class="bi bi-arrow-right"></i></button>
                    </div>
                    <?php if ($donated_items): $lastDonation = $donated_items[0]; ?>
                        <article class="doacao-resumo">
                            <img src="<?= e($lastDonation['photo'] ?: '../img/equipamentosQuebrados.webp') ?>" alt="">
                            <div class="doacao-resumo-info">
                                <span class="status-disponivel"><?= e($lastDonation['status']) ?></span>
                                <h4><?= e(trim($lastDonation['device_type'] . ' ' . $lastDonation['brand'] . ' ' . $lastDonation['model'])) ?></h4>
                                <p><?= e($lastDonation['description'] ?: 'Sem descrição.') ?></p>
                                <small><i class="bi bi-calendar3"></i> Publicado em <?= e(date('d/m/Y', strtotime($lastDonation['created_at']))) ?></small>
                            </div>
                        </article>
                    <?php else: ?><div class="perfil-vazio">
                            <p>Você ainda não cadastrou doações.</p>
                        </div><?php endif; ?>
                </section>

                <aside class="perfil-impacto">
                    <span>Seu impacto</span>
                    <h3>Você já colocou em circulação</h3>
                    <strong><?= count($donated_items) ?> <?= count($donated_items) === 1 ? 'item' : 'itens' ?></strong>
                    <p>que podem ganhar uma nova vida útil.</p>
                    <div class="impacto-progresso"><span style="width: <?= min(100, count($donated_items) * 10) ?>%"></span></div>
                    <small>Continue doando para aumentar seu impacto.</small>
                </aside>
            </div>
        </section>

        <section class="perfil-painel" id="painel-doacoes" role="tabpanel" aria-labelledby="tab-doacoes" data-painel="doacoes" hidden>
            <div class="perfil-secao-titulo">
                <div><span>Seus anúncios</span>
                    <h2>Minhas doações</h2>
                </div>
                <a href="donate.php" class="perfil-acao-primaria"><i class="bi bi-plus-lg"></i> Nova doação</a>
            </div>
            <div class="doacoes-lista">
                <?php foreach ($donated_items as $item): ?>
                    <article class="doacao-item">
                        <img src="<?= e($item['photo'] ?: '../img/equipamentosQuebrados.webp') ?>" alt="">
                        <div class="doacao-item-info">
                            <div><span class="status-disponivel"><?= e($item['status']) ?></span><small>Publicado em <?= e(date('d/m/Y', strtotime($item['created_at']))) ?></small></div>
                            <h3><?= e(trim($item['device_type'] . ' ' . $item['brand'] . ' ' . $item['model'])) ?></h3>
                            <p><?= e($item['description'] ?: 'Sem descrição.') ?></p>
                            <span class="doacao-local"><i class="bi bi-geo-alt"></i> <?= e(trim(($user['address_city'] ?: 'Local não informado') . ($user['address_state'] ? ', ' . $user['address_state'] : ''))) ?></span>
                        </div>
                        <div class="doacao-acoes">
                            <?php if ($item['status'] === 'available'): ?><a href="edit_device.php?id=<?= (int) $item['id'] ?>" title="Editar doação" aria-label="Editar doação"><i class="bi bi-pencil"></i></a><?php endif; ?>
                            <button type="button" title="Mais opções" aria-label="Mais opções"><i class="bi bi-three-dots-vertical"></i></button>
                        </div>
                        <?php if (!empty($incoming_requests[(int) $item['id']])): ?>
                            <div class="solicitacoes-recebidas">
                                <h4><i class="bi bi-person-check"></i> Solicitações recebidas</h4>
                                <?php foreach ($incoming_requests[(int) $item['id']] as $request): ?>
                                    <section class="solicitacao-recebida">
                                        <div class="solicitacao-dados">
                                            <strong><?= e($request['adopter_name']) ?></strong>
                                            <a href="mailto:<?= e($request['adopter_email']) ?>"><?= e($request['adopter_email']) ?></a>
                                            <p><?= e($request['purpose'] ?: 'Finalidade não informada.') ?></p>
                                            <small>Solicitado em <?= e(date('d/m/Y, H:i', strtotime($request['created_at']))) ?></small>
                                        </div>
                                        <?php if ($request['status'] === 'pending'): ?>
                                            <form class="solicitacao-acoes" action="process_reservation.php" method="post">
                                                <input type="hidden" name="reservation_id" value="<?= (int) $request['id'] ?>">
                                                <button class="solicitacao-recusar" type="submit" name="action" value="reject"><i class="bi bi-x-lg"></i> Recusar</button>
                                                <button class="solicitacao-aceitar" type="submit" name="action" value="approve"><i class="bi bi-check-lg"></i> Aceitar solicitação</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="solicitacao-status <?= e($request['status']) ?>"><?= $request['status'] === 'approved' ? 'Aceita' : 'Recusada' ?></span>
                                        <?php endif; ?>
                                    </section>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="perfil-painel" id="painel-adocoes" role="tabpanel" aria-labelledby="tab-adocoes" data-painel="adocoes" hidden>
            <div class="perfil-secao-titulo">
                <div><span>Itens recebidos</span>
                    <h2>Minhas adoções</h2>
                </div>
            </div>
            <?php if (!$adopted_items): ?>
                <div class="perfil-vazio">
                    <div><i class="bi bi-handbag"></i></div>
                    <h3>Você ainda não adotou nenhum item</h3>
                    <p>Explore as doações disponíveis e encontre um equipamento que possa ganhar uma nova vida com você.</p>
                    <a href="index.php" class="perfil-acao-primaria">Explorar doações <i class="bi bi-arrow-right"></i></a>
                </div>
            <?php else: ?>
                <div class="doacoes-lista">
                    <?php foreach ($adopted_items as $item): ?>
                        <article class="doacao-item">
                            <div class="doacao-item-info">
                                <div><span class="status-disponivel"><?= e($item['status']) ?></span><small>Solicitado em <?= e(date('d/m/Y', strtotime($item['created_at']))) ?></small></div>
                                <h3><?= e($item['device_name'] ?: 'Dispositivo') ?></h3>
                                <p><?= e($item['purpose'] ?: 'Finalidade não informada.') ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="perfil-painel" id="painel-editar" role="tabpanel" aria-labelledby="tab-editar" data-painel="editar" hidden>
            <div class="perfil-secao-titulo">
                <div><span>Dados pessoais</span>
                    <h2>Editar perfil</h2>
                </div>
            </div>
            <form id="form-perfil" class="perfil-formulario">
                <div class="campo-perfil campo-largo">
                    <label for="perfil-nome">Nome completo</label>
                    <input id="perfil-nome" name="nome" type="text" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="campo-perfil">
                    <label for="perfil-email">E-mail</label>
                    <input id="perfil-email" name="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="campo-perfil">
                    <label for="perfil-telefone">Telefone</label>
                    <input id="perfil-telefone" name="telefone" type="tel" value="(88) 99999-9999">
                </div>
                <div class="campo-perfil">
                    <label for="perfil-cidade">Cidade</label>
                    <input id="perfil-cidade" name="cidade" type="text" value="<?= htmlspecialchars($user['address_street']) ?>">
                </div>
                <div class="campo-perfil">
                    <label for="perfil-estado">Estado</label>
                    <select id="perfil-estado" name="estado">
                        <option value="CE" selected>Ceará</option>
                        <option value="PB">Paraíba</option>
                        <option value="PE">Pernambuco</option>
                        <option value="RN">Rio Grande do Norte</option>
                    </select>
                </div>
                <div class="campo-perfil campo-largo">
                    <label for="perfil-bio">Sobre você</label>
                    <textarea id="perfil-bio" name="bio" rows="4" placeholder="Conte um pouco sobre você e seu interesse em sustentabilidade."></textarea>
                </div>
                <div class="perfil-form-acoes campo-largo">
                    <button type="reset" class="perfil-acao-secundaria">Cancelar</button>
                    <button type="submit" class="perfil-acao-primaria"><i class="bi bi-check-lg"></i> Salvar alterações</button>
                </div>
            </form>
        </section>
    </main>

    <div id="perfil-mensagem" class="perfil-mensagem" role="status" aria-live="polite">
        <i class="bi bi-check-circle-fill"></i> Dados atualizados com sucesso.
    </div>

    <?php require 'templates/footer.php'; ?>

    <script src="js/infoFooter.js"></script>
    <script src="js/dropdown.js"></script>
    <script src="js/perfil.js"></script>
</body>

</html>
