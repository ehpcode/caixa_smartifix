<?php
namespace App\Models;
use App\Core\Model;

class Fornecedor extends Model {
    public function getAll() {
        return $this->db->query("SELECT f.*, u.nome as cadastrado_por_nome 
                                 FROM fornecedores f 
                                 JOIN usuarios u ON f.cadastrado_por = u.id 
                                 ORDER BY f.nome ASC")->fetchAll();
    }

    public function inserir($nome, $cadastradoPor) {
        $stmt = $this->db->prepare("INSERT INTO fornecedores (nome, cadastrado_por) VALUES (?, ?)");
        return $stmt->execute([$nome, $cadastradoPor]);
    }
}
