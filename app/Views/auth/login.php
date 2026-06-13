<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartiFix Caixa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card p-4 shadow-sm">
                <div class="text-center mb-4">
                    <img src="<?= BASE_URL ?>/assets/img/logotipo_smartifix.svg" alt="SmartiFix Logo" class="img-fluid mb-3" style="height: 60px; width: auto;">
                    <p class="text-muted">Sistema de Controle de Caixa</p>
                </div>

                <?php if (isset($_SESSION['erro_login'])): ?>
                    <div class="alert alert-danger text-center">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= $_SESSION['erro_login']; unset($_SESSION['erro_login']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-orange w-100 py-2 fw-bold">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
