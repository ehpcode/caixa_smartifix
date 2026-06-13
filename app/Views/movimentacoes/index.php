<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-end align-items-center">
                <?php if ($caixaAberto): ?>
                <div>
                    <button class="btn btn-success btn-sm fw-bold me-1" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                        <i class="fa-solid fa-arrow-down me-1"></i>Nova Entrada
                    </button>
                    <button class="btn btn-danger btn-sm fw-bold me-1" data-bs-toggle="modal" data-bs-target="#modalSaida">
                        <i class="fa-solid fa-arrow-up me-1"></i>Nova Saída
                    </button>
                    <button class="btn btn-secondary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalSangria">
                        <i class="fa-solid fa-money-bill-transfer me-1"></i>Sangria
                    </button>
                </div>
                <?php else: ?>
                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-lock me-1"></i>Caixa Fechado</span>
                <?php endif; ?>
            </div>
            
            <div class="card-body bg-light border-bottom">
                <form method="GET" action="<?= BASE_URL ?>/movimentacoes" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Data Início</label>
                        <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Data Fim</label>
                        <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos</option>
                            <option value="entrada" <?= $filtros['tipo'] == 'entrada' ? 'selected' : '' ?>>Entrada</option>
                            <option value="saida" <?= $filtros['tipo'] == 'saida' ? 'selected' : '' ?>>Saída</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Conta Financeira</label>
                        <select name="conta" class="form-select">
                            <option value="">Todas as Contas</option>
                            <?php foreach($contas as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $filtros['conta'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Data/Hora</th>
                                <th>Tipo</th>
                                <th class="text-start">Descrição</th>
                                <th>Conta</th>
                                <th>Forma Pgto.</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($movimentacoes)): ?>
                                <tr><td colspan="7" class="py-5 text-muted">Nenhum registro encontrado com estes filtros.</td></tr>
                            <?php else: ?>
                                <?php foreach($movimentacoes as $m): ?>
                                <tr>
                                    <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($m['data_movimentacao'])) ?></td>
                                    <td>
                                        <?php if($m['tipo'] == 'entrada'): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-arrow-down"></i> Entrada</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fa-solid fa-arrow-up"></i> Saída</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-start fw-bold">
                                        <?= htmlspecialchars($m['descricao']) ?>
                                        <?php if(!empty($m['numero_os'])): ?>
                                            <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">OS #<?= htmlspecialchars($m['numero_os']) ?></span>
                                        <?php endif; ?>
                                        <?php if(($m['categoria_base'] ?? '') === 'sangria'): ?>
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">SANGRIA</span>
                                        <?php elseif(!empty($m['categoria_base'])): ?>
                                            <span class="badge bg-light text-dark border ms-1" style="font-size: 0.65rem;"><?= htmlspecialchars(strtoupper($m['categoria_base'])) ?></span>
                                        <?php endif; ?>
                                        <?php if($m['eh_parcela']): ?>
                                            <span class="badge bg-info ms-1" style="font-size: 0.65rem;">PARCELA</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small><?= htmlspecialchars($m['conta_nome']) ?></small></td>
                                    <td><small><?= htmlspecialchars($m['forma_nome']) ?></small></td>
                                    <td class="fw-bold <?= $m['tipo'] == 'entrada' ? 'text-success' : 'text-danger' ?>">
                                        <?= $m['tipo'] == 'entrada' ? '+' : '-' ?> R$ <?= number_format($m['valor'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" 
                                            onclick="verDetalhes(
                                                '<?= date('d/m/Y H:i', strtotime($m['data_movimentacao'])) ?>',
                                                '<?= $m['tipo'] ?>',
                                                '<?= htmlspecialchars($m['descricao']) ?>',
                                                '<?= htmlspecialchars($m['conta_nome']) ?>',
                                                '<?= htmlspecialchars($m['natureza_nome']) ?>',
                                                '<?= htmlspecialchars($m['forma_nome']) ?>',
                                                '<?= number_format($m['valor'], 2, ',', '.') ?>',
                                                '<?= htmlspecialchars($m['funcionario_nome'] ?? 'Não informado') ?>',
                                                '<?= htmlspecialchars($m['usuario_nome'] ?? 'Sistema') ?>'
                                            )">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <?php 
                                        $podeEditar = false;
                                        if (!empty($_SESSION['permissoes']['todas']) || !empty($_SESSION['permissoes']['movimentacao:editar'])) {
                                            $podeEditar = true;
                                        }
                                        if ($podeEditar && $caixaAberto && $m['caixa_id'] == $caixaAberto['id']): 
                                        ?>
                                            <button class="btn btn-sm btn-outline-warning ms-1" onclick="editarMovimentacao(<?= $m['id'] ?>)">
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

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fa-solid fa-circle-info me-2"></i>Detalhes da Movimentação</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr><td class="text-muted text-end w-50">Data/Hora:</td><td class="fw-bold" id="detData"></td></tr>
                        <tr><td class="text-muted text-end">Tipo:</td><td class="fw-bold" id="detTipo"></td></tr>
                        <tr><td class="text-muted text-end">Descrição:</td><td class="fw-bold" id="detDesc"></td></tr>
                        <tr><td class="text-muted text-end">Conta:</td><td class="fw-bold" id="detConta"></td></tr>
                        <tr><td class="text-muted text-end">Natureza:</td><td class="fw-bold" id="detNat"></td></tr>
                        <tr><td class="text-muted text-end">Forma de Pgto:</td><td class="fw-bold" id="detForma"></td></tr>
                        <tr><td class="text-muted text-end">Funcionário/Técnico:</td><td class="fw-bold" id="detFunc"></td></tr>
                        <tr><td class="text-muted text-end">Criado por:</td><td class="fw-bold" id="detCriadoPor"></td></tr>
                        <tr><td class="text-muted text-end">Valor:</td><td class="fw-bold fs-5" id="detValor"></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php 
if ($caixaAberto) {
    include __DIR__ . '/../partials/modais_movimentacao.php';
}
?>

<script>
function verDetalhes(data, tipo, desc, conta, nat, forma, valor, func, criado_por) {
    document.getElementById('detData').innerText = data;
    
    let elTipo = document.getElementById('detTipo');
    if(tipo == 'entrada') {
        elTipo.innerHTML = '<span class="text-success"><i class="fa-solid fa-arrow-down"></i> Entrada</span>';
        document.getElementById('detValor').className = 'fw-bold fs-5 text-success';
        document.getElementById('detValor').innerText = '+ R$ ' + valor;
    } else {
        elTipo.innerHTML = '<span class="text-danger"><i class="fa-solid fa-arrow-up"></i> Saída</span>';
        document.getElementById('detValor').className = 'fw-bold fs-5 text-danger';
        document.getElementById('detValor').innerText = '- R$ ' + valor;
    }

    document.getElementById('detDesc').innerText = desc;
    document.getElementById('detConta').innerText = conta;
    document.getElementById('detNat').innerText = nat;
    document.getElementById('detForma').innerText = forma;
    document.getElementById('detFunc').innerText = func;
    document.getElementById('detCriadoPor').innerText = criado_por;

    var modal = new bootstrap.Modal(document.getElementById('modalDetalhes'));
    modal.show();
}
</script>
