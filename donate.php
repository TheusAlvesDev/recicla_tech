<!-- donate.php -->
<?php require 'templates/header.php'; 
// 2. Redireciona para Login se NÃO estiver logado
if(!isset($_SESSION['user_id'])){
    // Opcional: Adiciona um parâmetro 'redirect' para levar o usuário de volta 
    // a esta página após o login.
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

?>
<h2>Cadastro de Doação</h2>
<form action="submit_donation.php" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nome</label>
        <input name="donor_name" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">E-mail</label>
        <input name="donor_email" type="email" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipo de aparelho</label>
        <input name="device_type" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Marca</label>
        <input name="brand" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Modelo</label>
        <input name="model" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Condição</label>
        <select name="condition" class="form-select">
            <option value="funcional">Funcional</option>
            <option value="com_defeito">Com defeito</option>
            <option value="para_pecas">Para peças</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Foto (opcional)</label>
        <input name="photo" type="file" accept="image/*" class="form-control">
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Enviar doação</button>
    </div>
</form>
<?php require 'templates/footer.php'; ?>