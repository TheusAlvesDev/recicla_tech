<?php
require 'config.php';

// 1. VERIFICAR AUTENTICAÇÃO
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
            address_zipcode 
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

    // 4. BUSCAR HISTÓRICO DE ADOÇÕES (Itens que o usuário reservou/adotou)
    $adopted_stmt = $pdo->prepare("
        SELECT r.*, d.description AS device_name, d.status AS device_status 
        FROM reservations r
        JOIN devices d ON r.device_id = d.id
        WHERE r.device_id = ? 
        ORDER BY r.created_at DESC
    ");
    $adopted_stmt->execute([$user_id]);
    $adopted_items = $adopted_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Tratar erro de banco de dados
    die("Erro ao carregar dados do usuário: " . $e->getMessage());
}

require 'templates/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4">Meu Perfil: <?= htmlspecialchars($user['name']) ?></h2>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        Dados atualizados com sucesso!
    </div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                type="button" role="tab" aria-controls="overview" aria-selected="true">Visão Geral</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="donations-tab" data-bs-toggle="tab" data-bs-target="#donations" type="button"
                role="tab" aria-controls="donations" aria-selected="false">Minhas Doações</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="adoptions-tab" data-bs-toggle="tab" data-bs-target="#adoptions" type="button"
                role="tab" aria-controls="adoptions" aria-selected="false">Minhas Adoções</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab"
                aria-controls="edit" aria-selected="false">Editar Dados</button>
        </li>
    </ul>

    <div class="tab-content border p-3 bg-white shadow-sm">

        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <h4 class="mb-3">Estatísticas Rápidas</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card text-center bg-light p-3">
                        <h5>Itens Doados</h5>
                        <p class="display-4 text-primary"><?= count($donated_items) ?></p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center bg-light p-3">
                        <h5>Itens Adotados</h5>
                        <p class="display-4 text-success"><?= count(array_filter($adopted_items, function($item) {
                            return $item['status'] === 'approved';
                        })) ?></p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center bg-light p-3">
                        <h5>Reservas Pendentes</h5>
                        <p class="display-4 text-warning"><?= count(array_filter($adopted_items, function($item) {
                            return $item['status'] === 'pending';
                        })) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="donations" role="tabpanel" aria-labelledby="donations-tab">
            <h4 class="mb-3">Lista de Itens que Você Doou</h4>
            <?php if (empty($donated_items)): ?>
            <div class="alert alert-info">Você ainda não doou nenhum item. Que tal começar agora?</div>
            <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome do Item</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Data da Doação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donated_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['device_type']) ?></td>
                        <td><?= htmlspecialchars($item['device_condition']) ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($item['status']) ?></span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                        <td>
                            <?php if ($item['status'] === 'available'): ?>
                            <a href="edit_device.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-info me-2">Editar</a>
                            <a href="delete_device.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Tem certeza que deseja apagar este item? Esta ação é irreversível.')">
                                Apagar
                            </a>
                            <?php else: ?>
                            <span class="text-muted small">Não Editável (Status:
                                <?= htmlspecialchars($item['status']) ?>)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="adoptions" role="tabpanel" aria-labelledby="adoptions-tab">
            <h4 class="mb-3">Histórico de Reservas / Adoções</h4>
            <?php if (empty($adopted_items)): ?>
            <div class="alert alert-info">Você ainda não reservou nenhum item. Explore a seção de Adoções!</div>
            <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Status da Reserva</th>
                        <th>Status do Item</th>
                        <th>Data da Reserva</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adopted_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['device_name']) ?></td>
                        <td><span
                                class="badge bg-<?= $item['status'] === 'approved' ? 'success' : ($item['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= htmlspecialchars(ucfirst($item['status'])) ?>
                            </span></td>
                        <td><span class="badge bg-info"><?= htmlspecialchars($item['device_status']) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($item['reservation_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
            <h4 class="mb-3">Editar Informações Cadastrais</h4>
            <form action="update_profile.php" method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email"
                            value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        <small class="form-text text-muted">O email não pode ser alterado.</small>
                    </div>
                </div>

                <h5 class="mt-4 mb-3 text-primary">Editar Endereço</h5>

                <div class="row">
                    <div class="col-md-9 mb-3">
                        <label for="address_street" class="form-label">Rua / Avenida</label>
                        <input type="text" class="form-control" id="address_street" name="address_street"
                            value="<?= htmlspecialchars($user['address_street']) ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="address_number" class="form-label">Número</label>
                        <input type="text" class="form-control" id="address_number" name="address_number"
                            value="<?= htmlspecialchars($user['address_number']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="address_complement" class="form-label">Complemento (Opcional)</label>
                        <input type="text" class="form-control" id="address_complement" name="address_complement"
                            value="<?= htmlspecialchars($user['address_complement']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address_zipcode" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="address_zipcode" name="address_zipcode"
                            value="<?= htmlspecialchars($user['address_zipcode']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="address_city" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="address_city" name="address_city"
                            value="<?= htmlspecialchars($user['address_city']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address_state" class="form-label">Estado</label>
                        <input type="text" class="form-control" id="address_state" name="address_state"
                            value="<?= htmlspecialchars($user['address_state']) ?>" required>
                    </div>
                </div>

                <hr>

                <h5 class="mt-4 mb-3 text-danger">Mudar Senha</h5>
                <p class="small text-muted">Preencha os campos abaixo apenas se desejar mudar sua senha.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new_password" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg">Salvar Alterações</button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php require 'templates/footer.php'; ?>