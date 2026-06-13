<?php
namespace App\Models;
use App\Core\Model;

class Funcionario extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM funcionarios ORDER BY nome ASC")->fetchAll();
    }

    public function getAllAtivos() {
        return $this->db->query("SELECT id, nome FROM funcionarios WHERE ativo = 1 ORDER BY nome ASC")->fetchAll();
    }

    public function inserir($nome, $cargo) {
        $stmt = $this->db->prepare("INSERT INTO funcionarios (nome, cargo, ativo) VALUES (?, ?, 1)");
        return $stmt->execute([$nome, $cargo]);
    }

    public function atualizar($id, $nome, $cargo) {
        $stmt = $this->db->prepare("UPDATE funcionarios SET nome = ?, cargo = ? WHERE id = ?");
        return $stmt->execute([$nome, $cargo, $id]);
    }

    public function toggleAtivo($id) {
        $stmt = $this->db->prepare("UPDATE funcionarios SET ativo = NOT ativo WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
