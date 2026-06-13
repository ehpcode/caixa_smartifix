<!-- Modais Entrada, Saída, Sangria (somente se caixa aberto) -->
<?php if ($caixaAberto): ?>

<!-- Modal Nova Entrada -->
<div class="modal fade" id="modalEntrada" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= BASE_URL ?>/movimentacoes/salvar" method="POST" class="modal-content">
            <input type="hidden" name="tipo_movimento" value="entrada">
            <input type="hidden" name="caixa_id" value="<?= $caixaAberto['id'] ?>">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa-solid fa-arrow-down me-2"></i>Nova Entrada</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Entrada</label>
                        <select name="item_tipo" id="entradaItemTipo" class="form-select" onchange="toggleEntradaFields()">
                            <option value="simples">Entrada Simples</option>
                            <option value="os">Serviço/Reparo (OS)</option>
                            <option value="venda">Venda</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Conta Financeira</label>
                        <select name="conta_financeira_id" class="form-select" required>
                            <?php foreach($contas as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Natureza</label>
                        <select name="natureza_financeira_id" id="entradaNatureza" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach($naturezas as $n): if($n['tipo'] == 'entrada' || empty($n['tipo'])): ?>
                                <option value="<?= $n['id'] ?>" data-categoria="<?= htmlspecialchars($n['categoria_base'] ?? '') ?>"><?= htmlspecialchars($n['nome']) ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <select name="forma_pagamento_id" class="form-select" required>
                            <?php foreach($formas as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Valor (R$)</label>
                        <input type="text" name="valor" id="entradaValor" class="form-control" placeholder="R$ 0,00" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Funcionário (Vendedor/Técnico)</label>
                        <select name="funcionario_id" class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach($funcionarios as $fu): ?>
                                <option value="<?= $fu['id'] ?>"><?= htmlspecialchars($fu['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="eh_parcela" id="eh_parcela">
                            <label class="form-check-label" for="eh_parcela">É um recebimento parcelado?</label>
                        </div>
                    </div>
                </div>

                <!-- Sessão Específica Item (OS, Venda, Avulso) -->
                <div id="itemFields" style="display:none;" class="bg-light p-3 border rounded mb-3">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Detalhes</h6>
                    <div class="row mb-2">
                        <div class="col-md-6" id="fieldNumeroOS">
                            <label class="form-label">Nº OS Externa</label>
                            <select name="numero_os" id="entradaNumeroOS" class="form-select" style="width: 100%;">
                                <option value="">Selecione ou digite...</option>
                                <?php if(isset($os_cadastradas)): foreach($os_cadastradas as $os): ?>
                                    <option value="<?= htmlspecialchars($os['numero_os']) ?>" data-exists="true"><?= htmlspecialchars($os['numero_os']) ?> - <?= htmlspecialchars($os['cliente']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="fieldVendaExistente" style="display:none;">
                            <label class="form-label">Venda Existente (Parcela)</label>
                            <select name="venda_id" id="entradaVendaExistente" class="form-select" style="width: 100%;">
                                <option value="">Selecione a venda...</option>
                                <?php if(isset($vendas_cadastradas)): foreach($vendas_cadastradas as $vd): ?>
                                    <option value="<?= $vd['id'] ?>" data-exists="true">Venda #<?= $vd['id'] ?> - <?= htmlspecialchars($vd['cliente'] ?: 'S/N') ?> (<?= htmlspecialchars($vd['item'] ?: 'S/I') ?>)</option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="fieldCliente">
                            <label class="form-label">Cliente</label>
                            <input type="text" name="cliente" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2" id="fieldItem">
                        <label class="form-label">Aparelho / Item</label>
                        <input type="text" name="item" class="form-control">
                    </div>
                    <div class="mb-2" id="fieldValorTotalItem">
                        <label class="form-label">Valor Total (R$)</label>
                        <input type="text" name="valor_total_item" id="entradaValorTotalItem" class="form-control" placeholder="R$ 0,00">
                    </div>

                    <!-- Sessão de Custos -->
                    <div class="mt-3">
                        <label class="form-label fw-bold">Custos Operacionais</label>
                        <div id="custosContainer">
                            <div class="row mb-2 custo-row align-items-center">
                                <div class="col-md-3">
                                    <input type="text" name="custo_descricao[]" class="form-control form-control-sm" placeholder="Ex: Tela Display">
                                </div>
                                <div class="col-md-3">
                                    <select name="custo_tipo[]" class="form-select form-select-sm" onchange="toggleCustoFornecedor(this)">
                                        <option value="estoque">Estoque (Interno)</option>
                                        <option value="fornecedor">Fornecedor (Externo)</option>
                                        <option value="mao_obra">Mão de Obra</option>
                                    </select>
                                </div>
                                <div class="col-md-3 custo-fornecedor-container" style="display:none;">
                                    <select name="custo_fornecedor[]" class="form-select form-select-sm">
                                        <option value="">Selecione o fornecedor...</option>
                                        <?php foreach($fornecedores as $forn): ?>
                                            <option value="<?= $forn['id'] ?>"><?= htmlspecialchars($forn['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="custo_valor[]" class="form-control form-control-sm input-custo-valor" placeholder="R$ 0,00">
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.custo-row').remove()">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addCustoRow()"><i class="fa-solid fa-plus"></i> Adicionar Custo</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold">Salvar Entrada</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nova Saída -->
<div class="modal fade modal-lg" id="modalSaida" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/movimentacoes/salvar" method="POST" class="modal-content">
            <input type="hidden" name="tipo_movimento" value="saida">
            <input type="hidden" name="caixa_id" value="<?= $caixaAberto['id'] ?>">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa-solid fa-arrow-up me-2"></i>Nova Saída</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Conta Financeira</label>
                    <select name="conta_financeira_id" class="form-select" required>
                        <?php foreach($contas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Natureza</label>
                        <select name="natureza_financeira_id" class="form-select" required>
                            <?php foreach($naturezas as $n): if(($n['tipo'] == 'saida' || empty($n['tipo'])) && ($n['categoria_base'] ?? '') !== 'sangria'): ?>
                                <option value="<?= $n['id'] ?>"><?= htmlspecialchars($n['nome']) ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Forma de Pagamento</label>
                        <select name="forma_pagamento_id" class="form-select" required>
                            <?php foreach($formas as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valor (R$)</label>
                    <input type="text" name="valor" id="saidaValor" class="form-control" placeholder="R$ 0,00" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vincular Despesa a:</label>
                    <select name="item_tipo" id="saidaItemTipo" class="form-select" onchange="toggleSaidaFields()">
                        <option value="simples">Nenhum (Saída Simples)</option>
                        <option value="os">Serviço/Reparo (OS)</option>
                        <option value="venda">Venda</option>
                    </select>
                </div>

                <!-- Sessão Específica Item Saída -->
                <div id="saidaItemFields" style="display:none;" class="bg-light p-3 border rounded mb-3">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Detalhes do Vínculo</h6>
                    <div class="row mb-2">
                        <div class="col-md-6" id="saidaFieldNumeroOS">
                            <label class="form-label">Nº OS Externa</label>
                            <select name="numero_os" id="saidaNumeroOS" class="form-select" style="width: 100%;">
                                <option value="">Selecione ou digite...</option>
                                <?php if(isset($os_cadastradas)): foreach($os_cadastradas as $os): ?>
                                    <option value="<?= htmlspecialchars($os['numero_os']) ?>" data-exists="true"><?= htmlspecialchars($os['numero_os']) ?> - <?= htmlspecialchars($os['cliente']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="saidaFieldVendaExistente" style="display:none;">
                            <label class="form-label">Venda Existente</label>
                            <select name="venda_id" id="saidaVendaExistente" class="form-select" style="width: 100%;">
                                <option value="">Selecione a venda...</option>
                                <?php if(isset($vendas_cadastradas)): foreach($vendas_cadastradas as $vd): ?>
                                    <option value="<?= $vd['id'] ?>" data-exists="true">Venda #<?= $vd['id'] ?> - <?= htmlspecialchars($vd['cliente'] ?: 'S/N') ?> (<?= htmlspecialchars($vd['item'] ?: 'S/I') ?>)</option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="saidaFieldCliente">
                            <label class="form-label">Cliente</label>
                            <input type="text" name="cliente" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2" id="saidaFieldItem">
                        <label class="form-label">Aparelho / Item</label>
                        <input type="text" name="item" class="form-control">
                    </div>
                    <div class="mb-2" id="saidaFieldValorTotalItem">
                        <label class="form-label">Valor Total (R$)</label>
                        <input type="text" name="valor_total_item" id="saidaValorTotalItem" class="form-control" placeholder="R$ 0,00">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Salvar Saída</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sangria -->
<div class="modal fade" id="modalSangria" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/sangria/salvar" method="POST" class="modal-content">
            <input type="hidden" name="caixa_id" value="<?= $caixaAberto['id'] ?>">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-money-bill-transfer me-2"></i>Sangria de Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border border-warning">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><strong>Atenção:</strong> Sangrias representam retiradas do caixa (ex: depósito, cofre) e não são contabilizadas como despesas no resultado do dia.
                </div>
                <div class="mb-3">
                    <label class="form-label text-danger fw-bold"><i class="fa-solid fa-arrow-up me-1"></i>Conta de Origem</label>
                    <select name="conta_origem_id" id="sangriaContaOrigem" class="form-select" required onchange="buscarSaldoSangria()">
                        <option value="">Selecione...</option>
                        <?php foreach($contas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-muted" id="sangriaSaldoText">Saldo disponível: Selecione uma conta.</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-success fw-bold"><i class="fa-solid fa-arrow-down me-1"></i>Conta de Destino</label>
                    <select name="conta_destino_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach($contas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fa-solid fa-credit-card me-1"></i>Forma de Pagamento</label>
                    <select name="forma_pagamento_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach($formas as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Valor (R$)</label>
                        <input type="text" name="valor" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Data e Hora</label>
                        <input type="datetime-local" name="data_movimentacao" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição (Opcional)</label>
                    <input type="text" name="descricao" class="form-control" placeholder="Detalhes adicionais...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-dark fw-bold">Registrar Sangria</button>
            </div>
        </form>
    </div>
</div>

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery e Select2 JS (Necessário para Select2 funcionar adequadamente) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- IMask JS -->
<script src="https://unpkg.com/imask"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar máscaras de dinheiro
    var maskOptions = {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: '.',
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: ',',
        mapToRadix: ['.']
    };
    
    // Campo Valor Principal (Entrada)
    var elValor = document.getElementById('entradaValor');
    if(elValor) IMask(elValor, maskOptions);
    
    // Campo Valor Total Item (OS/Venda)
    var elValorTotal = document.getElementById('entradaValorTotalItem');
    if(elValorTotal) IMask(elValorTotal, maskOptions);

    // Campo Valor Total Item Saída
    var elValorTotalSaida = document.getElementById('saidaValorTotalItem');
    if(elValorTotalSaida) IMask(elValorTotalSaida, maskOptions);

    // Inicializar Select2 na OS
    $('#entradaNumeroOS').select2({
        dropdownParent: $('#modalEntrada'),
        tags: true,
        placeholder: "Selecione ou digite..."
    }).on('change', checkOsVendaExisting);

    // Inicializar Select2 na Venda
    $('#entradaVendaExistente').select2({
        dropdownParent: $('#modalEntrada'),
        placeholder: "Selecione a venda..."
    }).on('change', checkOsVendaExisting);

    // Selects da Saída
    $('#saidaNumeroOS').select2({
        dropdownParent: $('#modalSaida'),
        tags: true,
        placeholder: "Selecione ou digite..."
    }).on('change', checkSaidaOsVendaExisting);

    $('#saidaVendaExistente').select2({
        dropdownParent: $('#modalSaida'),
        placeholder: "Selecione a venda..."
    }).on('change', checkSaidaOsVendaExisting);

    document.getElementById('eh_parcela').addEventListener('change', function() {
        toggleEntradaFields();
    });

    // Chamar a função de toggle para inicializar o filtro da natureza corretamente ao abrir
    toggleEntradaFields();
    toggleSaidaFields();
});

function checkSaidaOsVendaExisting() {
    var tipo = document.getElementById('saidaItemTipo').value;
    var fieldCliente = document.getElementById('saidaFieldCliente');
    var fieldItem = document.getElementById('saidaFieldItem');
    var fieldValorTotalItem = document.getElementById('saidaFieldValorTotalItem');
    
    var exists = false;
    
    if (tipo === 'os') {
        var selectedOpt = $('#saidaNumeroOS').find('option:selected');
        exists = selectedOpt.data('exists') === true;
    } else if (tipo === 'venda') {
        var selectedOpt = $('#saidaVendaExistente').find('option:selected');
        exists = selectedOpt.data('exists') === true;
    }
    
    if (exists) {
        if(fieldCliente) fieldCliente.style.display = 'none';
        if(fieldItem) fieldItem.style.display = 'none';
        if (fieldValorTotalItem) fieldValorTotalItem.style.display = 'none';
    } else {
        if(fieldCliente) fieldCliente.style.display = 'block';
        if(fieldItem) fieldItem.style.display = 'block';
        if (fieldValorTotalItem) fieldValorTotalItem.style.display = 'block';
    }
}

function checkOsVendaExisting() {
    var tipo = document.getElementById('entradaItemTipo').value;
    var fieldCliente = document.getElementById('fieldCliente');
    var fieldItem = document.getElementById('fieldItem');
    var fieldValorTotalItem = document.getElementById('fieldValorTotalItem');
    
    var exists = false;
    
    if (tipo === 'os') {
        var selectedOpt = $('#entradaNumeroOS').find('option:selected');
        exists = selectedOpt.data('exists') === true;
    } else if (tipo === 'venda') {
        var isParcela = document.getElementById('eh_parcela').checked;
        if (isParcela) {
            var selectedOpt = $('#entradaVendaExistente').find('option:selected');
            exists = selectedOpt.data('exists') === true;
        }
    }
    
    if (exists) {
        fieldCliente.style.display = 'none';
        fieldItem.style.display = 'none';
        if (fieldValorTotalItem) fieldValorTotalItem.style.display = 'none';
    } else {
        fieldCliente.style.display = 'block';
        fieldItem.style.display = 'block';
        if (fieldValorTotalItem) fieldValorTotalItem.style.display = 'block';
    }
}

// Helper function to apply mask to new dynamically added elements
function applyCurrencyMask(element) {
    IMask(element, {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: '.',
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: ',',
        mapToRadix: ['.']
    });
}

function toggleEntradaFields() {
    var tipo = document.getElementById('entradaItemTipo').value;
    var isParcela = document.getElementById('eh_parcela').checked;
    var container = document.getElementById('itemFields');
    var numOs = document.getElementById('fieldNumeroOS');
    var fieldVendaExistente = document.getElementById('fieldVendaExistente');
    
    // Lógica para esconder/mostrar as opções do select de Natureza Financeira
    var selectNatureza = document.getElementById('entradaNatureza');
    var options = selectNatureza.querySelectorAll('option');
    var temOpcaoValida = false;
    
    options.forEach(opt => {
        if(opt.value === "") return; // Option "Selecione..." sempre visível/disponível se não tiver selection, mas escondida abaixo.
        var cat = opt.getAttribute('data-categoria');
        
        if(tipo === 'os') {
            opt.style.display = (cat === 'servico') ? '' : 'none';
        } else if(tipo === 'venda') {
            opt.style.display = (cat === 'venda') ? '' : 'none';
        } else if(tipo === 'simples') {
            opt.style.display = (cat !== 'servico' && cat !== 'venda' && cat !== 'sangria') ? '' : 'none';
        } else {
            opt.style.display = '';
        }
        
        // Verifica se o valor atual selecionado ficou invisível
        if(opt.selected && opt.style.display === 'none') {
            selectNatureza.value = ''; // reseta
        }
    });

    if (tipo === 'simples') {
        container.style.display = 'none';
    } else {
        container.style.display = 'block';
        
        if (tipo === 'os') {
            numOs.style.display = 'block';
            fieldVendaExistente.style.display = 'none';
            checkOsVendaExisting();
        } else if (tipo === 'venda') {
            numOs.style.display = 'none';
            if (isParcela) {
                fieldVendaExistente.style.display = 'block';
            } else {
                fieldVendaExistente.style.display = 'none';
            }
            checkOsVendaExisting();
        } else {
            numOs.style.display = 'none';
            fieldVendaExistente.style.display = 'none';
            checkOsVendaExisting();
        }
    }
}

function toggleSaidaFields() {
    var tipo = document.getElementById('saidaItemTipo').value;
    var container = document.getElementById('saidaItemFields');
    var numOs = document.getElementById('saidaFieldNumeroOS');
    var fieldVendaExistente = document.getElementById('saidaFieldVendaExistente');

    if (tipo === 'simples') {
        container.style.display = 'none';
    } else {
        container.style.display = 'block';
        
        if (tipo === 'os') {
            numOs.style.display = 'block';
            fieldVendaExistente.style.display = 'none';
            checkSaidaOsVendaExisting();
        } else if (tipo === 'venda') {
            numOs.style.display = 'none';
            fieldVendaExistente.style.display = 'block';
            checkSaidaOsVendaExisting();
        } else {
            numOs.style.display = 'none';
            fieldVendaExistente.style.display = 'none';
            checkSaidaOsVendaExisting();
        }
    }
}

function toggleCustoFornecedor(selectElem) {
    var row = selectElem.closest('.custo-row');
    var fornContainer = row.querySelector('.custo-fornecedor-container');
    if(selectElem.value === 'fornecedor') {
        fornContainer.style.display = 'block';
    } else {
        fornContainer.style.display = 'none';
        fornContainer.querySelector('select').value = '';
    }
}

function addCustoRow() {
    var container = document.getElementById('custosContainer');
    var row = document.createElement('div');
    row.className = 'row mb-2 custo-row align-items-center';
    row.innerHTML = `
        <div class="col-md-3">
            <input type="text" name="custo_descricao[]" class="form-control form-control-sm" placeholder="Ex: Tela Display">
        </div>
        <div class="col-md-3">
            <select name="custo_tipo[]" class="form-select form-select-sm" onchange="toggleCustoFornecedor(this)">
                <option value="estoque">Estoque (Interno)</option>
                <option value="fornecedor">Fornecedor (Externo)</option>
                <option value="mao_obra">Mão de Obra</option>
            </select>
        </div>
        <div class="col-md-3 custo-fornecedor-container" style="display:none;">
            <select name="custo_fornecedor[]" class="form-select form-select-sm">
                <option value="">Selecione o fornecedor...</option>
                <?php foreach($fornecedores as $forn): ?>
                    <option value="<?= $forn['id'] ?>"><?= htmlspecialchars($forn['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="custo_valor[]" class="form-control form-control-sm input-custo-valor-dyn" placeholder="R$ 0,00">
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.custo-row').remove()">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    
    // Aplicar a máscara no novo input de custo
    applyCurrencyMask(row.querySelector('.input-custo-valor-dyn'));
}

function buscarSaldoSangria() {
    var contaId = document.getElementById('sangriaContaOrigem').value;
    var textoSaldo = document.getElementById('sangriaSaldoText');
    
    if (!contaId) {
        textoSaldo.innerHTML = 'Saldo disponível: Selecione uma conta.';
        return;
    }
    
    textoSaldo.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Buscando saldo...';
    
    fetch('<?= BASE_URL ?>/caixa/saldo-conta?conta_id=' + contaId)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                textoSaldo.innerHTML = '<span class="text-danger">Erro ao buscar saldo</span>';
            } else {
                let formatter = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
                textoSaldo.innerHTML = 'Saldo disponível: <strong>' + formatter.format(data.saldo) + '</strong>';
            }
        })
        .catch(error => {
            textoSaldo.innerHTML = '<span class="text-danger">Erro de conexão</span>';
        });
}
</script>




<!-- Modal Editar Entrada -->

<div class="modal fade" id="modalEditarEntrada" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= BASE_URL ?>/movimentacoes/update" method="POST" class="modal-content">
            <input type="hidden" name="tipo_movimento" value="entrada">
            <input type="hidden" name="id" id="editEntradaId">
            <input type="hidden" name="caixa_id" value="<?= $caixaAberto['id'] ?>">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa-solid fa-arrow-down me-2"></i>Editar Entrada</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Entrada</label>
                        <select name="item_tipo" id="editEntradaItemTipo" class="form-select" onchange="toggleEditEntradaFields()">
                            <option value="simples">Entrada Simples</option>
                            <option value="os">Serviço/Reparo (OS)</option>
                            <option value="venda">Venda</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Conta Financeira</label>
                        <select name="conta_financeira_id" id="editEntradaConta" class="form-select" required>
                            <?php foreach($contas as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Natureza</label>
                        <select name="natureza_financeira_id" id="editEntradaNatureza" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach($naturezas as $n): if($n['tipo'] == 'entrada' || empty($n['tipo'])): ?>
                                <option value="<?= $n['id'] ?>" data-categoria="<?= htmlspecialchars($n['categoria_base'] ?? '') ?>"><?= htmlspecialchars($n['nome']) ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <select name="forma_pagamento_id" id="editEntradaForma" class="form-select" required>
                            <?php foreach($formas as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Valor (R$)</label>
                        <input type="text" name="valor" id="editEntradaValor" class="form-control" placeholder="R$ 0,00" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" id="editEntradaDescricao" class="form-control" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Funcionário (Vendedor/Técnico)</label>
                        <select name="funcionario_id" id="editEntradaFuncionario" class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach($funcionarios as $fu): ?>
                                <option value="<?= $fu['id'] ?>"><?= htmlspecialchars($fu['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="eh_parcela" id="edit_entrada_eh_parcela">
                            <label class="form-check-label" for="edit_entrada_eh_parcela">É um recebimento parcelado?</label>
                        </div>
                    </div>
                </div>

                <!-- Sessão Específica Item (OS, Venda, Avulso) -->
                <div id="editEntradaItemFields" style="display:none;" class="bg-light p-3 border rounded mb-3">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Detalhes</h6>
                    <div class="row mb-2">
                        <div class="col-md-6" id="editEntradaFieldNumeroOS">
                            <label class="form-label">Nº OS Externa</label>
                            <select name="numero_os" id="editEntradaNumeroOS" class="form-select" style="width: 100%;">
                                <option value="">Selecione ou digite...</option>
                                <?php if(isset($os_cadastradas)): foreach($os_cadastradas as $os): ?>
                                    <option value="<?= htmlspecialchars($os['numero_os']) ?>" data-exists="true"><?= htmlspecialchars($os['numero_os']) ?> - <?= htmlspecialchars($os['cliente']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="editEntradaFieldVendaExistente" style="display:none;">
                            <label class="form-label">Venda Existente (Parcela)</label>
                            <select name="venda_id" id="editEntradaVendaExistente" class="form-select" style="width: 100%;">
                                <option value="">Selecione a venda...</option>
                                <?php if(isset($vendas_cadastradas)): foreach($vendas_cadastradas as $vd): ?>
                                    <option value="<?= $vd['id'] ?>" data-exists="true">Venda #<?= $vd['id'] ?> - <?= htmlspecialchars($vd['cliente'] ?: 'S/N') ?> (<?= htmlspecialchars($vd['item'] ?: 'S/I') ?>)</option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="editEntradaFieldCliente">
                            <label class="form-label">Cliente</label>
                            <input type="text" name="cliente" id="editEntradaCliente" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2" id="editEntradaFieldItem">
                        <label class="form-label">Aparelho / Item</label>
                        <input type="text" name="item" id="editEntradaItem" class="form-control">
                    </div>
                    <div class="mb-2" id="editEntradaFieldValorTotalItem">
                        <label class="form-label">Valor Total (R$)</label>
                        <input type="text" name="valor_total_item" id="editEntradaValorTotalItem" class="form-control" placeholder="R$ 0,00">
                    </div>

                    <!-- Sessão de Custos -->
                    <div class="mt-3">
                        <label class="form-label fw-bold">Custos Operacionais</label>
                        <div id="editEntradaCustosContainer">
                            <div class="row mb-2 custo-row align-items-center">
                                <div class="col-md-3">
                                    <input type="text" name="custo_descricao[]" class="form-control form-control-sm" placeholder="Ex: Tela Display">
                                </div>
                                <div class="col-md-3">
                                    <select name="custo_tipo[]" class="form-select form-select-sm" onchange="toggleCustoFornecedor(this)">
                                        <option value="estoque">Estoque (Interno)</option>
                                        <option value="fornecedor">Fornecedor (Externo)</option>
                                        <option value="mao_obra">Mão de Obra</option>
                                    </select>
                                </div>
                                <div class="col-md-3 custo-fornecedor-container" style="display:none;">
                                    <select name="custo_fornecedor[]" class="form-select form-select-sm">
                                        <option value="">Selecione o fornecedor...</option>
                                        <?php foreach($fornecedores as $forn): ?>
                                            <option value="<?= $forn['id'] ?>"><?= htmlspecialchars($forn['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="custo_valor[]" class="form-control form-control-sm input-custo-valor" placeholder="R$ 0,00">
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.custo-row').remove()">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addEditEntradaCustoRow()"><i class="fa-solid fa-plus"></i> Adicionar Custo</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold">Salvar Entrada</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Editar Saida -->

<div class="modal fade modal-lg" id="modalEditarSaida" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/movimentacoes/update" method="POST" class="modal-content">
            <input type="hidden" name="tipo_movimento" value="saida">
            <input type="hidden" name="id" id="editSaidaId">
            <input type="hidden" name="caixa_id" value="<?= $caixaAberto['id'] ?>">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa-solid fa-arrow-up me-2"></i>Editar Saída</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Conta Financeira</label>
                    <select name="conta_financeira_id" id="editSaidaConta" class="form-select" required>
                        <?php foreach($contas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Natureza</label>
                        <select name="natureza_financeira_id" id="editSaidaNatureza" class="form-select" required>
                            <?php foreach($naturezas as $n): if(($n['tipo'] == 'saida' || empty($n['tipo'])) && ($n['categoria_base'] ?? '') !== 'sangria'): ?>
                                <option value="<?= $n['id'] ?>"><?= htmlspecialchars($n['nome']) ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Forma de Pagamento</label>
                        <select name="forma_pagamento_id" id="editSaidaForma" class="form-select" required>
                            <?php foreach($formas as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valor (R$)</label>
                    <input type="text" name="valor" id="editSaidaValor" class="form-control" placeholder="R$ 0,00" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" id="editSaidaDescricao" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vincular Despesa a:</label>
                    <select name="item_tipo" id="editSaidaItemTipo" class="form-select" onchange="toggleEditSaidaFields()">
                        <option value="simples">Nenhum (Saída Simples)</option>
                        <option value="os">Serviço/Reparo (OS)</option>
                        <option value="venda">Venda</option>
                    </select>
                </div>

                <!-- Sessão Específica Item Saída -->
                <div id="editSaidaItemFields" style="display:none;" class="bg-light p-3 border rounded mb-3">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Detalhes do Vínculo</h6>
                    <div class="row mb-2">
                        <div class="col-md-6" id="editSaidaFieldNumeroOS">
                            <label class="form-label">Nº OS Externa</label>
                            <select name="numero_os" id="editSaidaNumeroOS" class="form-select" style="width: 100%;">
                                <option value="">Selecione ou digite...</option>
                                <?php if(isset($os_cadastradas)): foreach($os_cadastradas as $os): ?>
                                    <option value="<?= htmlspecialchars($os['numero_os']) ?>" data-exists="true"><?= htmlspecialchars($os['numero_os']) ?> - <?= htmlspecialchars($os['cliente']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="editSaidaFieldVendaExistente" style="display:none;">
                            <label class="form-label">Venda Existente</label>
                            <select name="venda_id" id="editSaidaVendaExistente" class="form-select" style="width: 100%;">
                                <option value="">Selecione a venda...</option>
                                <?php if(isset($vendas_cadastradas)): foreach($vendas_cadastradas as $vd): ?>
                                    <option value="<?= $vd['id'] ?>" data-exists="true">Venda #<?= $vd['id'] ?> - <?= htmlspecialchars($vd['cliente'] ?: 'S/N') ?> (<?= htmlspecialchars($vd['item'] ?: 'S/I') ?>)</option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6" id="editSaidaFieldCliente">
                            <label class="form-label">Cliente</label>
                            <input type="text" name="cliente" id="editSaidaCliente" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2" id="editSaidaFieldItem">
                        <label class="form-label">Aparelho / Item</label>
                        <input type="text" name="item" id="editSaidaItem" class="form-control">
                    </div>
                    <div class="mb-2" id="editSaidaFieldValorTotalItem">
                        <label class="form-label">Valor Total (R$)</label>
                        <input type="text" name="valor_total_item" id="editSaidaValorTotalItem" class="form-control" placeholder="R$ 0,00">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Salvar Saída</button>
            </div>
        </form>
    </div>
</div>


<script>
function toggleEditEntradaFields() {
    let t = document.getElementById('editEntradaItemTipo').value;
    let fields = document.getElementById('editEntradaItemFields');
    let fOS = document.getElementById('editEntradaFieldNumeroOS');
    let fVendaExistente = document.getElementById('editEntradaFieldVendaExistente');
    let fCliente = document.getElementById('editEntradaFieldCliente');
    let fItem = document.getElementById('editEntradaFieldItem');
    let fValorTot = document.getElementById('editEntradaFieldValorTotalItem');
    let ehParcela = document.getElementById('edit_entrada_eh_parcela').checked;
    
    if(t == 'simples') {
        fields.style.display = 'none';
    } else {
        fields.style.display = 'block';
        if(t == 'os') {
            fOS.style.display = 'block';
            fVendaExistente.style.display = 'none';
            fCliente.style.display = 'block';
            fItem.style.display = 'block';
            fValorTot.style.display = 'block';
        } else if(t == 'venda') {
            fOS.style.display = 'none';
            if (ehParcela) {
                fVendaExistente.style.display = 'block';
                fCliente.style.display = 'none';
                fItem.style.display = 'none';
                fValorTot.style.display = 'none';
            } else {
                fVendaExistente.style.display = 'none';
                fCliente.style.display = 'block';
                fItem.style.display = 'block';
                fValorTot.style.display = 'block';
            }
        }
    }
}

document.getElementById('edit_entrada_eh_parcela').addEventListener('change', toggleEditEntradaFields);

function toggleEditSaidaFields() {
    let t = document.getElementById('editSaidaItemTipo').value;
    let fields = document.getElementById('editSaidaItemFields');
    let fOS = document.getElementById('editSaidaFieldNumeroOS');
    let fVendaExistente = document.getElementById('editSaidaFieldVendaExistente');
    let fCliente = document.getElementById('editSaidaFieldCliente');
    let fItem = document.getElementById('editSaidaFieldItem');
    let fValorTot = document.getElementById('editSaidaFieldValorTotalItem');
    
    if(t == 'simples') {
        fields.style.display = 'none';
    } else {
        fields.style.display = 'block';
        if(t == 'os') {
            fOS.style.display = 'block';
            fVendaExistente.style.display = 'none';
            fCliente.style.display = 'block';
            fItem.style.display = 'block';
            fValorTot.style.display = 'block';
        } else if(t == 'venda') {
            fOS.style.display = 'none';
            fVendaExistente.style.display = 'block';
            fCliente.style.display = 'none';
            fItem.style.display = 'none';
            fValorTot.style.display = 'none';
        }
    }
}

function editarMovimentacao(id) {
    fetch('<?= BASE_URL ?>/movimentacoes/buscar?id=' + id)
    .then(res => res.json())
    .then(data => {
        if(data.erro) {
            alert(data.erro);
            return;
        }
        
        let prefix = data.tipo === 'entrada' ? 'editEntrada' : 'editSaida';
        
        document.getElementById(prefix + 'Id').value = data.id;
        
        document.getElementById(prefix + 'Natureza').value = data.natureza_financeira_id;
        document.getElementById(prefix + 'Conta').value = data.conta_financeira_id;
        document.getElementById(prefix + 'Forma').value = data.forma_pagamento_id;
        document.getElementById(prefix + 'Valor').value = parseFloat(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        document.getElementById(prefix + 'Descricao').value = data.descricao;
        
        if (document.getElementById(prefix + 'Funcionario')) {
            document.getElementById(prefix + 'Funcionario').value = data.funcionario_id || '';
        }
        
        if (document.getElementById('edit_' + data.tipo + '_eh_parcela')) {
            document.getElementById('edit_' + data.tipo + '_eh_parcela').checked = data.eh_parcela == 1;
        }
        
        if(data.item_movimentacao_id) {
            document.getElementById(prefix + 'ItemTipo').value = data.item_tipo;
            if(document.getElementById(prefix + 'NumeroOS')) {
                $('#' + prefix + 'NumeroOS').val(data.numero_os || '').trigger('change');
            }
            if(document.getElementById(prefix + 'Cliente')) document.getElementById(prefix + 'Cliente').value = data.cliente || '';
            if(document.getElementById(prefix + 'Item')) document.getElementById(prefix + 'Item').value = data.item || '';
            if(document.getElementById(prefix + 'ValorTotalItem')) {
                if(data.valor_total) {
                    document.getElementById(prefix + 'ValorTotalItem').value = parseFloat(data.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                } else {
                    document.getElementById(prefix + 'ValorTotalItem').value = '';
                }
            }
            if(data.item_tipo == 'venda' && document.getElementById(prefix + 'VendaExistente')) {
                $('#' + prefix + 'VendaExistente').val(data.item_movimentacao_id).trigger('change');
            }
            
            // Popula custos (apenas Entrada)
            if (data.tipo === 'entrada') {
                let editCustosContainer = document.getElementById('editEntradaCustosContainer');
                editCustosContainer.innerHTML = '';
                if (data.custos && data.custos.length > 0) {
                    data.custos.forEach(custo => {
                        addEditEntradaCustoRow(custo);
                    });
                }
            }
        } else {
            document.getElementById(prefix + 'ItemTipo').value = 'simples';
        }
        
        if (data.tipo === 'entrada') {
            toggleEditEntradaFields();
            new bootstrap.Modal(document.getElementById('modalEditarEntrada')).show();
        } else {
            toggleEditSaidaFields();
            new bootstrap.Modal(document.getElementById('modalEditarSaida')).show();
        }
    })
    .catch(e => {
        console.error(e);
        alert('Erro ao carregar dados da movimentação.');
    });
}

function addEditEntradaCustoRow(custo = null) {
    const container = document.getElementById('editEntradaCustosContainer');
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
    
    // Seta fornecedor select if needed
    if (tipo === 'fornecedor' && forn) {
        row.querySelector('select[name="custo_fornecedor[]"]').value = forn;
    }

    // Mask for new input
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

document.addEventListener("DOMContentLoaded", function() {
    $('#editEntradaNumeroOS').select2({
        dropdownParent: $('#modalEditarEntrada'),
        tags: true
    });
    $('#editEntradaVendaExistente').select2({
        dropdownParent: $('#modalEditarEntrada')
    });
    $('#editSaidaNumeroOS').select2({
        dropdownParent: $('#modalEditarSaida'),
        tags: true
    });
    $('#editSaidaVendaExistente').select2({
        dropdownParent: $('#modalEditarSaida')
    });
});
</script>
<?php endif; ?>
