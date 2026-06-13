<?php include __DIR__ . '/_tabs.php'; ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <a href="<?= BASE_URL ?>/config" class="btn btn-light border-2 fw-bold btn-sm mb-3 text-secondary">&larr; Voltar às Configurações</a>
        <h4 class="text-secondary fw-bold mb-0">Meu Perfil de Acesso</h4>
    </div>
</div>

<?php if(!empty($sucesso)): ?>
    <div class="alert alert-success fw-bold shadow-sm border-0 border-start border-5 border-success"><?= $sucesso ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/config/perfil/salvar" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Nome Completo</label>
                        <input type="text" name="nome" class="form-control border-2" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">E-mail de Login</label>
                        <input type="email" name="email" class="form-control border-2" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
                    </div>

                    <hr class="my-4 opacity-25">
                    
                    <h6 class="fw-bold text-purple mb-3">Segurança e Senha</h6>
                    <div class="alert bg-light border-0 py-2 fs-7 text-muted">
                        Deixe o campo em branco caso não queira alterar sua senha atual.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Nova Senha</label>
                        <input type="password" name="senha_nova" class="form-control border-2" placeholder="Digite apenas para alterar">
                    </div>

                    <div class="text-end mt-4 border-top pt-4">
                        <button type="submit" class="btn btn-orange px-5 fw-bold shadow-sm">Salvar Alterações</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
