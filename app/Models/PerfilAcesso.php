<?php
namespace App\Models;
use App\Core\Model;

class PerfilAcesso extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM perfis ORDER BY nome ASC")->fetchAll();
    }

    public function inserir($dados) {
        $stmt = $this->db->prepare("INSERT INTO perfis (nome, descricao, permissoes) VALUES (?, ?, ?)");
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'],
            $dados['permissoes']
        ]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM perfis WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function atualizar($id, $dados) {
        $stmt = $this->db->prepare("UPDATE perfis SET nome = ?, descricao = ?, permissoes = ? WHERE id = ?");
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'],
            $dados['permissoes'],
            $id
        ]);
    }
}
