<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body bg-light border-bottom rounded-top">
                <form method="GET" action="<?= BASE_URL ?>/vendas" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-muted small">Data Início</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-muted small">Data Fim</label>
                        <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-filter me-2"></i>Filtrar</button>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>ID da Venda</th>
                                <th class="text-start">Cliente</th>
                                <th class="text-start">Produto/Acessório</th>
                                <th class="text-end">Valor Total (R$)</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($itens)): ?>
                                <tr><td colspan="6" class="py-5 text-muted">Nenhum registro de venda encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach($itens as $i): ?>
                                <tr>
                                    <td class="text-muted small"><?= date('d/m/Y', strtotime($i['data'])) ?></td>
                                    <td>
                                        <span class="badge bg-success">Venda #<?= $i['id'] ?></span>
                                    </td>
                                    <td class="text-start fw-bold"><?= htmlspecialchars($i['cliente'] ?: 'N/I') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($i['item'] ?: 'N/I') ?></td>
                                    <td class="text-end fw-bold text-success">
                                        R$ <?= number_format($i['valor_total'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                            onclick="verDetalhesItem(
                                                '<?= htmlspecialchars($i['cliente']) ?>',
                                                '<?= htmlspecialchars($i['item']) ?>',
                                                '<?= htmlspecialchars($i['descricao']) ?>',
                                                '<?= date('d/m/Y H:i', strtotime($i['data'])) ?>',
                                                '<?= htmlspecialchars($i['funcionario_nome']) ?>',
                                                '<?= number_format($i['valor_total'], 2, ',', '.') ?>'
                                            )">
                                            <i class="fa-solid fa-list-ul"></i>
                                        </button>
                                        <?php if (!empty($_SESSION['permissoes']['todas']) || !empty($_SESSION['permissoes']['venda:editar'])): ?>
                                        <button class="btn btn-sm btn-outline-warning ms-1" onclick="editarVenda(<?= $i['id'] ?>)">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <?php endif; ?>
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
</div>

<!-- Modal Detalhes Item -->
<div class="modal fade" id="modalDetalhesItem" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fa-solid fa-tags me-2"></i>Detalhes do Registro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr><td class="text-muted text-end w-50">Data de Registro:</td><td class="fw-bold" id="diData"></td></tr>
                        <tr><td class="text-muted text-end">Cliente:</td><td class="fw-bold" id="diCli"></td></tr>
                        <tr><td class="text-muted text-end">Produto / Acessório:</td><td class="fw-bold" id="diItem"></td></tr>
                        <tr><td class="text-muted text-end">Descrição:</td><td class="fw-bold" id="diDesc"></td></tr>
                        <tr><td class="text-muted text-end">Criado por:</td><td class="fw-bold" id="diFunc"></td></tr>
                        <tr><td class="text-muted text-end">Valor Total Gerado:</td><td class="fw-bold fs-5 text-success" id="diValor"></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verDetalhesItem(cli, item, desc, data, func, valor) {
    document.getElementById('diCli').innerText = cli || '-';
    document.getElementById('diItem').innerText = item || '-';
    document.getElementById('diDesc').innerText = desc || '-';
    document.getElementById('diData').innerText = data;
    document.getElementById('diFunc').innerText = func || 'Sistema';
    document.getElementById('diValor').innerText = 'R$ ' + valor;

    var modal = new bootstrap.Modal(document.getElementById('modalDetalhesItem'));
    modal.show();
}

function editarVenda(id) {
    fetch('<?= BASE_URL ?>/vendas/buscar?id=' + id)
    .then(res => res.json())
    .then(data => {
        if(data.erro) {
            alert(data.erro);
            return;
        }
        document.getElementById('editId').value = data.id;
        document.getElementById('editCliente').value = data.cliente || '';
        document.getElementById('editItem').value = data.item || '';
        document.getElementById('editDescricao').value = data.descricao || '';
        document.getElementById('editValorTotal').value = parseFloat(data.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        
        let container = document.getElementById('editCustosContainer');
        container.innerHTML = '';
        if(data.custos && data.custos.length > 0) {
            data.custos.forEach(c => addCustoRow(c));
        }
        
        new bootstrap.Modal(document.getElementById('modalEditarVenda')).show();
    });
}

function addCustoRow(custo = null) {
    const container = document.getElementById('editCustosContainer');
    const row = document.createElement('div');
    row.className = 'row mb-2 custo-row align-items-center';
    
    let fornecedoresHtml = '<option value="">Selecione o fornecedor...</option>';
    <?php if(isset($fornecedores)): foreach($fornecedores as $forn): ?>
    fornecedoresHtml += `<option value="<?= $forn['id'] ?>"><?= htmlspecialchars($forn['nome']) ?></option>`;
    <?php endforeach; endif; ?>

    let idHtml = custo ? `<input type="hidden" name="custo_id[]" value="${custo.id}">` : '';
    let desc = custo ? custo.descricao : '';
    let tipo = custo ? custo.tipo : 'estoque';
    let forn = custo ? custo.fornecedor_id : '';
    let valor = custo ? parseFloat(custo.valor).toLocaleString('pt-BR', {minimumFractionDigits:2}) : '';

    row.innerHTML = `
        ${idHtml}
        <div class="col-md-3">
            <input type="text" name="custo_descricao[]" class="form-control form-control-sm" placeholder="Ex: Tela Display" value="${desc}">
        </div>
        <div class="col-md-3">
            <select name="custo_tipo[]" class="form-select form-select-sm" onchange="toggleCustoFornecedor(this)">
                <option value="estoque" ${tipo === 'estoque' ? 'selected' : ''}>Estoque (Interno)</option>
                <option value="fornecedor" ${tipo === 'fornecedor' ? 'selected' : ''}>Fornecedor (Externo)</option>
                <option value="mao_obra" ${tipo === 'mao_obra' ? 'selected' : ''}>Mão de Obra</option>
            </select>
        </div>
        <div class="col-md-3 custo-fornecedor-container" style="${tipo === 'fornecedor' ? '' : 'display:none;'}">
            <select name="custo_fornecedor[]" class="form-select form-select-sm">
                ${fornecedoresHtml}
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="custo_valor[]" class="form-control form-control-sm input-custo-valor" placeholder="R$ 0,00" value="${valor}">
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.custo-row').remove()">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    
    if (tipo === 'fornecedor' && forn) {
        row.querySelector('select[name="custo_fornecedor[]"]').value = forn;
    }

    var mask = IMask(row.querySelector('.input-custo-valor'), {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: '.',
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: ','
    });
}

function toggleCustoFornecedor(select) {
    let container = select.closest('.custo-row').querySelector('.custo-fornecedor-container');
    if (select.value === 'fornecedor') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
        container.querySelector('select').value = '';
    }
}
</script>

<!-- Modal Editar Venda -->
<div class="modal fade" id="modalEditarVenda" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= BASE_URL ?>/vendas/atualizar" method="POST" class="modal-content">
            <input type="hidden" name="id" id="editId">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i>Editar Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Cliente (Opcional)</label>
                        <input type="text" name="cliente" id="editCliente" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Produto / Acessório</label>
                        <input type="text" name="item" id="editItem" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição Adicional</label>
                    <input type="text" name="descricao" id="editDescricao" class="form-control">
                </div>
                <div class="mb-3 w-50">
                    <label class="form-label">Valor Total (R$)</label>
                    <input type="text" name="valor_total" id="editValorTotal" class="form-control fw-bold text-success" required>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-secondary"><i class="fa-solid fa-boxes-stacked me-2"></i>Custos Associados</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustoRow()"><i class="fa-solid fa-plus me-1"></i>Adicionar Custo</button>
                </div>
                <div id="editCustosContainer" class="bg-light p-3 border rounded"></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning fw-bold text-dark">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<!-- IMask -->
<script src="https://unpkg.com/imask"></script>
<script>
    if (document.getElementById('editValorTotal')) {
        IMask(document.getElementById('editValorTotal'), {
            mask: Number,
            scale: 2,
            signed: false,
            thousandsSeparator: '.',
            padFractionalZeros: true,
            normalizeZeros: true,
            radix: ','
        });
    }
</script>
