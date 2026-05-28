<?php 
require 'config.php'; 
require 'templates/header.php'; 
// 2. Redireciona para Login se NÃO estiver logado
if(!isset($_SESSION['user_id'])){
    // Opcional: Adiciona um parâmetro 'redirect' para levar o usuário de volta 
    // a esta página após o login.
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Prepara e executa a busca por dispositivos disponíveis
$stmt = $pdo->prepare("SELECT id, device_type, brand, model, description, `device_condition`, photo FROM devices WHERE status='available' ORDER BY created_at DESC");

// Nota: O nome da coluna 'condition' está entre acentos graves para evitar o erro de palavra reservada (conforme a Opção 2 da nossa correção anterior).
// Se você renomeou para 'device_condition', ajuste a query acima.

$stmt->execute();
$devices = $stmt->fetchAll();
?>

<h2>Adote um Aparelho!</h2>
<p class="lead">Veja os dispositivos eletrônicos que estão disponíveis para doação e reutilização. Reserve o seu!</p>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php if($devices): ?>
    <?php foreach($devices as $d): ?>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <?php 
                // Exibe a imagem se houver, ou um placeholder
                $photo_url = $d['photo'] ? e($d['photo']) : 'https://via.placeholder.com/300x200?text=Sem+Foto';
                ?>
            <img src="<?= $photo_url ?>" class="card-img-top" alt="Foto do <?= e($d['device_type']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= e($d['device_type']) ?> — <?= e($d['brand']) ?> <?= e($d['model']) ?></h5>
                <p class="card-text small text-muted">Condição:
                    <span class="badge bg-secondary"><?= ucfirst(e($d['condition'] ?? 'funcional')) ?></span>
                </p>
                <p class="card-text">
                    <?= nl2br(e(substr($d['description'], 0, 100) . (strlen($d['description']) > 100 ? '...' : ''))) ?>
                </p>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                    data-bs-target="#reserveModal<?= e($d['id']) ?>">
                    Reservar
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reserveModal<?= e($d['id']) ?>" tabindex="-1" aria-labelledby="reserveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="submit_reservation.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reserveModalLabel">Reservar: <?= e($d['device_type']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="device_id" value="<?= e($d['id']) ?>">
                        <p>Preencha seus dados para solicitar a reserva deste aparelho. Entraremos em contato para
                            agendar a retirada.</p>
                        <div class="mb-3">
                            <label class="form-label">Seu Nome</label>
                            <input type="text" name="adopter_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Seu E-mail</label>
                            <input type="email" name="adopter_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Finalidade</label>
                            <textarea name="purpose" class="form-control"
                                placeholder="Para que você utilizará este aparelho?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Confirmar Reserva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="col-12">
        <div class="alert alert-info">
            Nenhum aparelho disponível para adoção neste momento.
        </div>
    </div>
    <?php endif; ?>
</div>

<br><br><br><br><br><br><br><br><br><br><br>

<?php require 'templates/footer.php'; ?>