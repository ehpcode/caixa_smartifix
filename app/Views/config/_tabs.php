<?php
$currentPath = $_SERVER['REQUEST_URI'];
?>
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs fw-bold">
            <li class="nav-item">
                <a class="nav-link <?= $currentPath == BASE_URL.'/config' ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config">
                    <i class="fa-solid fa-home me-1"></i>Início
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($currentPath, '/config/perfil') !== false && strpos($currentPath, 'perfis') === false ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config/perfil">
                    <i class="fa-solid fa-user me-1"></i>Meu Perfil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($currentPath, '/config/usuarios') !== false ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config/usuarios">
                    <i class="fa-solid fa-users me-1"></i>Usuários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($currentPath, '/config/perfis') !== false ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config/perfis">
                    <i class="fa-solid fa-key me-1"></i>Perfis de Acesso
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($currentPath, '/config/financeiro') !== false ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config/financeiro">
                    <i class="fa-solid fa-building-columns me-1"></i>Dados Financeiros
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($currentPath, '/config/funcionarios') !== false ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config/funcionarios">
                    <i class="fa-solid fa-user-tie me-1"></i>Funcionários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($currentPath, '/config/fornecedores') !== false ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config/fornecedores">
                    <i class="fa-solid fa-truck me-1"></i>Fornecedores
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($currentPath, '/config/auditoria') !== false ? 'active text-primary' : 'text-muted' ?>" href="<?= BASE_URL ?>/config/auditoria">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i>Auditoria
                </a>
            </li>
        </ul>
    </div>
</div>
