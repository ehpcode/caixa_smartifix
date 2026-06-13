<div class="row mb-4">
    <div class="col-md-8">
        <a href="<?= BASE_URL ?>/config/usuarios" class="btn btn-light border-2 fw-bold btn-sm mb-3 text-secondary">&larr; Voltar às Configurações</a>
        <h4 class="text-secondary fw-bold mb-0">Cadastrar Novo Usuário</h4>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/config/usuarios/salvar" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Nome Completo</label>
                        <input type="text" name="nome" class="form-control border-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">E-mail de Login</label>
                        <input type="email" name="email" class="form-control border-2" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Senha de Acesso</label>
                            <input type="password" name="senha" class="form-control border-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Perfil do Sistema</label>
                            <select name="perfil_id" class="form-select border-2" required>
                                <?php foreach($perfis as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="text-end mt-4 border-top pt-4">
                        <button type="submit" class="btn btn-orange px-5 fw-bold shadow-sm">Salvar Usuário</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
