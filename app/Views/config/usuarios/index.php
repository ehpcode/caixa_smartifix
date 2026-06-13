<?php include __DIR__ . '/../_tabs.php'; ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <a href="<?= BASE_URL ?>/config" class="btn btn-light border-2 fw-bold btn-sm mb-3 text-secondary">&larr; Voltar às Configurações</a>
        <h4 class="text-secondary fw-bold mb-0">Usuários</h4>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= BASE_URL ?>/config/usuarios/novo" class="btn btn-orange fw-bold shadow-sm">+ Novo Usuário</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $user): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($user['nome']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($user['perfil_nome'] ?? 'Padrão') ?></span></td>
                        <td>
                            <?php if($user['ativo']): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Bloqueado</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?= BASE_URL ?>/config/usuarios/editar/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary fw-bold me-2">Editar</a>
                            <?php if($user['id'] != $_SESSION['usuario_id']): ?>
                                <a href="<?= BASE_URL ?>/config/usuarios/toggle/<?= $user['id'] ?>" class="btn btn-sm <?= $user['ativo'] ? 'btn-outline-danger' : 'btn-outline-success' ?> fw-bold">
                                    <?= $user['ativo'] ? 'Bloquear Acesso' : 'Desbloquear' ?>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-muted">Você</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
