<?php
namespace App\Models;
use App\Core\Model;

class Usuario extends Model {
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT u.*, p.nome as perfil_nome, p.permissoes 
                                    FROM usuarios u 
                                    JOIN perfis p ON u.perfil_id = p.id 
                                    WHERE u.email = ? AND u.ativo = 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT u.*, p.nome as perfil_nome FROM usuarios u LEFT JOIN perfis p ON u.perfil_id = p.id ORDER BY u.nome ASC");
        return $stmt->fetchAll();
    }

    public function atualizar($id, $dados) {
        if (!empty($dados['senha'])) {
            $stmt = $this->db->prepare("UPDATE usuarios SET nome = ?, email = ?, perfil_id = ?, senha = ? WHERE id = ?");
            return $stmt->execute([$dados['nome'], $dados['email'], $dados['perfil_id'], $dados['senha'], $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE usuarios SET nome = ?, email = ?, perfil_id = ? WHERE id = ?");
            return $stmt->execute([$dados['nome'], $dados['email'], $dados['perfil_id'], $id]);
        }
    }

    public function atualizarSenha($id, $senhaHash) {
        $stmt = $this->db->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        return $stmt->execute([$senhaHash, $id]);
    }

    public function atualizarPerfil($id, $nome, $email) {
        $stmt = $this->db->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        return $stmt->execute([$nome, $email, $id]);
    }

    public function inserir($dados) {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha, perfil_id, ativo) VALUES (?, ?, ?, ?, 1)");
        return $stmt->execute([
            $dados['nome'],
            $dados['email'],
            $dados['senha'],
            $dados['perfil_id']
        ]);
    }

    public function toggleAtivo($id) {
        $stmt = $this->db->prepare("UPDATE usuarios SET ativo = CASE WHEN ativo = 1 THEN 0 ELSE 1 END WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
