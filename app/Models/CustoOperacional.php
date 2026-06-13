<?php
namespace App\Models;
use App\Core\Model;

class CustoOperacional extends Model {
    public function inserir($dados) {
        $stmt = $this->db->prepare("INSERT INTO custos_operacionais (item_movimentacao_id, descricao, tipo, fornecedor_id, valor, criado_por) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $dados['item_movimentacao_id'],
            $dados['descricao'],
            $dados['tipo'],
            $dados['fornecedor_id'] ?? null,
            $dados['valor'],
            $dados['criado_por']
        ]);
    }

    public function getPorItem($itemId) {
        $stmt = $this->db->prepare("SELECT c.*, f.nome as fornecedor_nome FROM custos_operacionais c LEFT JOIN fornecedores f ON c.fornecedor_id = f.id WHERE c.item_movimentacao_id = ?");
        $stmt->execute([$itemId]);
        return $stmt->fetchAll();
    }

    public function atualizar($id, $dados) {
        $stmt = $this->db->prepare("UPDATE custos_operacionais SET descricao = ?, tipo = ?, fornecedor_id = ?, valor = ? WHERE id = ?");
        return $stmt->execute([
            $dados['descricao'],
            $dados['tipo'],
            $dados['fornecedor_id'] ?? null,
            $dados['valor'],
            $id
        ]);
    }

    public function deletarAusentes($itemId, $idsMantidos) {
        if (empty($idsMantidos)) {
            $stmt = $this->db->prepare("DELETE FROM custos_operacionais WHERE item_movimentacao_id = ?");
            return $stmt->execute([$itemId]);
        } else {
            $placeholders = str_repeat('?,', count($idsMantidos) - 1) . '?';
            $sql = "DELETE FROM custos_operacionais WHERE item_movimentacao_id = ? AND id NOT IN ($placeholders)";
            $params = array_merge([$itemId], $idsMantidos);
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        }
    }
}
