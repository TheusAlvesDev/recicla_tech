<?php require 'templates/header.php'; ?>
<h5 class="card-title">Como funciona</h5>
<br>
<ol>
    <li>Você cadastra um aparelho para doação.</li>
    <li>Organizações parceiras ou interessados podem reservar o aparelho.</li>
    <li>Coleta ou entrega é agendada, ou dispositivo vai para pontos de reciclagem.</li>
</ol>
<a class="btn btn-success" href="donate.php">Quero doar</a>

<br><br><br>

<div class="row">
    <div class="col-sm-6">
        <div class="card mb-3">
            <div class="card-body">
                <h6>O que doar</h6>
                <p>Celulares, notebooks, tablets, roteadores, pequenas placas e acessórios.</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card mb-3">
            <div class="card-body">
                <h6>O que não doar</h6>
                <p>Baterias soltas e itens perigosos exigem preparação. Siga as instruções locais.</p>
            </div>
        </div>
    </div>
</div>

<br>

<hr />

<br>

<div class="col-md-5">
    <div class="card">
        <div class="card-body">
            <h5>Últimos aparelhos cadastrados</h5>
            <?php
$stmt = $pdo->query("SELECT * FROM devices WHERE status='available' ORDER BY created_at DESC LIMIT 6");
$devices = $stmt->fetchAll();
if($devices){
echo '<ul class="list-group list-group-flush">';
foreach($devices as $d){
echo '<li class="list-group-item">' . e($d['device_type']) . ' — ' . e($d['brand']) . ' ' . e($d['model']) . '</li>';
}
echo '</ul>';
} else {
echo '<div class="text-muted">Nenhum aparelho disponível no momento.</div>';
}
?>
        </div>
    </div>
</div>
</div>

<br>
<br>
<br>

<?php require 'templates/footer.php'; ?>