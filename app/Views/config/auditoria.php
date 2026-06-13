<?php include __DIR__ . '/_tabs.php'; ?>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Logs de Auditoria</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Data / Hora</th>
                            <th>Usuário</th>
                            <th>Tabela Afetada</th>
                            <th>Registro (ID)</th>
                            <th>Operação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logs)): ?>
                            <tr><td colspan="5" class="py-4 text-muted">Nenhum log de auditoria encontrado.</td></tr>
                        <?php else: ?>
                            <?php foreach($logs as $l): ?>
                            <tr>
                                <td class="text-muted small"><?= date('d/m/Y H:i:s', strtotime($l['criado_em'])) ?></td>
                                <td class="fw-bold text-start"><?= htmlspecialchars($l['usuario_nome']) ?></td>
                                <td><?= htmlspecialchars($l['tabela_afetada']) ?></td>
                                <td>#<?= htmlspecialchars($l['registro_id']) ?></td>
                                <td>
                                    <?php 
                                        $op = $l['operacao'];
                                        $badge = 'bg-secondary';
                                        if($op == 'insert') $badge = 'bg-success';
                                        if($op == 'update') $badge = 'bg-warning text-dark';
                                        if($op == 'delete' || $op == 'delete_logico') $badge = 'bg-danger';
                                        if($op == 'reabertura_caixa') $badge = 'bg-info text-dark';
                                        if($op == 'cancelamento') $badge = 'bg-danger text-white';
                                    ?>
                                    <span class="badge <?= $badge ?> text-uppercase"><?= htmlspecialchars($op) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
