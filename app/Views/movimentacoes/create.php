<div class="row">
    <div class="col-md-9 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                <h5 class="fw-bold text-secondary">Registrar Lançamento Financeiro</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/movimentacoes/salvar" method="POST">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Ação da Movimentação</label>
                            <select name="tipo_item" id="tipo_item" class="form-select border-2 fw-bold text-purple" onchange="toggleOSFields()">
                                <option value="avulso">Serviço/Movimentação Avulso</option>
                                <option value="venda">Venda de Produto</option>
                                <option value="os">Ordem de Serviço (OS)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Tipo Financeiro</label>
                            <select name="tipo" id="tipo" class="form-select border-2" required onchange="toggleNaturezas()">
                                <option value="entrada">Entrada (Receita)</option>
                                <option value="saida">Saída (Despesa)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Valor (R$)</label>
                            <input type="text" name="valor" class="form-control border-2 fw-bold" placeholder="0,00" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Descrição / Observação</label>
                        <input type="text" name="descricao" class="form-control border-2" placeholder="Ex: Pagamento de Conserto" required>
                    </div>

                    <!-- CAIXA DE OS / VENDA OCULTA -->
                    <div id="box-os-fields" class="p-3 mb-4 rounded border border-purple" style="display:none; background-color: #fdfaff;">
                        <h6 class="fw-bold text-purple mb-3">Dados da Ordem de Serviço ou Venda</h6>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label text-muted fs-7">Nº da OS / Ref</label>
                                <input type="text" name="numero_os" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label text-muted fs-7">Nome do Cliente</label>
                                <input type="text" name="cliente" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label text-muted fs-7">Item / Aparelho</label>
                                <input type="text" name="item_aparelho" class="form-control form-control-sm">
                            </div>
                        </div>

                        <hr class="my-3 opacity-25">
                        <h6 class="fw-bold text-orange mb-3">Custos Operacionais Iniciais (Peças / Adicionais)</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="custo_descricao" class="form-control form-control-sm" placeholder="Descrição do Custo (Ex: Tela Touch)">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="custo_valor" class="form-control form-control-sm text-danger fw-bold" placeholder="Valor do Custo (R$)">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Conta Financeira</label>
                            <select name="conta_financeira_id" class="form-select border-2" required>
                                <option value="">Selecione...</option>
                                <?php foreach($contas as $conta): ?>
                                    <option value="<?= $conta['id'] ?>"><?= htmlspecialchars($conta['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Forma de Pagamento</label>
                            <select name="forma_pagamento_id" class="form-select border-2" required>
                                <option value="">Selecione...</option>
                                <?php foreach($formasPagamento as $forma): ?>
                                    <option value="<?= $forma['id'] ?>"><?= htmlspecialchars($forma['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Natureza</label>
                            
                            <!-- Select Entradas -->
                            <select name="natureza_financeira_id" id="natureza_entrada" class="form-select border-2" required>
                                <option value="">Selecione a natureza...</option>
                                <?php foreach($naturezasEntrada as $nat): ?>
                                    <option value="<?= $nat['id'] ?>"><?= htmlspecialchars($nat['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <!-- Select Saídas (Oculto) -->
                            <select name="natureza_financeira_id" id="natureza_saida" class="form-select border-2" style="display:none;" disabled required>
                                <option value="">Selecione a natureza...</option>
                                <?php foreach($naturezasSaida as $nat): ?>
                                    <option value="<?= $nat['id'] ?>"><?= htmlspecialchars($nat['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="text-end mt-4 border-top pt-4">
                        <a href="<?= BASE_URL ?>/movimentacoes" class="btn btn-light me-2 fw-bold px-4">Cancelar</a>
                        <button type="submit" class="btn btn-orange px-5 fw-bold shadow-sm">Confirmar e Salvar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleNaturezas() {
    const tipo = document.getElementById('tipo').value;
    const selEntrada = document.getElementById('natureza_entrada');
    const selSaida = document.getElementById('natureza_saida');

    if(tipo === 'entrada') {
        selEntrada.style.display = 'block';
        selEntrada.disabled = false;
        selSaida.style.display = 'none';
        selSaida.disabled = true;
    } else {
        selEntrada.style.display = 'none';
        selEntrada.disabled = true;
        selSaida.style.display = 'block';
        selSaida.disabled = false;
    }
}

function toggleOSFields() {
    const tipoItem = document.getElementById('tipo_item').value;
    const boxOS = document.getElementById('box-os-fields');

    if(tipoItem === 'os' || tipoItem === 'venda') {
        boxOS.style.display = 'block';
    } else {
        boxOS.style.display = 'none';
    }
}
</script>
