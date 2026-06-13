<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body bg-light border-bottom rounded-top">
                <form method="GET" action="<?= BASE_URL ?>/os" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Data Início</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Data Fim</label>
                        <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos (OS, Avulso)</option>
                            <option value="os" <?= $filtros['tipo'] == 'os' ? 'selected' : '' ?>>Ordens de Serviço (OS)</option>
                            <option value="servico_avulso" <?= $filtros['tipo'] == 'servico_avulso' ? 'selected' : '' ?>>Serviços Avulsos</option>
                        </select>
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
                                <th>Nº OS / Tipo</th>
                                <th class="text-start">Cliente</th>
                                <th class="text-start">Aparelho/Item</th>
                                <th class="text-end">Valor Total (R$)</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($itens)): ?>
                                <tr><td colspan="6" class="py-5 text-muted">Nenhum registro encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach($itens as $i): ?>
                                <tr>
                                    <td class="text-muted small"><?= date('d/m/Y', strtotime($i['data'])) ?></td>
                                    <td>
                                        <?php if($i['tipo'] == 'os'): ?>
                                            <span class="badge bg-primary">OS <?= htmlspecialchars($i['numero_os']) ?></span>
                                        <?php elseif($i['tipo'] == 'venda'): ?>
                                            <span class="badge bg-success">Venda</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark">Avulso</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-start fw-bold"><?= htmlspecialchars($i['cliente'] ?: 'N/I') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($i['item'] ?: 'N/I') ?></td>
                                    <td class="text-end fw-bold text-success">
                                        R$ <?= number_format($i['valor_total'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                            onclick="verDetalhesItem(
                                                '<?= htmlspecialchars($i['tipo']) ?>',
                                                '<?= htmlspecialchars($i['numero_os']) ?>',
                                                '<?= htmlspecialchars($i['cliente']) ?>',
                                                '<?= htmlspecialchars($i['item']) ?>',
                                                '<?= htmlspecialchars($i['descricao']) ?>',
                                                '<?= date('d/m/Y H:i', strtotime($i['data'])) ?>',
                                                '<?= htmlspecialchars($i['funcionario_nome']) ?>',
                                                '<?= number_format($i['valor_total'], 2, ',', '.') ?>'
                                            )">
                                            <i class="fa-solid fa-list-ul"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="abrirModalEditar(<?= $i['id'] ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
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
                        <tr><td class="text-muted text-end">Tipo:</td><td class="fw-bold text-primary text-uppercase" id="diTipo"></td></tr>
                        <tr><td class="text-muted text-end">Nº OS Externa:</td><td class="fw-bold" id="diOs"></td></tr>
                        <tr><td class="text-muted text-end">Cliente:</td><td class="fw-bold" id="diCli"></td></tr>
                        <tr><td class="text-muted text-end">Aparelho / Produto:</td><td class="fw-bold" id="diItem"></td></tr>
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

<!-- Modal Editar Item -->
<div class="modal fade modal-lg" id="modalEditarItem" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditarItem" onsubmit="salvarEdicao(event)">
                <input type="hidden" name="id" id="editId">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fa-solid fa-pen-to-square me-2"></i>Editar Registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Nº OS Externa</label>
                        <input type="text" class="form-control" name="numero_os" id="editOs">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <input type="text" class="form-control" name="cliente" id="editCli">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aparelho / Item</label>
                        <input type="text" class="form-control" name="item" id="editItem">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <input type="text" class="form-control" name="descricao" id="editDesc">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor Total (R$)</label>
                        <input type="text" class="form-control" name="valor_total" id="editValor">
                    </div>

                    <div class="mt-4 p-3 bg-light border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Custos Operacionais</h6>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="addEditCustoRow()">
                                <i class="fa-solid fa-plus"></i> Adicionar Custo
                            </button>
                        </div>
                        <div id="editCustosContainer"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarEdit">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function verDetalhesItem(tipo, os, cli, item, desc, data, func, valor) {
    document.getElementById('diTipo').innerText = tipo;
    document.getElementById('diOs').innerText = os || '-';
    document.getElementById('diCli').innerText = cli || '-';
    document.getElementById('diItem').innerText = item || '-';
    document.getElementById('diDesc').innerText = desc || '-';
    document.getElementById('diData').innerText = data;
    document.getElementById('diFunc').innerText = func || 'Sistema';
    document.getElementById('diValor').innerText = 'R$ ' + valor;

    var modal = new bootstrap.Modal(document.getElementById('modalDetalhesItem'));
    modal.show();
}

function abrirModalEditar(id) {
    fetch('<?= BASE_URL ?>/os/buscar?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert(data.erro);
                return;
            }
            
            document.getElementById('editId').value = data.id;
            document.getElementById('editOs').value = data.numero_os || '';
            document.getElementById('editCli').value = data.cliente || '';
            document.getElementById('editItem').value = data.item || '';
            document.getElementById('editDesc').value = data.descricao || '';
            
            let valorFloat = parseFloat(data.valor_total).toFixed(2);
            document.getElementById('editValor').value = valorFloat.replace('.', ',');

            // Preencher Custos
            let custosContainer = document.getElementById('editCustosContainer');
            custosContainer.innerHTML = '';
            if (data.custos && data.custos.length > 0) {
                data.custos.forEach(custo => {
                    addEditCustoRow(custo);
                });
            }

            var modal = new bootstrap.Modal(document.getElementById('modalEditarItem'));
            modal.show();
        })
        .catch(err => alert('Erro de conexão ao buscar OS.'));
}

function addEditCustoRow(custo = null) {
    var container = document.getElementById('editCustosContainer');
    var row = document.createElement('div');
    row.className = 'row mb-2 edit-custo-row align-items-center';
    
    let custoId = custo ? custo.id : '';
    let descricao = custo ? custo.descricao : '';
    let tipo = custo ? custo.tipo : 'estoque';
    let fornId = custo ? custo.fornecedor_id : '';
    let valor = custo ? parseFloat(custo.valor).toFixed(2).replace('.', ',') : '';

    let fornecedoresOptions = '<option value="">Selecione o fornecedor...</option>';
    <?php foreach($fornecedores as $forn): ?>
        fornecedoresOptions += `<option value="<?= $forn['id'] ?>"><?= htmlspecialchars($forn['nome']) ?></option>`;
    <?php endforeach; ?>

    row.innerHTML = `
        <input type="hidden" name="custo_id[]" value="${custoId}">
        <div class="col-md-3">
            <input type="text" name="custo_descricao[]" class="form-control form-control-sm" placeholder="Ex: Tela Display" value="${descricao}">
        </div>
        <div class="col-md-3">
            <select name="custo_tipo[]" class="form-select form-select-sm" onchange="toggleEditCustoFornecedor(this)">
                <option value="estoque" ${tipo === 'estoque' ? 'selected' : ''}>Estoque (Interno)</option>
                <option value="fornecedor" ${tipo === 'fornecedor' ? 'selected' : ''}>Fornecedor (Externo)</option>
                <option value="mao_obra" ${tipo === 'mao_obra' ? 'selected' : ''}>Mão de Obra</option>
            </select>
        </div>
        <div class="col-md-3 edit-custo-forn-container" style="${tipo === 'fornecedor' ? 'display:block;' : 'display:none;'}">
            <select name="custo_fornecedor[]" class="form-select form-select-sm forn-select">
                ${fornecedoresOptions}
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="custo_valor[]" class="form-control form-control-sm" placeholder="Valor (R$)" value="${valor}">
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.edit-custo-row').remove()">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);

    // Setar o valor do select de fornecedor corretamente se houver
    if (tipo === 'fornecedor' && fornId) {
        row.querySelector('.forn-select').value = fornId;
    }
}

function toggleEditCustoFornecedor(selectElem) {
    var row = selectElem.closest('.edit-custo-row');
    var fornContainer = row.querySelector('.edit-custo-forn-container');
    if(selectElem.value === 'fornecedor') {
        fornContainer.style.display = 'block';
    } else {
        fornContainer.style.display = 'none';
        fornContainer.querySelector('select').value = '';
    }
}

function salvarEdicao(e) {
    e.preventDefault();
    let form = document.getElementById('formEditarItem');
    let btn = document.getElementById('btnSalvarEdit');
    
    let formData = new FormData(form);
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    fetch('<?= BASE_URL ?>/os/atualizar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            window.location.reload();
        } else {
            alert(data.erro || 'Erro ao atualizar.');
            btn.disabled = false;
            btn.innerHTML = 'Salvar';
        }
    })
    .catch(err => {
        alert('Erro de conexão ao salvar.');
        btn.disabled = false;
        btn.innerHTML = 'Salvar';
    });
}
</script>
