<?php include __DIR__ . '/_tabs.php'; ?>
<div class="row mb-4">
    <div class="col-md-8">
        <a href="<?= BASE_URL ?>/config" class="btn btn-light border-2 fw-bold btn-sm mb-3 text-secondary">&larr; Voltar às Configurações</a>
        <h4 class="text-secondary fw-bold mb-0">Dados Financeiros</h4>
        <p class="text-muted mt-2">Gerencie as opções que aparecem nos cadastros de movimentações.</p>
    </div>
</div>

<div class="row g-4">
    
    <!-- Contas -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100" id="cardConta">
            <div class="card-header bg-white fw-bold text-orange d-flex justify-content-between align-items-center">
                <span id="titleConta">Contas / Caixas</span>
                <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelarConta" onclick="cancelarEdicaoConta()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body p-3">
                <form action="<?= BASE_URL ?>/config/financeiro/conta/salvar" method="POST" class="mb-3 d-flex gap-2" id="formConta">
                    <input type="hidden" name="id" id="contaId">
                    <input type="text" name="nome" id="contaNome" class="form-control form-control-sm" placeholder="Nova Conta..." required>
                    <select name="tipo" id="contaTipo" class="form-select form-select-sm" style="width:100px;">
                        <option value="caixa_fisico">Físico</option>
                        <option value="conta_bancaria">Banco</option>
                        <option value="carteira_digital">Carteira</option>
                        <option value="outro">Outro</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-orange fw-bold" id="btnSubmitConta">+</button>
                </form>
                <ul class="list-group list-group-flush fs-7">
                    <?php foreach($contas as $c): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="<?= !$c['ativo'] ? 'text-decoration-line-through text-muted' : '' ?>"><?= htmlspecialchars($c['nome']) ?></span>
                            <div>
                                <button class="btn btn-sm text-primary" onclick="editarConta(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['nome'])) ?>', '<?= htmlspecialchars(addslashes($c['tipo'])) ?>')">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="<?= BASE_URL ?>/config/financeiro/conta/toggle/<?= $c['id'] ?>" class="btn btn-sm text-<?= $c['ativo'] ? 'danger' : 'success' ?>">
                                    <i class="fa-solid fa-power-off"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Formas de Pagamento -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100" id="cardForma">
            <div class="card-header bg-white fw-bold text-purple d-flex justify-content-between align-items-center">
                <span id="titleForma">Formas de Pagamento</span>
                <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelarForma" onclick="cancelarEdicaoForma()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body p-3">
                <form action="<?= BASE_URL ?>/config/financeiro/forma/salvar" method="POST" class="mb-3 d-flex gap-2" id="formForma">
                    <input type="hidden" name="id" id="formaId">
                    <input type="text" name="nome" id="formaNome" class="form-control form-control-sm" placeholder="Ex: PicPay, VR..." required>
                    <button type="submit" class="btn btn-sm btn-purple fw-bold text-white" id="btnSubmitForma">+</button>
                </form>
                <ul class="list-group list-group-flush fs-7">
                    <?php foreach($formas as $f): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="<?= !$f['ativo'] ? 'text-decoration-line-through text-muted' : '' ?>"><?= htmlspecialchars($f['nome']) ?></span>
                            <div>
                                <button class="btn btn-sm text-primary" onclick="editarForma(<?= $f['id'] ?>, '<?= htmlspecialchars(addslashes($f['nome'])) ?>')">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="<?= BASE_URL ?>/config/financeiro/forma/toggle/<?= $f['id'] ?>" class="btn btn-sm text-<?= $f['ativo'] ? 'danger' : 'success' ?>">
                                    <i class="fa-solid fa-power-off"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Naturezas -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100" id="cardNatureza">
            <div class="card-header bg-white fw-bold text-success d-flex justify-content-between align-items-center">
                <span id="titleNatureza">Naturezas / Categorias</span>
                <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelarNatureza" onclick="cancelarEdicaoNatureza()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body p-3">
                <form action="<?= BASE_URL ?>/config/financeiro/natureza/salvar" method="POST" class="mb-3 d-flex flex-column gap-2" id="formNatureza">
                    <input type="hidden" name="id" id="naturezaId">
                    <div class="d-flex gap-2">
                        <input type="text" name="nome" id="naturezaNome" class="form-control form-control-sm" placeholder="Nova Categoria..." required>
                        <select name="tipo" id="naturezaTipo" class="form-select form-select-sm" style="width:100px;">
                            <option value="entrada">Receita</option>
                            <option value="saida">Despesa</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <select name="categoria_base" id="naturezaCategoriaBase" class="form-select form-select-sm" required>
                            <option value="venda">Venda</option>
                            <option value="servico">Serviço</option>
                            <option value="despesa_adm">Despesa Adm.</option>
                            <option value="imposto">Imposto</option>
                            <option value="aporte">Aporte</option>
                            <option value="sangria">Sangria</option>
                            <option value="devolucao">Devolução</option>
                            <option value="retirada">Retirada</option>
                            <option value="outro" selected>Outro</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-success fw-bold text-white" id="btnSubmitNatureza">+</button>
                    </div>
                </form>
                <ul class="list-group list-group-flush fs-7" style="max-height: 400px; overflow-y: auto;">
                    <?php foreach($naturezas as $n): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="badge bg-<?= $n['tipo']=='entrada' ? 'success' : 'danger' ?> opacity-50 me-1" style="font-size:0.6rem;"><?= substr($n['tipo'],0,1) ?></span>
                                <span class="<?= !$n['ativo'] ? 'text-decoration-line-through text-muted' : '' ?>"><?= htmlspecialchars($n['nome']) ?></span>
                            </div>
                            <div>
                                <button class="btn btn-sm text-primary" onclick="editarNatureza(<?= $n['id'] ?>, '<?= htmlspecialchars(addslashes($n['nome'])) ?>', '<?= htmlspecialchars(addslashes($n['tipo'])) ?>', '<?= htmlspecialchars(addslashes($n['categoria_base'])) ?>')">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="<?= BASE_URL ?>/config/financeiro/natureza/toggle/<?= $n['id'] ?>" class="btn btn-sm text-<?= $n['ativo'] ? 'danger' : 'success' ?>">
                                    <i class="fa-solid fa-power-off"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

<script>
function editarConta(id, nome, tipo) {
    document.getElementById('contaId').value = id;
    document.getElementById('contaNome').value = nome;
    document.getElementById('contaTipo').value = tipo;
    
    document.getElementById('titleConta').innerHTML = 'Editar Conta';
    document.getElementById('btnSubmitConta').innerHTML = '<i class="fa-solid fa-check"></i>';
    document.getElementById('btnCancelarConta').classList.remove('d-none');
    document.getElementById('cardConta').scrollIntoView({behavior: 'smooth'});
}

function cancelarEdicaoConta() {
    document.getElementById('contaId').value = '';
    document.getElementById('contaNome').value = '';
    document.getElementById('contaTipo').value = 'caixa_fisico';
    
    document.getElementById('titleConta').innerHTML = 'Contas / Caixas';
    document.getElementById('btnSubmitConta').innerHTML = '+';
    document.getElementById('btnCancelarConta').classList.add('d-none');
}

function editarForma(id, nome) {
    document.getElementById('formaId').value = id;
    document.getElementById('formaNome').value = nome;
    
    document.getElementById('titleForma').innerHTML = 'Editar Forma';
    document.getElementById('btnSubmitForma').innerHTML = '<i class="fa-solid fa-check"></i>';
    document.getElementById('btnCancelarForma').classList.remove('d-none');
    document.getElementById('cardForma').scrollIntoView({behavior: 'smooth'});
}

function cancelarEdicaoForma() {
    document.getElementById('formaId').value = '';
    document.getElementById('formaNome').value = '';
    
    document.getElementById('titleForma').innerHTML = 'Formas de Pagamento';
    document.getElementById('btnSubmitForma').innerHTML = '+';
    document.getElementById('btnCancelarForma').classList.add('d-none');
}

function editarNatureza(id, nome, tipo, categoria) {
    document.getElementById('naturezaId').value = id;
    document.getElementById('naturezaNome').value = nome;
    document.getElementById('naturezaTipo').value = tipo;
    document.getElementById('naturezaCategoriaBase').value = categoria || 'outro';
    
    document.getElementById('titleNatureza').innerHTML = 'Editar Natureza';
    document.getElementById('btnSubmitNatureza').innerHTML = '<i class="fa-solid fa-check"></i>';
    document.getElementById('btnCancelarNatureza').classList.remove('d-none');
    document.getElementById('cardNatureza').scrollIntoView({behavior: 'smooth'});
}

function cancelarEdicaoNatureza() {
    document.getElementById('naturezaId').value = '';
    document.getElementById('naturezaNome').value = '';
    document.getElementById('naturezaTipo').value = 'entrada';
    document.getElementById('naturezaCategoriaBase').value = 'outro';
    
    document.getElementById('titleNatureza').innerHTML = 'Naturezas / Categorias';
    document.getElementById('btnSubmitNatureza').innerHTML = '+';
    document.getElementById('btnCancelarNatureza').classList.add('d-none');
}
</script>
    </div>

</div>
