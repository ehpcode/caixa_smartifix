<div class="row mb-4">
    <div class="col-md-8">
        <a href="<?= BASE_URL ?>/config/usuarios" class="btn btn-light border-2 fw-bold btn-sm mb-3 text-secondary">&larr; Voltar às Configurações</a>
        <h4 class="text-secondary fw-bold mb-0">Editar Usuário</h4>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/config/usuarios/atualizar/<?= $usuario['id'] ?>" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Nome Completo</label>
                        <input type="text" name="nome" class="form-control border-2" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">E-mail de Acesso</label>
                        <input type="email" name="email" class="form-control border-2" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Perfil de Acesso</label>
                        <select name="perfil_id" class="form-select border-2" required>
                            <option value="">Selecione um perfil...</option>
                            <?php foreach($perfis as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $usuario['perfil_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Nova Senha (deixe em branco para não alterar)</label>
                        <input type="password" name="senha" class="form-control border-2 border-danger" placeholder="Somente se quiser alterar">
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-orange px-5 fw-bold shadow-sm">Atualizar Dados</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
