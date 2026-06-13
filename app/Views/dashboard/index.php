<div class="d-flex justify-content-end align-items-center mb-4">
    <form class="d-flex" method="GET" action="<?= BASE_URL ?>/dashboard">
        <input type="date" name="data" class="form-control me-2" value="<?= htmlspecialchars($data_operacao) ?>">
        <button type="submit" class="btn btn-orange fw-bold">Filtrar</button>
    </form>
</div>

<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card bg-primary text-white h-100 py-2 shadow-sm border-0">
            <div class="card-body px-3 py-2">
                <div class="text-xs fw-bold text-uppercase mb-1 opacity-75">Saldo Inicial</div>
                <div class="h4 mb-0 fw-bold">R$ <?= number_format($totais['saldo_inicial'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card bg-success text-white h-100 py-2 shadow-sm border-0">
            <div class="card-body px-3 py-2">
                <div class="text-xs fw-bold text-uppercase mb-1 opacity-75">Entradas</div>
                <div class="h4 mb-0 fw-bold">R$ <?= number_format($totais['entradas'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card bg-danger text-white h-100 py-2 shadow-sm border-0">
            <div class="card-body px-3 py-2">
                <div class="text-xs fw-bold text-uppercase mb-1 opacity-75">Saídas</div>
                <div class="h4 mb-0 fw-bold">R$ <?= number_format($totais['saidas'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card bg-warning text-dark h-100 py-2 shadow-sm border-0">
            <div class="card-body px-3 py-2">
                <div class="text-xs fw-bold text-uppercase mb-1 opacity-75">Custos</div>
                <div class="h4 mb-0 fw-bold">R$ <?= number_format($totais['custos'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card bg-info text-white h-100 py-2 shadow-sm border-0">
            <div class="card-body px-3 py-2">
                <div class="text-xs fw-bold text-uppercase mb-1 opacity-75">Lucro Bruto</div>
                <div class="h4 mb-0 fw-bold">R$ <?= number_format($totais['lucro_bruto'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card bg-dark text-white h-100 py-2 shadow-sm border-0">
            <div class="card-body px-3 py-2">
                <div class="text-xs fw-bold text-uppercase mb-1 opacity-75">Disponível</div>
                <div class="h4 mb-0 fw-bold">R$ <?= number_format($totais['saldo_disponivel'], 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-chart-pie text-success me-2"></i>Entradas por Natureza (R$)</div>
            <div class="card-body">
                <canvas id="chartEntradasNatureza"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-chart-pie text-danger me-2"></i>Saídas por Natureza (R$)</div>
            <div class="card-body">
                <canvas id="chartSaidasNatureza"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-wallet text-success me-2"></i>Entradas</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-md mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Forma</th>
                                <th class="text-end">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($graficos['entradas_forma'])): ?>
                                <tr><td colspan="2" class="text-muted text-center">Sem dados.</td></tr>
                            <?php else: ?>
                                <?php foreach($graficos['entradas_forma'] as $e): ?>
                                <tr>
                                    <td><?= htmlspecialchars($e['nome'] ?: 'Indefinida') ?></td>
                                    <td class="text-end fw-bold text-success"><?= number_format($e['total'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-wallet text-danger me-2"></i>Saídas</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-md mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Forma</th>
                                <th class="text-end">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($graficos['saidas_forma'])): ?>
                                <tr><td colspan="2" class="text-muted text-center">Sem dados.</td></tr>
                            <?php else: ?>
                                <?php foreach($graficos['saidas_forma'] as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['nome'] ?: 'Indefinida') ?></td>
                                    <td class="text-end fw-bold text-danger"><?= number_format($s['total'], 2, ',', '.') ?></td>
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

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-users me-2"></i>Desempenho por Funcionário</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Serviços</th>
                                <th>Vendas</th>
                                <th>R$ Serviços</th>
                                <th>R$ Vendas</th>
                                <th><strong class="text-orange">Geral</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($desempenho)): ?>
                                <tr><td colspan="6" class="text-muted">Nenhum dado para este dia.</td></tr>
                            <?php else: ?>
                                <?php foreach($desempenho as $d): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($d['nome']) ?></td>
                                    <td><?= $d['qtd_os'] ?></td>
                                    <td><?= $d['qtd_vendas'] ?></td>
                                    <td>R$ <?= number_format($d['valor_os'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($d['valor_vendas'], 2, ',', '.') ?></td>
                                    <td class="fw-bold text-success">R$ <?= number_format($d['valor_total'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-building-columns me-2"></i>Contas e Saldos</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Conta</th>
                                <th class="text-end">Inicial</th>
                                <th class="text-end">Fechamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($saldos)): ?>
                                <tr><td colspan="3" class="text-muted text-center">Nenhum saldo registrado.</td></tr>
                            <?php else: ?>
                                <?php foreach($saldos as $s): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($s['nome']) ?></td>
                                    <td class="text-end text-muted">R$ <?= number_format($s['saldo_inicial'], 2, ',', '.') ?></td>
                                    <td class="text-end fw-bold">R$ <?= number_format($s['saldo_final'], 2, ',', '.') ?></td>
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
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    const graficosData = <?= json_encode($graficos) ?>;
    createPieChart('chartEntradasNatureza', graficosData.entradas_natureza);
    createPieChart('chartSaidasNatureza', graficosData.saidas_natureza);
});
</script>
