  <!-- templates/header.php -->
  <?php if(!isset($pdo)) require_once __DIR__ . '/../config.php'; ?>
  <!doctype html>
  <html lang="pt-BR">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>ReciclaTech</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="/assets/css/custom.css" rel="stylesheet">
  </head>

  <body>

      <div class="container">
          <header class="p-3 mb-3 border-bottom">
              <div class="container">
                  <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                      <a href="#" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                          <img src="assets/img/ReciclaTech.png" width="55px" height="55px">
                      </a>

                      <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">

                          <li><a href="index.php" class="nav-link px-2 link-secondary">Home</a></li>

                          <?php if(isset($_SESSION['user_id'])): // Usuário logado ?>
                          <li><a href="donate.php" class="nav-link px-2 link-dark">Doar</a></li>
                          <li><a href="adote.php" class="nav-link px-2 link-dark">Adotar</a></li>
                          <?php endif; ?>

                          <li><a href="ranking.php" class="nav-link px-2 link-dark">Ranking</a></li>
                          <li><a href="recycle.php" class="nav-link px-2 link-dark">Reciclar</a></li>

                      </ul>

                      <?php if(isset($_SESSION['user_id'])): // Usuário logado ?>
                      <div class="dropdown text-end">
                          <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
                              data-bs-toggle="dropdown" aria-expanded="false">
                              <img src="assets/img/profile.png" alt="mdo" width="32" height="32" class="rounded-circle">
                          </a>
                          <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                              <li><a class="dropdown-item" href="#">Olá, <?= e($_SESSION['user_nome']); ?></a></li>
                              <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                              <?php if($_SESSION['user_role'] === 'admin'): // Link do Admin apenas para admins ?>
                              <li><a class="dropdown-item" href="dashboard.php">Admin</a></li>
                              <?php endif; ?>
                              <li>
                                  <hr class="dropdown-divider">
                              </li>
                              <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                          </ul>
                      </div>
                      <?php endif; ?>


                      <?php if(!isset($_SESSION['user_id'])): // Usuário logado ?>
                      <div class="col-md-3 text-end">
                          <a href="login.php"><button type="button"
                                  class="btn btn-outline-success me-2">Login</button></a>
                          <a href="register.php"><button type="button" class="btn btn-success">Sign-up</button></a>
                      </div>
                      <?php endif; ?>

                  </div>
              </div>
          </header>
      </div>
      <div class="container py-4">