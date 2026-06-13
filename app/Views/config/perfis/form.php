<div class="row mb-4">
    <div class="col-md-8">
        <a href="<?= BASE_URL ?>/config/perfis" class="btn btn-light border-2 fw-bold btn-sm mb-3 text-secondary">&larr; Voltar para Perfis</a>
        <h4 class="text-secondary fw-bold mb-0">Cadastrar Novo Perfil</h4>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/config/perfis/salvar" method="POST">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Nome do Perfil</label>
                            <input type="text" name="nome" class="form-control border-2" placeholder="Ex: Gerente" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Breve Descrição</label>
                            <input type="text" name="descricao" class="form-control border-2" placeholder="Ex: Acesso total às rotinas financeiras">
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">
                    <h6 class="fw-bold text-purple mb-3">Permissões de Visualização (Módulos)</h6>
                    
                    <div class="alert alert-light border-2 d-flex align-items-center px-4">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input fs-5 mt-0" type="checkbox" name="perm_todas" id="perm_todas">
                            <label class="form-check-label fw-bold text-dark" for="perm_todas">Acesso Total (Todas as Permissões)</label>
                        </div>
                    </div>
                    <div class="row g-3">
                        <!-- Módulo de Caixa & Operação -->
                        <div class="col-md-12 mt-3">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-orange"><i class="fa-solid fa-cash-register me-2"></i>Módulo de Caixa & Operação</h6>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_caixa_visualizar" id="pc_v" checked>
                                <label class="form-check-label fw-bold" for="pc_v">Acesso à tela</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_caixa_abrir" id="pc_a">
                                <label class="form-check-label fw-bold" for="pc_a">Abrir Caixa</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_caixa_fechar" id="pc_f">
                                <label class="form-check-label fw-bold" for="pc_f">Fechar Caixa</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_sangria_criar" id="ps_c">
                                <label class="form-check-label fw-bold" for="ps_c">Realizar Sangria</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_caixa_ver_saldo" id="pc_vs">
                                <label class="form-check-label fw-bold text-primary" for="pc_vs">Ver Saldos (Contas)</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_caixa_reabrir" id="pc_r">
                                <label class="form-check-label fw-bold text-danger" for="pc_r">Reabrir Caixa Fechado</label>
                            </div>
                        </div>

                        <!-- Módulo de Movimentações -->
                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="fa-solid fa-exchange-alt me-2"></i>Módulo de Movimentações</h6>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_movimentacao_visualizar_todas" id="pm_v" checked>
                                <label class="form-check-label fw-bold" for="pm_v">Ver Histórico</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_movimentacao_criar" id="pm_c">
                                <label class="form-check-label fw-bold" for="pm_c">Registrar Nova</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_movimentacao_editar" id="pm_e">
                                <label class="form-check-label fw-bold text-danger" for="pm_e">Editar Valores</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_movimentacao_cancelar" id="pm_ca">
                                <label class="form-check-label fw-bold text-danger" for="pm_ca">Cancelar Transação</label>
                            </div>
                        </div>

                        <!-- Módulo de OS e Vendas -->
                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-success"><i class="fa-solid fa-tools me-2"></i>Módulo de OS e Vendas</h6>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_os_visualizar" id="po_v" checked>
                                <label class="form-check-label fw-bold" for="po_v">Acesso às OSs</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_venda_visualizar" id="pv_v" checked>
                                <label class="form-check-label fw-bold" for="pv_v">Acesso às Vendas</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_custos_gerenciar" id="pcg_g">
                                <label class="form-check-label fw-bold text-warning text-dark" for="pcg_g">Gerenciar Custos</label>
                            </div>
                        </div>

                        <!-- Módulo Gerencial e Configurações -->
                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-purple"><i class="fa-solid fa-chart-pie me-2"></i>Gerencial e Configurações</h6>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_dashboard_visualizar" id="pg_dv" checked>
                                <label class="form-check-label fw-bold" for="pg_dv">Acesso ao Dashboard</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_relatorios_visualizar" id="pg_rv">
                                <label class="form-check-label fw-bold text-primary" for="pg_rv">Acesso à Tela de Relatórios</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="perm_relatorios_gerar" id="pg_rg">
                                <label class="form-check-label fw-bold text-primary" for="pg_rg">Gerar/Exportar Relatórios</label>
                            </div>
                            <div class="form-check form-switch form-check-inline mt-2">
                                <input class="form-check-input" type="checkbox" name="perm_configuracoes_visualizar" id="pg_cv">
                                <label class="form-check-label fw-bold text-danger" for="pg_cv">Acesso às Configurações</label>
                            </div>
                            <div class="form-check form-switch form-check-inline mt-2">
                                <input class="form-check-input" type="checkbox" name="perm_cadastros_gerenciar" id="pg_cg">
                                <label class="form-check-label fw-bold text-danger" for="pg_cg">Gerenciar Cadastros (Usuários, Bancos)</label>
                            </div>
                            <div class="form-check form-switch form-check-inline mt-2">
                                <input class="form-check-input" type="checkbox" name="perm_auditoria_visualizar" id="pg_av">
                                <label class="form-check-label fw-bold text-danger" for="pg_av">Visualizar Auditoria</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-purple px-5 fw-bold shadow-sm">Salvar Perfil</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('perm_todas').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('.form-check-input:not(#perm_todas)');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>
