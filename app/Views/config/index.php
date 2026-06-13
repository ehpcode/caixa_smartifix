<?php include __DIR__ . '/_tabs.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <p class="text-muted mb-0">Navegue pelas abas acima para gerenciar os acessos, cadastros e preferências da loja.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Meu Perfil -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/config/perfil'">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-2">👤</span>
                </div>
                <h5 class="fw-bold text-dark">Meu Perfil</h5>
                <p class="text-muted mb-0">Altere seu nome, e-mail e redefina sua senha de acesso.</p>
            </div>
        </div>
    </div>

    <!-- Gestão de Equipe -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/config/usuarios'">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-2">👥</span>
                </div>
                <h5 class="fw-bold text-purple">Usuários do sistema</h5>
                <p class="text-muted mb-0">Adicione usuários, defina seus perfis de acesso e gerencie bloqueios.</p>
            </div>
        </div>
    </div>

    <!-- Cadastros Financeiros -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/config/financeiro'">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-2">🏦</span>
                </div>
                <h5 class="fw-bold text-orange">Cadastros Financeiros</h5>
                <p class="text-muted mb-0">Gerencie Contas (Bancos), Formas de Pagamento e Naturezas.</p>
            </div>
        </div>
    </div>

    <!-- Perfis de Acesso -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/config/perfis'">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-2">🔑</span>
                </div>
                <h5 class="fw-bold text-info">Perfis de Acesso</h5>
                <p class="text-muted mb-0">Crie regras e determine módulos que cada cargo acessa.</p>
            </div>
        </div>
    </div>

    <!-- Fornecedores -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/config/fornecedores'">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-2">🚚</span>
                </div>
                <h5 class="fw-bold text-success">Fornecedores</h5>
                <p class="text-muted mb-0">Cadastre fornecedores para vincular aos custos operacionais de peças.</p>
            </div>
        </div>
    </div>

    <!-- Funcionários -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/config/funcionarios'">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-2">👔</span>
                </div>
                <h5 class="fw-bold text-secondary">Funcionários</h5>
                <p class="text-muted mb-0">Gerencie funcionários e prestadores para comissões e vínculos.</p>
            </div>
        </div>
    </div>

    <!-- Auditoria -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 hover-shadow transition" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/config/auditoria'">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-2">🕒</span>
                </div>
                <h5 class="fw-bold text-dark">Auditoria e Logs</h5>
                <p class="text-muted mb-0">Acompanhe todas as alterações críticas feitas no sistema.</p>
            </div>
        </div>
    </div>
</div>
<style>
.hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.transition { transition: all 0.3s ease; }
</style>
