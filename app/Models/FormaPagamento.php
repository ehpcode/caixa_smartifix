<?php
namespace App\Models;
use App\Core\Model;

class FormaPagamento extends Model {
    public function getAllAtivas() {
        $stmt = $this->db->query("SELECT * FROM formas_pagamento WHERE ativo = 1 ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM formas_pagamento ORDER BY nome ASC")->fetchAll();
    }
    public function inserir($nome) {
        $stmt = $this->db->prepare("INSERT INTO formas_pagamento (nome, ativo) VALUES (?, 1)");
        return $stmt->execute([$nome]);
    }
    public function atualizar($id, $nome) {
        $stmt = $this->db->prepare("UPDATE formas_pagamento SET nome = ? WHERE id = ?");
        return $stmt->execute([$nome, $id]);
    }
    public function toggleAtivo($id) {
        $stmt = $this->db->prepare("UPDATE formas_pagamento SET ativo = CASE WHEN ativo = 1 THEN 0 ELSE 1 END WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
