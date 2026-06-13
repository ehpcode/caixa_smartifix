<?php
namespace App\Models;
use App\Core\Model;

class ItemMovimentacao extends Model {
    public function getFiltered($filtros) {
        $sql = "SELECT i.*, u.nome as funcionario_nome FROM item_movimentacao i
                LEFT JOIN usuarios u ON i.criado_por = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND DATE(i.data) >= ?";
            $params[] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $sql .= " AND DATE(i.data) <= ?";
            $params[] = $filtros['data_fim'];
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND i.tipo = ?";
            $params[] = $filtros['tipo'];
        }
        
        if (!empty($filtros['exclude_tipo'])) {
            $sql .= " AND i.tipo != ?";
            $params[] = $filtros['exclude_tipo'];
        }
        
        $sql .= " ORDER BY i.data DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function inserir($dados) {
        $stmt = $this->db->prepare("INSERT INTO item_movimentacao (tipo, numero_os, valor_total, descricao, item, cliente, criado_por, data) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $dados['tipo'],
            $dados['numero_os'],
            $dados['valor_total'],
            $dados['descricao'],
            $dados['item'],
            $dados['cliente'],
            $dados['criado_por']
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM item_movimentacao WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByNumeroOs($numero_os) {
        $stmt = $this->db->prepare("SELECT * FROM item_movimentacao WHERE numero_os = ? AND tipo = 'os'");
        $stmt->execute([$numero_os]);
        return $stmt->fetch();
    }

    public function atualizar($id, $dados) {
        $stmt = $this->db->prepare("UPDATE item_movimentacao SET numero_os = ?, cliente = ?, item = ?, descricao = ?, valor_total = ? WHERE id = ?");
        return $stmt->execute([
            $dados['numero_os'],
            $dados['cliente'],
            $dados['item'],
            $dados['descricao'],
            $dados['valor_total'],
            $id
        ]);
    }
}
