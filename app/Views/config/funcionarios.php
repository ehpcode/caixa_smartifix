<?php include __DIR__ . '/_tabs.php'; ?>

<div class="row">
    <!-- Lista de Funcionários -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-user-tie me-2 text-primary"></i>Funcionários Cadastrados</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Nome</th>
                            <th>Cargo/Função</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($funcionarios)): ?>
                            <tr><td colspan="4" class="py-4 text-muted">Nenhum funcionário cadastrado.</td></tr>
                        <?php else: ?>
                            <?php foreach($funcionarios as $f): ?>
                            <tr>
                                <td class="text-start fw-bold"><?= htmlspecialchars($f['nome']) ?></td>
                                <td><?= htmlspecialchars($f['cargo'] ?? '-') ?></td>
                                <td>
                                    <?php if($f['ativo']): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editarFuncionario(<?= $f['id'] ?>, '<?= htmlspecialchars(addslashes($f['nome'])) ?>', '<?= htmlspecialchars(addslashes($f['cargo'] ?? '')) ?>')">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <a href="<?= BASE_URL ?>/config/funcionarios/toggle/<?= $f['id'] ?>" class="btn btn-sm <?= $f['ativo'] ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <i class="fa-solid fa-power-off"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Novo/Editar Funcionário -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0" id="cardFormFuncionario">
            <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark" id="formTitleFunc"><i class="fa-solid fa-plus me-2 text-success"></i>Novo Funcionário</h5>
                <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelarEdicao" onclick="cancelarEdicao()">Cancelar</button>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/config/funcionarios/salvar" method="POST">
                    <input type="hidden" name="id" id="inputId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome do Funcionário</label>
                        <input type="text" name="nome" id="inputNome" class="form-control" required placeholder="Ex: João Silva">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cargo/Função</label>
                        <input type="text" name="cargo" id="inputCargo" class="form-control" placeholder="Ex: Técnico">
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold" id="btnSalvarFunc">Salvar Funcionário</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editarFuncionario(id, nome, cargo) {
    document.getElementById('inputId').value = id;
    document.getElementById('inputNome').value = nome;
    document.getElementById('inputCargo').value = cargo;
    
    document.getElementById('formTitleFunc').innerHTML = '<i class="fa-solid fa-pen me-2 text-primary"></i>Editar Funcionário';
    document.getElementById('btnSalvarFunc').innerText = 'Atualizar Funcionário';
    document.getElementById('btnSalvarFunc').className = 'btn btn-primary w-100 fw-bold';
    document.getElementById('btnCancelarEdicao').classList.remove('d-none');
    
    document.getElementById('cardFormFuncionario').scrollIntoView({behavior: 'smooth'});
}

function cancelarEdicao() {
    document.getElementById('inputId').value = '';
    document.getElementById('inputNome').value = '';
    document.getElementById('inputCargo').value = '';
    
    document.getElementById('formTitleFunc').innerHTML = '<i class="fa-solid fa-plus me-2 text-success"></i>Novo Funcionário';
    document.getElementById('btnSalvarFunc').innerText = 'Salvar Funcionário';
    document.getElementById('btnSalvarFunc').className = 'btn btn-success w-100 fw-bold';
    document.getElementById('btnCancelarEdicao').classList.add('d-none');
}
</script>
