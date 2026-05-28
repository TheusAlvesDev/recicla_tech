<?php 
require 'config.php';

// 1. Proteção de Acesso
if(empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){ 
    header('Location: login.php'); 
    exit; 
}

// 2. Busca de Métricas
$stmt_tot = $pdo->query('SELECT COUNT(*) AS total FROM devices'); 
$tot = $stmt_tot->fetch();

$stmt_res_pending = $pdo->query("SELECT COUNT(*) AS total_pending FROM reservations WHERE status='pending'");
$tot_res_pending = $stmt_res_pending->fetch();

// 3. Busca de Dados de Listagem
$stmt_donations = $pdo->query('SELECT * FROM donations ORDER BY created_at DESC LIMIT 20'); 
$donations = $stmt_donations->fetchAll();

$stmt_reservations = $pdo->query('SELECT * FROM reservations ORDER BY created_at DESC LIMIT 20');
$reservations = $stmt_reservations->fetchAll();

// 4. Captura de Mensagens
$message = '';
$message_type = '';
if (isset($_GET['success'])) {
    $message = $_GET['success'];
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    $message = 'Erro ao processar a ação. Código: ' . $_GET['error'];
    $message_type = 'danger';
}
?>

<?php require 'templates/header.php'; ?>

<h3>Dashboard Administrativo</h3>

<?php 
// Exibe a mensagem de feedback se existir
if ($message): 
?>
<div class="alert alert-<?php echo e($message_type); ?> alert-dismissible fade show" role="alert">
    <?php echo e($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

---

<h4>📊 Visão Geral</h4>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3 bg-primary text-white">
            Total de Aparelhos Cadastrados:
            <h2 class="mt-2"><?php echo $tot['total']; ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 bg-warning text-dark">
            Reservas Pendentes de Ação:
            <h2 class="mt-2"><?php echo $tot_res_pending['total_pending']; ?></h2>
        </div>
    </div>
</div>

---

<h4>🚨 Gestão de Novas Reservas</h4>
<p>Pedidos de aparelhos que aguardam aprovação ou contato.</p>
<table class="table table-bordered table-hover small">
    <thead>
        <tr>
            <th>#</th>
            <th>Dispositivo</th>
            <th>Adotante</th>
            <th>Finalidade</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($reservations as $r):
      $stmtD = $pdo->prepare('SELECT device_type, brand, model FROM devices WHERE id=:id');
      $stmtD->execute([':id'=>$r['device_id']]); $dev = $stmtD->fetch();
      // Define a cor da linha baseado no status
      $row_class = match($r['status']) {
          'pending' => 'table-warning',
          'approved' => 'table-success',
          default => ''
      };
    ?>
        <tr class="<?php echo $row_class; ?>">
            <td><?php echo e($r['id']); ?></td>
            <td>
                <strong><?php echo e($dev['device_type'].' - '.$dev['brand']); ?></strong>
            </td>
            <td>
                <?php echo e($r['adopter_name']); ?><br>
                <a href="mailto:<?php echo e($r['adopter_email']); ?>"><?php echo e($r['adopter_email']); ?></a>
            </td>
            <td><?php echo e(substr($r['purpose'], 0, 50)); ?>...</td>
            <td><span
                    class="badge bg-<?php echo ($r['status'] == 'pending' ? 'warning' : ($r['status'] == 'approved' ? 'success' : 'secondary')); ?>"><?php echo e(ucfirst($r['status'])); ?></span>
            </td>
            <td>
                <?php if($r['status'] == 'pending'): ?>
                <form action="process_reservation.php" method="POST" class="d-inline">
                    <input type="hidden" name="reservation_id" value="<?php echo e($r['id']); ?>">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-sm btn-success">Aprovar</button>
                </form>

                <form action="process_reservation.php" method="POST" class="d-inline">
                    <input type="hidden" name="reservation_id" value="<?php echo e($r['id']); ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-sm btn-danger">Rejeitar</button>
                </form>
                <?php else: ?>
                <span class="text-muted">Processado</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$reservations): ?>
        <tr>
            <td colspan="6" class="text-center">Nenhuma reserva recente para mostrar.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

---

<h4>📦 Histórico de Doações</h4>
<p>Registros de coleta/entrega de dispositivos.</p>
<table class="table table-striped table-hover small">
    <thead>
        <tr>
            <th>#</th>
            <th>Dispositivo</th>
            <th>Doado em</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($donations as $d):
      $stmtD = $pdo->prepare('SELECT device_type,brand,model FROM devices WHERE id=:id');
      $stmtD->execute([':id'=>$d['device_id']]); $dev = $stmtD->fetch();
    ?>
        <tr>
            <td><?php echo e($d['id']); ?></td>
            <td><?php echo e($dev['device_type'].' - '.$dev['brand'].' '.$dev['model']); ?></td>
            <td><?php echo e($d['created_at']); ?></td>
            <td><span class="badge bg-secondary"><?php echo e(ucfirst($d['status'])); ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require 'templates/footer.php'; ?>