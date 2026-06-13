<div class="row mb-4 align-items-center justify-content-end">
    <div class="col-md-6 text-end">
        <form action="<?= BASE_URL ?>/relatorios" method="GET" class="d-flex justify-content-end align-items-center gap-2">
            <select name="mes" class="form-select w-auto fw-bold text-purple border-2">
                <?php for($i=1; $i<=12; $i++): $m = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                    <option value="<?= $m ?>" <?= $m == $mesSelecionado ? 'selected' : '' ?>><?= $m ?></option>
                <?php endfor; ?>
            </select>
            <select name="ano" class="form-select w-auto fw-bold text-orange border-2">
                <?php for($y=date('Y')-2; $y<=date('Y')+1; $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $anoSelecionado ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-dark fw-bold px-3 shadow-sm"><i class="fa-solid fa-filter me-2"></i>Filtrar</button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center py-4">
                <div class="text-xs fw-bold text-success text-uppercase mb-2">Entradas do Mês</div>
                <div class="h3 mb-0 fw-bold text-success">R$ <?= number_format($resumo['entradas'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center py-4">
                <div class="text-xs fw-bold text-danger text-uppercase mb-2">Saídas do Mês</div>
                <div class="h3 mb-0 fw-bold text-danger">R$ <?= number_format($resumo['saidas'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center py-4">
                <div class="text-xs fw-bold text-primary text-uppercase mb-2">Ticket Médio Geral</div>
                <div class="h3 mb-0 fw-bold text-primary">R$ <?= number_format($resumo['ticket_medio'], 2, ',', '.') ?></div>
                <small class="text-muted d-block mt-2 fw-bold">Volume: <?= $resumo['qtd_os'] ?> serviços/vendas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0 h-100 <?= $resumo['resultado'] >= 0 ? 'bg-orange text-white' : 'bg-danger text-white' ?>">
            <div class="card-body text-center py-4">
                <div class="text-xs fw-bold text-uppercase mb-2 opacity-75">Resultado Financeiro</div>
                <div class="h3 mb-0 fw-bold">R$ <?= number_format($resumo['resultado'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-chart-pie text-success me-2"></i>Entradas por Categoria</div>
            <div class="card-body">
                <canvas id="chartEntradasNat"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-chart-pie text-danger me-2"></i>Saídas por Categoria</div>
            <div class="card-body">
                <canvas id="chartSaidasNat"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-table me-2"></i>DRE Simplificada (Demonstrativo do Resultado)</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Natureza / Categoria</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-end">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-success"><td colspan="3" class="fw-bold"><i class="fa-solid fa-arrow-down me-2"></i>RECEITAS (ENTRADAS)</td></tr>
                            <?php if(empty($resumo['entradas_natureza'])): ?>
                                <tr><td colspan="3" class="text-muted text-center">Nenhuma entrada no período.</td></tr>
                            <?php else: ?>
                                <?php foreach($resumo['entradas_natureza'] as $en): ?>
                                <tr>
                                    <td class="ps-4"><?= htmlspecialchars($en['nome']) ?></td>
                                    <td class="text-center"><span class="badge bg-success">Entrada</span></td>
                                    <td class="text-end fw-bold text-success">+ <?= number_format($en['total'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr><td colspan="3" class="text-end fw-bold">Subtotal Entradas: <span class="text-success">R$ <?= number_format($resumo['entradas'], 2, ',', '.') ?></span></td></tr>

                            <tr class="table-danger"><td colspan="3" class="fw-bold"><i class="fa-solid fa-arrow-up me-2"></i>DESPESAS E CUSTOS (SAÍDAS)</td></tr>
                            <?php if(empty($resumo['saidas_natureza'])): ?>
                                <tr><td colspan="3" class="text-muted text-center">Nenhuma saída no período.</td></tr>
                            <?php else: ?>
                                <?php foreach($resumo['saidas_natureza'] as $sn): ?>
                                <tr>
                                    <td class="ps-4"><?= htmlspecialchars($sn['nome']) ?></td>
                                    <td class="text-center"><span class="badge bg-danger">Saída</span></td>
                                    <td class="text-end fw-bold text-danger">- <?= number_format($sn['total'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr><td colspan="3" class="text-end fw-bold">Subtotal Saídas: <span class="text-danger">R$ <?= number_format($resumo['saidas'], 2, ',', '.') ?></span></td></tr>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="2" class="fw-bold fs-5 text-end">LUCRO / PREJUÍZO LÍQUIDO DO MÊS:</td>
                                <td class="fw-bold fs-5 text-end <?= $resumo['resultado'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    R$ <?= number_format($resumo['resultado'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Rentabilidade OS -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-wrench text-primary me-2"></i>Ordens de Serviço (Rentabilidade)</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Qtd OS
                        <span class="badge bg-primary rounded-pill"><?= $resumo['rentabilidade_os']['qtd'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Ticket Médio
                        <span class="fw-bold">R$ <?= number_format($resumo['rentabilidade_os']['ticket_medio'], 2, ',', '.') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total das OS
                        <span class="fw-bold text-success">R$ <?= number_format($resumo['rentabilidade_os']['faturado'], 2, ',', '.') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Custos (Peças, Mão de Obra, etc)
                        <span class="fw-bold text-danger">- R$ <?= number_format($resumo['rentabilidade_os']['custos'], 2, ',', '.') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold fs-5 mt-2">
                        Lucro Bruto
                        <span class="<?= $resumo['rentabilidade_os']['lucro_bruto'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            R$ <?= number_format($resumo['rentabilidade_os']['lucro_bruto'], 2, ',', '.') ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Rentabilidade Vendas -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-shopping-cart text-info me-2"></i>Vendas (Rentabilidade)</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Qtd Vendas
                        <span class="badge bg-info rounded-pill"><?= $resumo['rentabilidade_vendas']['qtd'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Ticket Médio
                        <span class="fw-bold">R$ <?= number_format($resumo['rentabilidade_vendas']['ticket_medio'], 2, ',', '.') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Faturado
                        <span class="fw-bold text-success">R$ <?= number_format($resumo['rentabilidade_vendas']['faturado'], 2, ',', '.') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Custos
                        <span class="fw-bold text-danger">- R$ <?= number_format($resumo['rentabilidade_vendas']['custos'], 2, ',', '.') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold fs-5 mt-2">
                        Lucro Bruto
                        <span class="<?= $resumo['rentabilidade_vendas']['lucro_bruto'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            R$ <?= number_format($resumo['rentabilidade_vendas']['lucro_bruto'], 2, ',', '.') ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-credit-card text-purple me-2"></i>Volume por Forma de Pagamento</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Forma de Pagamento</th>
                                <th class="text-end">Volume (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($resumo['volume_forma_pagamento'])): ?>
                                <tr><td colspan="2" class="text-muted text-center py-3">Nenhum dado encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach($resumo['volume_forma_pagamento'] as $vp): ?>
                                <tr>
                                    <td class="ps-3"><?= htmlspecialchars($vp['nome']) ?></td>
                                    <td class="text-end fw-bold pe-3">R$ <?= number_format($vp['total'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-users text-orange me-2"></i>OS/Vendas por Funcionário</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Funcionário</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($resumo['performance_funcionario'])): ?>
                                <tr><td colspan="3" class="text-muted text-center py-3">Nenhum dado encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach($resumo['performance_funcionario'] as $pf): ?>
                                <tr>
                                    <td class="ps-3"><?= htmlspecialchars($pf['funcionario']) ?></td>
                                    <td class="text-center"><span class="badge bg-secondary"><?= $pf['qtd'] ?></span></td>
                                    <td class="text-end fw-bold text-success pe-3">R$ <?= number_format($pf['valor_total'], 2, ',', '.') ?></td>
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

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-building-columns text-dark me-2"></i>Saldos e Movimentações por Conta</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Conta</th>
                                <th class="text-end">Saldo Inicial</th>
                                <th class="text-end text-success">Entradas</th>
                                <th class="text-end text-danger">Saídas</th>
                                <th class="text-end">Saldo Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($resumo['saldo_por_conta'])): ?>
                                <tr><td colspan="5" class="text-muted text-center py-3">Nenhum dado encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach($resumo['saldo_por_conta'] as $sc): ?>
                                <tr>
                                    <td class="fw-bold ps-3"><?= htmlspecialchars($sc['conta']) ?></td>
                                    <td class="text-end">R$ <?= number_format($sc['saldo_inicial'], 2, ',', '.') ?></td>
                                    <td class="text-end text-success">+ R$ <?= number_format($sc['entradas'], 2, ',', '.') ?></td>
                                    <td class="text-end text-danger">- R$ <?= number_format($sc['saidas'], 2, ',', '.') ?></td>
                                    <td class="text-end fw-bold pe-3">R$ <?= number_format($sc['saldo_final'], 2, ',', '.') ?></td>
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

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-calendar-days text-secondary me-2"></i>Movimentação Diária</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th class="text-end text-success">Entradas</th>
                                <th class="text-end text-danger">Saídas</th>
                                <th class="text-end">Resultado do Dia</th>
                                <th class="text-end">Saldo Fechamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($resumo['movimentacao_diaria'])): ?>
                                <tr><td colspan="5" class="text-muted text-center py-3">Nenhum dado encontrado no mês.</td></tr>
                            <?php else: ?>
                                <?php foreach($resumo['movimentacao_diaria'] as $md): ?>
                                <tr>
                                    <td class="ps-3"><?= date('d/m/Y', strtotime($md['dia'])) ?></td>
                                    <td class="text-end text-success">+ R$ <?= number_format($md['entradas'], 2, ',', '.') ?></td>
                                    <td class="text-end text-danger">- R$ <?= number_format($md['saidas'], 2, ',', '.') ?></td>
                                    <td class="text-end fw-bold <?= $md['resultado'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        R$ <?= number_format($md['resultado'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-end fw-bold pe-3">R$ <?= number_format($md['saldo_fechamento'], 2, ',', '.') ?></td>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const defaultColors = ['#1cc88a', '#e74a3b', '#f6c23e', '#36b9cc', '#4e73df', '#858796', '#5a5c69'];

    function createPieChart(ctxId, rawData) {
        if (!document.getElementById(ctxId)) return;
        const ctx = document.getElementById(ctxId).getContext('2d');
        const labels = rawData.map(r => r.nome || 'Indefinido');
        const data = rawData.map(r => r.total);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: defaultColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }

    const relatorioData = <?= json_encode($resumo) ?>;
    createPieChart('chartEntradasNat', relatorioData.entradas_natureza);
    createPieChart('chartSaidasNat', relatorioData.saidas_natureza);
});
</script>
