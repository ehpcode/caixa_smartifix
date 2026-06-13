<?php include __DIR__ . '/../_tabs.php'; ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <a href="<?= BASE_URL ?>/config" class="btn btn-light border-2 fw-bold btn-sm mb-3 text-secondary">&larr; Voltar às Configurações</a>
        <h4 class="text-secondary fw-bold mb-0"><i class="fa-solid fa-key me-1"></i> Perfis de Acesso</h4>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= BASE_URL ?>/config/perfis/novo" class="btn btn-purple fw-bold shadow-sm">+ Novo Perfil</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nome do Perfil</th>
                        <th>Descrição</th>
                        <th>Regras de Acesso (Módulos)</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($perfis as $p): 
                        $json = json_decode($p['permissoes'], true);
                        $modulos = is_array($json) ? array_keys(array_filter($json)) : [];
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($p['nome']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($p['descricao'] ?? '-') ?></td>
                        <td>
                            <?php if(!empty($modulos)): ?>
                                <?php foreach($modulos as $mod): ?>
                                    <span class="badge bg-light text-secondary border"><?= ucfirst($mod) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="badge bg-danger">Acesso Restrito / Total</span>
                            <?php endif; ?>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>/config/perfis/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary fw-bold">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
