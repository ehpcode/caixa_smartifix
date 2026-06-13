<div class="row">
    <!-- Coluna Esquerda: Status do Caixa -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-cash-register me-2 text-orange"></i>Caixa Diário</h4>
                    <p class="text-muted mb-0 mt-1"><?= date('d/m/Y', strtotime($data_visualizacao)) ?></p>
                </div>
                <?php if ($podeNavegar): ?>
                    <form method="GET" action="<?= BASE_URL ?>/caixa" class="d-flex align-items-center" id="formNavegacaoData">
                        <?php $podeVoltar = strtotime($data_visualizacao) > strtotime($data_primeiro_caixa); ?>
                        <button type="button" class="btn btn-sm btn-light border me-1" onclick="alterarData(-1)" <?= !$podeVoltar ? 'disabled' : '' ?>><i class="fa-solid fa-chevron-left"></i></button>
                        
                        <input type="date" name="data" id="inputDataNavegacao" class="form-control form-control-sm border-0 bg-light fw-bold text-center" style="width: 130px; cursor: pointer;" value="<?= $data_visualizacao ?>" min="<?= $data_primeiro_caixa ?>" max="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                        
                        <?php $podeAvancar = strtotime($data_visualizacao) < strtotime(date('Y-m-d')); ?>
                        <button type="button" class="btn btn-sm btn-light border ms-1" onclick="alterarData(1)" <?= !$podeAvancar ? 'disabled' : '' ?>><i class="fa-solid fa-chevron-right"></i></button>
                    </form>
                    <script>
                        function alterarData(dias) {
                            let input = document.getElementById('inputDataNavegacao');
                            let date = new Date(input.value + 'T12:00:00Z');
                            date.setDate(date.getDate() + dias);
                            
                            let max = input.getAttribute('max');
                            let min = input.getAttribute('min');
                            let strDate = date.toISOString().split('T')[0];

                            if (strDate > max) strDate = max;
                            if (strDate < min) strDate = min;

                            input.value = strDate;
                            input.form.submit();
                        }
                    </script>
                <?php endif; ?>
            </div>
            <div class="card-body text-center py-4">
                <?php if ($caixaAberto): ?>
                    <div class="mb-4">
                        <i class="fa-solid fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-success fw-bold mb-3">Aberto</h3>
                    <div class="text-start bg-light p-3 rounded mb-4">
                        <div class="mb-2"><strong>Status:</strong> <span class="badge bg-success">Aberto</span></div>
                        <div class="mb-2"><strong>Aberto em:</strong> <?= date('d/m/Y H:i', strtotime($caixaAberto['aberto_em'])) ?></div>
                    </div>
                    <button type="button" class="btn btn-danger w-100 fw-bold shadow-sm py-2" data-bs-toggle="modal" data-bs-target="#modalFecharCaixa">
                        <i class="fa-solid fa-lock me-2"></i>Fechar Caixa
                    </button>
                <?php else: ?>
                    <div class="mb-4">
                        <i class="fa-solid fa-lock text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-warning fw-bold mb-3">Fechado</h3>
                    <p class="text-muted">Caixa atual encontra-se fechado.</p>
                    <?php if ($caixaAtual && $caixaAtual['status'] == 'fechado'): ?>
                        <?php 
                            $diasPassados = floor((strtotime(date('Y-m-d')) - strtotime($caixaAtual['data_operacao'])) / 86400); 
                        ?>
                        <?php if ($diasPassados <= 3 && (!empty($_SESSION['permissoes']['caixa:reabrir']) || !empty($_SESSION['permissoes']['todas']))): ?>
                            <button type="button" class="btn btn-warning w-100 fw-bold shadow-sm py-2" data-bs-toggle="modal" data-bs-target="#modalReabrirCaixa">
                                <i class="fa-solid fa-unlock me-2"></i>Reabrir Caixa
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary w-100 fw-bold shadow-sm py-2" disabled>
                                <i class="fa-solid fa-lock me-2"></i>Fechado
                            </button>
                        <?php endif; ?>
                    <?php elseif (!empty($caixaAnteriorAberto)): ?>
                        <button type="button" class="btn btn-orange w-100 fw-bold shadow-sm py-2" data-bs-toggle="modal" data-bs-target="#modalFecharCaixaAnterior">
                            <i class="fa-solid fa-lock-open me-2"></i>Abrir Caixa
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-orange w-100 fw-bold shadow-sm py-2" data-bs-toggle="modal" data-bs-target="#modalAbrirCaixa">
                            <i class="fa-solid fa-lock-open me-2"></i>Abrir Caixa
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Movimentações -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-list-ul me-2"></i>Movimentações de Hoje</h5>
                <?php if ($caixaAberto): ?>
                <div>
                    <button class="btn btn-success btn-sm fw-bold me-1" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                        <i class="fa-solid fa-arrow-down me-1"></i>Entrada
                    </button>
                    <button class="btn btn-danger btn-sm fw-bold me-1" data-bs-toggle="modal" data-bs-target="#modalSaida">
                        <i class="fa-solid fa-arrow-up me-1"></i>Saída
                    </button>
                    <button class="btn btn-secondary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalSangria">
                        <i class="fa-solid fa-money-bill-transfer me-1"></i>Sangria
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Hora</th>
                                <th>Tipo</th>
                                <th>OS</th>
                                <th>Descrição</th>
                                <th>Conta</th>
                                <th>Valor</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$caixaAberto): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">Abra o caixa para visualizar as movimentações.</td></tr>
                            <?php elseif (empty($movimentacoes)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">Nenhuma movimentação registrada hoje.</td></tr>
                            <?php else: ?>
                                <?php 
                                $tot_ent = 0; $tot_sai = 0;
                                foreach($movimentacoes as $m): 
                                    if (($m['categoria_base'] ?? '') !== 'sangria') {
                                        if($m['tipo'] == 'entrada') $tot_ent += $m['valor'];
                                        if($m['tipo'] == 'saida') $tot_sai += $m['valor'];
                                    }
                                ?>
                                <tr>
                                    <td class="text-muted small"><?= date('H:i', strtotime($m['data_movimentacao'])) ?></td>
                                    <td>
                                        <?php if($m['tipo'] == 'entrada'): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-arrow-down"></i> Entrada</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fa-solid fa-arrow-up"></i> Saída</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($m['numero_os'])): ?>
                                            <span class="badge bg-secondary">OS #<?= htmlspecialchars($m['numero_os']) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($m['descricao']) ?>
                                        <?php if($m['eh_parcela']): ?><span class="badge bg-info ms-1">Parcela</span><?php endif; ?>
                                        <?php if(($m['categoria_base'] ?? '') === 'sangria'): ?><span class="badge bg-warning text-dark ms-1">Sangria</span><?php endif; ?>
                                    </td>
                                    <td><small><?= htmlspecialchars($m['conta_nome']) ?></small></td>
                                    <td class="fw-bold <?= $m['tipo'] == 'entrada' ? 'text-success' : 'text-danger' ?>">
                                        <?= $m['tipo'] == 'entrada' ? '+' : '-' ?> R$ <?= number_format($m['valor'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-end">
                                        <?php 
                                        $podeEditar = false;
                                        if (!empty($_SESSION['permissoes']['todas']) || !empty($_SESSION['permissoes']['movimentacao:editar'])) {
                                            $podeEditar = true;
                                        }
                                        if ($podeEditar && $caixaAberto && $m['caixa_id'] == $caixaAberto['id']): 
                                        ?>
                                            <button class="btn btn-sm btn-outline-warning" onclick="editarMovimentacao(<?= $m['id'] ?>)" title="Editar">
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
            <?php if ($caixaAberto && !empty($movimentacoes)): ?>
            <div class="card-footer bg-light d-flex justify-content-between">
                <span class="fw-bold text-success">Entradas: R$ <?= number_format($tot_ent, 2, ',', '.') ?></span>
                <span class="fw-bold text-danger">Saídas: R$ <?= number_format($tot_sai, 2, ',', '.') ?></span>
                <span class="fw-bold text-dark">Saldo do Dia: R$ <?= number_format($tot_ent - $tot_sai, 2, ',', '.') ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Modal Reabrir Caixa -->
<?php if ($caixaAtual && $caixaAtual['status'] == 'fechado'): ?>
<div class="modal fade" id="modalReabrirCaixa" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/caixa/reabrir" method="POST" class="modal-content border-warning">
            <input type="hidden" name="caixa_id" value="<?= $caixaAtual['id'] ?>">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-unlock me-2"></i>Reabrir Caixa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger fw-bold">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Atenção: Ao reabrir este caixa, o saldo dos dias subsequentes será recalculado automaticamente caso haja alterações de valor.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Justificativa para a reabertura <span class="text-danger">*</span></label>
                    <textarea name="justificativa" class="form-control" rows="3" required placeholder="Ex: Correção de lançamento esquecido..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning fw-bold"><i class="fa-solid fa-unlock me-2"></i>Confirmar Reabertura</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modal Abrir Caixa -->
<?php if (!$caixaAberto): ?>
<div class="modal fade" id="modalAbrirCaixa" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/caixa/abrir" method="POST" class="modal-content">
            <div class="modal-header bg-orange text-white">
                <h5 class="modal-title"><i class="fa-solid fa-lock-open me-2"></i>Confirmar Abertura</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if ($isPrimeiroUso): ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>Primeiro uso detectado! Por favor, insira o saldo atual (inicial) de cada conta financeira física/bancária ativa.
                    </div>
                    <?php foreach ($contas as $c): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold"><?= htmlspecialchars($c['nome']) ?></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="saldos[<?= $c['id'] ?>]" class="form-control" value="0,00" required>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Os saldos iniciais serão puxados automaticamente com base no fechamento anterior de cada conta.</p>
                    <p class="mb-0 fw-bold text-center fs-5 text-success">Deseja abrir o caixa de hoje?</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-orange fw-bold">Confirmar Abertura</button>
            </div>
</form>
    </div>
</div>
<?php endif; ?>

<!-- Modal Fechar Caixa -->
<?php if ($caixaAberto): ?>
<div class="modal fade modal-lg" id="modalFecharCaixa" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/caixa/fechar" method="POST" class="modal-content">
            <input type="hidden" name="caixa_id" value="<?= $caixaAberto['id'] ?>">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa-solid fa-lock me-2"></i>Fechamento de Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4">Confira o resumo das contas antes de encerrar o caixa do dia.</p>
                
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Conta</th>
                                <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                    <th>Saldo Inicial</th>
                                <?php endif; ?>
                                <th>Líquido (Dia)</th>
                                <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                    <th>Fechamento</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($saldos_fechamento)): foreach($saldos_fechamento as $sf): 
                                $liquido = $sf['saldo_final'] - $sf['saldo_inicial'];
                            ?>
                                <tr>
                                    <td class="fw-bold text-start"><?= htmlspecialchars($sf['nome']) ?></td>
                                    <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                        <td class="text-muted">R$ <?= number_format($sf['saldo_inicial'], 2, ',', '.') ?></td>
                                    <?php endif; ?>
                                    <td class="<?= $liquido >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                        <?= $liquido >= 0 ? '+' : '' ?>R$ <?= number_format($liquido, 2, ',', '.') ?>
                                    </td>
                                    <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                        <td class="fw-bold text-dark">R$ <?= number_format($sf['saldo_final'], 2, ',', '.') ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4">Nenhum saldo registrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Observações de Fechamento</label>
                    <textarea name="observacao" class="form-control" rows="3" placeholder="Insira qualquer anotação importante ou divergência de valores..."></textarea>
                    <small class="text-muted">Deixe em branco caso não haja divergências ou observações.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Revisar Movimentações</button>
                <button type="submit" class="btn btn-danger fw-bold"><i class="fa-solid fa-lock me-2"></i>Confirmar Fechamento</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modal Fechar Caixa Anterior -->
<?php if (!empty($caixaAnteriorAberto)): ?>
<div class="modal fade modal-lg" id="modalFecharCaixaAnterior" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/caixa/fechar" method="POST" class="modal-content">
            <input type="hidden" name="caixa_id" value="<?= $caixaAnteriorAberto['id'] ?>">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa-solid fa-lock me-2"></i>Fechamento Pendente (<?= date('d/m/Y', strtotime($caixaAnteriorAberto['data_operacao'])) ?>)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning mb-4">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><strong>Atenção!</strong> O caixa do dia <strong><?= date('d/m/Y', strtotime($caixaAnteriorAberto['data_operacao'])) ?></strong> ainda está aberto. É obrigatório fechá-lo antes de abrir o caixa de hoje.
                </div>
                <p class="text-muted mb-4">Confira o resumo das contas antes de encerrar o caixa do dia <?= date('d/m/Y', strtotime($caixaAnteriorAberto['data_operacao'])) ?>.</p>
                
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Conta</th>
                                <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                    <th>Saldo Inicial</th>
                                <?php endif; ?>
                                <th>Líquido (Dia)</th>
                                <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                    <th>Fechamento</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($saldos_fechamento_anterior)): foreach($saldos_fechamento_anterior as $sf): 
                                $liquido = $sf['saldo_final'] - $sf['saldo_inicial'];
                            ?>
                                <tr>
                                    <td class="fw-bold text-start"><?= htmlspecialchars($sf['nome']) ?></td>
                                    <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                        <td class="text-muted">R$ <?= number_format($sf['saldo_inicial'], 2, ',', '.') ?></td>
                                    <?php endif; ?>
                                    <td class="<?= $liquido >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                        <?= $liquido >= 0 ? '+' : '' ?>R$ <?= number_format($liquido, 2, ',', '.') ?>
                                    </td>
                                    <?php if (!empty($_SESSION['permissoes']['dashboard:visualizar']) || !empty($_SESSION['permissoes']['caixa:ver_saldo']) || !empty($_SESSION['permissoes']['todas'])): ?>
                                        <td class="fw-bold text-dark">R$ <?= number_format($sf['saldo_final'], 2, ',', '.') ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4">Nenhum saldo registrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Observações de Fechamento</label>
                    <textarea name="observacao" class="form-control" rows="3" placeholder="Insira qualquer anotação importante ou divergência de valores..."></textarea>
                    <small class="text-muted">Deixe em branco caso não haja divergências ou observações.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Revisar Movimentações</button>
                <button type="submit" class="btn btn-danger fw-bold"><i class="fa-solid fa-lock me-2"></i>Confirmar Fechamento</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modais Entrada, Saída, Sangria (somente se caixa aberto) -->
<?php if ($caixaAberto): ?>
    <?php include __DIR__ . '/../partials/modais_movimentacao.php'; ?>
<?php endif; ?>
