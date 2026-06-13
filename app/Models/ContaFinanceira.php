<?php
namespace App\Models;
use App\Core\Model;

class ContaFinanceira extends Model {
    public function getAllAtivas() {
        $stmt = $this->db->query("SELECT * FROM contas_financeiras WHERE ativo = 1 ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM contas_financeiras ORDER BY nome ASC")->fetchAll();
    }
    public function inserir($nome, $tipo) {
        $stmt = $this->db->prepare("INSERT INTO contas_financeiras (nome, tipo, ativo) VALUES (?, ?, 1)");
        return $stmt->execute([$nome, $tipo]);
    }
    public function atualizar($id, $nome, $tipo) {
        $stmt = $this->db->prepare("UPDATE contas_financeiras SET nome = ?, tipo = ? WHERE id = ?");
        return $stmt->execute([$nome, $tipo, $id]);
    }
    public function toggleAtivo($id) {
        $stmt = $this->db->prepare("UPDATE contas_financeiras SET ativo = CASE WHEN ativo = 1 THEN 0 ELSE 1 END WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
