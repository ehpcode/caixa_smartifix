<?php
namespace App\Models;
use App\Core\Model;

class Auditoria extends Model {
    public function getAll() {
        return $this->db->query("SELECT a.*, u.nome as usuario_nome 
                                 FROM logs_auditoria a 
                                 JOIN usuarios u ON a.usuarios_id = u.id 
                                 ORDER BY a.criado_em DESC")->fetchAll();
    }
}
