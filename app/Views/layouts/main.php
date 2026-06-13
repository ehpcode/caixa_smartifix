<?php
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/");
    exit;
}
$userPerms = $this->getPermissoesUsuario();
$pAdmin = isset($userPerms['todas']) && $userPerms['todas'] === true;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SmartiFix Caixa' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <script src="<?= BASE_URL ?>/assets/js/chart.min.js"></script>
</head>
<body>

<!-- Navbar Topo Mobile -->
<nav class="navbar navbar-dark bg-purple d-md-none px-3 py-2 shadow-sm">
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/dashboard">
        <img src="<?= BASE_URL ?>/assets/img/logotipo_smartifix.svg" alt="SmartiFix Logo" height="30" style="filter: brightness(0) invert(1);">
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<!-- Offcanvas Sidebar (Mobile) -->
<div class="offcanvas offcanvas-start sidebar text-white" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header d-flex justify-content-between align-items-center px-4 pt-4 pb-2 border-bottom border-secondary">
        <img src="<?= BASE_URL ?>/assets/img/logotipo_smartifix.svg" alt="SmartiFix Logo" height="30" style="filter: brightness(0) invert(1);">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-4 pt-3">
        <ul class="nav nav-pills flex-column mb-auto gap-2">
            <?php include __DIR__ . '/../partials/sidebar_menu.php'; ?>
        </ul>
        <hr class="border-secondary mt-auto mb-3">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUserMobile" data-bs-toggle="dropdown" aria-expanded="false">
                <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUserMobile">
                <li><a class="dropdown-item" href="#">Perfil: <?= htmlspecialchars($_SESSION['perfil_nome'] ?? '') ?></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger fw-bold" href="<?= BASE_URL ?>/logout">Sair</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Container Principal com Sidebar Desktop Fixa -->
<div class="d-flex flex-column flex-md-row">
    <!-- Sidebar Desktop -->
    <div class="sidebar d-none d-md-flex flex-column flex-shrink-0 p-3" style="width: 280px; position: sticky; top: 0; height: 100vh; overflow-y: auto;">
        <div class="w-100 d-flex align-items-center mb-3 mb-md-0 me-md-auto" style="height: 50px;">
            <a href="<?= BASE_URL ?>/dashboard" class="text-white text-decoration-none">
                <img src="<?= BASE_URL ?>/assets/img/logotipo_smartifix.svg" alt="SmartiFix Logo" height="40" style="filter: brightness(0) invert(1);">
            </a>
        </div>
        <hr class="border-secondary">
        <ul class="nav nav-pills flex-column mb-auto gap-2">
            <?php include __DIR__ . '/../partials/sidebar_menu.php'; ?>
        </ul>
        <hr class="border-secondary">
        <div class="dropdown mt-auto">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong>
            </a>
            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
                <li><a class="dropdown-item" href="#">Perfil: <?= htmlspecialchars($_SESSION['perfil_nome'] ?? '') ?></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger fw-bold" href="<?= BASE_URL ?>/logout">Sair</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid p-3 p-md-4 w-100" style="background-color: #f8f9fa; min-height: 100vh;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom" >
            <h1 class="h3 mb-3 mb-md-0 text-gray-800 fw-bold"><?= $title ?? 'Painel' ?></h1>
            <div class="text-md-end">
                <span class="badge bg-purple px-3 py-2 fs-6 shadow-sm rounded-pill"><i class="fa-regular fa-calendar me-2"></i><?= date('d/m/Y') ?></span>
            </div>
        </div>

        <?php if(isset($_SESSION['msg_erro'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    Toast.fire({
                        icon: 'error',
                        title: '<?= addslashes($_SESSION['msg_erro']) ?>'
                    });
                });
            </script>
            <?php unset($_SESSION['msg_erro']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['msg_sucesso'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    Toast.fire({
                        icon: 'success',
                        title: '<?= addslashes($_SESSION['msg_sucesso']) ?>'
                    });
                });
            </script>
            <?php unset($_SESSION['msg_sucesso']); ?>
        <?php endif; ?>

        <?php if (isset($contentView) && file_exists(__DIR__ . '/../' . $contentView . '.php')) {
            require_once __DIR__ . '/../' . $contentView . '.php';
        } ?>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
