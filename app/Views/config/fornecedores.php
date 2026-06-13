<?php include __DIR__ . '/_tabs.php'; ?>

<div class="row">
    <!-- Lista de Fornecedores -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-truck me-2 text-primary"></i>Fornecedores Cadastrados</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Nome / Razão Social</th>
                            <th>Cadastrado Por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($fornecedores)): ?>
                            <tr><td colspan="2" class="py-4 text-muted">Nenhum fornecedor cadastrado.</td></tr>
                        <?php else: ?>
                            <?php foreach($fornecedores as $f): ?>
                            <tr>
                                <td class="text-start fw-bold"><?= htmlspecialchars($f['nome']) ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($f['cadastrado_por_nome'] ?? 'Sistema') ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Novo Fornecedor -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-plus me-2 text-success"></i>Novo Fornecedor</h5>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/config/fornecedores/salvar" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome do Fornecedor</label>
                        <input type="text" name="nome" class="form-control" required placeholder="Ex: Fornecedor de Telas S/A">
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold">Salvar Fornecedor</button>
                </form>
            </div>
        </div>
    </div>
</div>
