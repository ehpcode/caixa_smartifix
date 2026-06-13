<?php
namespace App\Models;
use App\Core\Model;

class NaturezaFinanceira extends Model {
    public const CATEGORIA_EXCLUIDA_RESULTADO = 'sangria';

    public function getByTipo($tipo) {
        $stmt = $this->db->prepare("SELECT * FROM naturezas_financeiras WHERE tipo = ? AND ativo = 1 ORDER BY nome ASC");
        $stmt->execute([$tipo]);
        return $stmt->fetchAll();
    }

    public function getByCategoriaBase($categoria_base) {
        $stmt = $this->db->prepare("SELECT * FROM naturezas_financeiras WHERE categoria_base = ? AND ativo = 1 ORDER BY nome ASC");
        $stmt->execute([$categoria_base]);
        return $stmt->fetchAll();
    }

    public function getAllAtivas() {
        return $this->db->query("SELECT * FROM naturezas_financeiras WHERE ativo = 1 ORDER BY tipo ASC, nome ASC")->fetchAll();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM naturezas_financeiras ORDER BY tipo ASC, nome ASC")->fetchAll();
    }
    public function inserir($nome, $tipo, $categoria_base) {
        $stmt = $this->db->prepare("INSERT INTO naturezas_financeiras (nome, tipo, categoria_base, ativo) VALUES (?, ?, ?, 1)");
        return $stmt->execute([$nome, $tipo, $categoria_base]);
    }
    public function atualizar($id, $nome, $tipo, $categoria_base) {
        $stmt = $this->db->prepare("UPDATE naturezas_financeiras SET nome = ?, tipo = ?, categoria_base = ? WHERE id = ?");
        return $stmt->execute([$nome, $tipo, $categoria_base, $id]);
    }
    public function toggleAtivo($id) {
        $stmt = $this->db->prepare("UPDATE naturezas_financeiras SET ativo = CASE WHEN ativo = 1 THEN 0 ELSE 1 END WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
