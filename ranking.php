<?php 
require 'config.php';
require 'templates/header.php'; 

// Busca os 20 usuários com mais pontos, excluindo administradores e usuários sem nome ou pontos (opcional)
$stmt = $pdo->prepare("SELECT name, points, role FROM users 
                      WHERE name IS NOT NULL AND points > 0 AND role = 'user'
                      ORDER BY points DESC, created_at ASC 
                      LIMIT 20");
$stmt->execute();
$ranking = $stmt->fetchAll();
?>

<h2 class="text-center mb-4">🏆 Hall da Fama ReciclaTech</h2>

<p class="lead text-center">Reconhecemos os usuários que mais contribuem com a Economia Circular, seja doando ou
    adotando aparelhos.</p>
<p class="text-center small text-muted"><b>Ações valem pontos: Doar (50 pts), Adotar (30 pts).</b></p>

<div class="row justify-content-center">
    <div class="col-md-8">
        <table class="table table-striped table-hover mt-4 shadow-lg">
            <thead class="table-primary">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Usuário</th>
                    <th scope="col" class="text-center">Pontuação</th>
                </tr>
            </thead>
            <tbody>
                <?php if($ranking): ?>
                <?php $rank = 1; foreach($ranking as $user): ?>
                <tr class="<?php echo ($rank <= 3) ? 'table-warning fw-bold' : ''; ?>">
                    <th scope="row"><?= $rank ?>º</th>
                    <td>
                        <?= e($user['name']) ?>
                        <?php if ($rank == 1): ?><span
                            class="badge bg-warning text-dark ms-2">OURO</span><?php endif; ?>
                        <?php if ($rank == 2): ?><span class="badge bg-secondary ms-2">PRATA</span><?php endif; ?>
                        <?php if ($rank == 3): ?><span class="badge bg-danger ms-2">BRONZE</span><?php endif; ?>
                    </td>
                    <td class="text-center"><?= number_format(e($user['points']), 0, ',', '.') ?> pts</td>
                </tr>
                <?php $rank++; endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Nenhum usuário com pontuação para exibir no momento.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<br><br><br><br>
<br><br><br><br>

<?php require 'templates/footer.php'; ?>