<?php if($pAdmin || !empty($userPerms['dashboard:visualizar']) || !empty($userPerms['dashboard_view'])): ?>
<li class="nav-item">
    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">
        <i class="fa-solid fa-chart-line me-2"></i>Dashboard
    </a>
</li>
<?php endif; ?>

<?php if($pAdmin || !empty($userPerms['caixa:visualizar']) || !empty($userPerms['caixa_view'])): ?>
<li>
    <a href="<?= BASE_URL ?>/caixa" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/caixa') !== false) ? 'active' : '' ?>">
        <i class="fa-solid fa-cash-register me-2"></i>Controle de Caixa
    </a>
</li>
<?php endif; ?>

<?php if($pAdmin || !empty($userPerms['movimentacao:visualizar_todas']) || !empty($userPerms['movimentacoes_view'])): ?>
<li>
    <a href="<?= BASE_URL ?>/movimentacoes" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/movimentacoes') !== false) ? 'active' : '' ?>">
        <i class="fa-solid fa-money-bill-transfer me-2"></i>Movimentações
    </a>
</li>
<?php endif; ?>

<?php if($pAdmin || !empty($userPerms['os:visualizar']) || !empty($userPerms['os_view'])): ?>
<li>
    <a href="<?= BASE_URL ?>/os" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/os') !== false) ? 'active' : '' ?>">
        <i class="fa-solid fa-clipboard-list me-2"></i>Ordens de Serviço
    </a>
</li>
<?php endif; ?>

<?php if($pAdmin || !empty($userPerms['venda:visualizar']) || !empty($userPerms['vendas_view'])): ?>
<li>
    <a href="<?= BASE_URL ?>/vendas" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/vendas') !== false) ? 'active' : '' ?>">
        <i class="fa-solid fa-tags me-2"></i>Vendas
    </a>
</li>
<?php endif; ?>

<?php if($pAdmin || !empty($userPerms['relatorios:visualizar']) || !empty($userPerms['relatorios_view'])): ?>
<li>
    <a href="<?= BASE_URL ?>/relatorios" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/relatorios') !== false) ? 'active' : '' ?>">
        <i class="fa-solid fa-file-invoice-dollar me-2"></i>Relatórios Mensais
    </a>
</li>
<?php endif; ?>

<?php if($pAdmin || !empty($userPerms['configuracoes:visualizar']) || !empty($userPerms['configuracoes_view'])): ?>
<hr class="my-2 border-secondary">
<li>
    <a href="<?= BASE_URL ?>/config" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/config') !== false) ? 'active' : '' ?>">
        <i class="fa-solid fa-gear me-2"></i>Configurações
    </a>
</li>
<?php endif; ?>
