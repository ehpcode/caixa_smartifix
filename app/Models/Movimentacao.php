<?php
namespace App\Models;
use App\Core\Model;

class Movimentacao extends Model {
    public function getDoCaixa($caixaId) {
        $stmt = $this->db->prepare("SELECT m.*, c.nome as conta_nome, n.nome as natureza_nome, n.categoria_base, f.nome as forma_nome, im.numero_os, usr.nome as usuario_nome
                                    FROM movimentacoes m
                                    LEFT JOIN contas_financeiras c ON m.conta_financeira_id = c.id
                                    LEFT JOIN naturezas_financeiras n ON m.natureza_financeira_id = n.id
                                    LEFT JOIN formas_pagamento f ON m.forma_pagamento_id = f.id
                                    LEFT JOIN item_movimentacao im ON m.item_movimentacao_id = im.id
                                    LEFT JOIN usuarios usr ON m.criado_por = usr.id
                                    WHERE m.caixa_id = ? AND m.status = 'ativa' 
                                    ORDER BY m.data_movimentacao DESC");
        $stmt->execute([$caixaId]);
        return $stmt->fetchAll();
    }

    public function getResumoCaixa($caixaId) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN m.tipo = 'entrada' THEN m.valor ELSE 0 END) as entradas,
                SUM(CASE WHEN m.tipo = 'saida' THEN m.valor ELSE 0 END) as saidas
            FROM movimentacoes m
            LEFT JOIN naturezas_financeiras nf ON m.natureza_financeira_id = nf.id
            WHERE m.caixa_id = ? AND m.status = 'ativa' AND (nf.categoria_base != '" . \App\Models\NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO . "' OR nf.categoria_base IS NULL)
        ");
        $stmt->execute([$caixaId]);
        return $stmt->fetch();
    }

    public function getFiltered($filtros) {
        $sql = "SELECT m.*, c.nome as conta_nome, n.nome as natureza_nome, n.categoria_base, f.nome as forma_nome, u.nome as funcionario_nome, im.numero_os, usr.nome as usuario_nome
                FROM movimentacoes m
                LEFT JOIN contas_financeiras c ON m.conta_financeira_id = c.id
                LEFT JOIN naturezas_financeiras n ON m.natureza_financeira_id = n.id
                LEFT JOIN formas_pagamento f ON m.forma_pagamento_id = f.id
                LEFT JOIN funcionarios u ON m.funcionario_id = u.id
                LEFT JOIN item_movimentacao im ON m.item_movimentacao_id = im.id
                LEFT JOIN usuarios usr ON m.criado_por = usr.id
                WHERE m.status = 'ativa'";
        
        $params = [];
        
        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND DATE(m.data_movimentacao) >= ?";
            $params[] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $sql .= " AND DATE(m.data_movimentacao) <= ?";
            $params[] = $filtros['data_fim'];
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND m.tipo = ?";
            $params[] = $filtros['tipo'];
        }
        if (!empty($filtros['conta'])) {
            $sql .= " AND m.conta_financeira_id = ?";
            $params[] = $filtros['conta'];
        }
        
        $sql .= " ORDER BY m.data_movimentacao DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function inserir($dados) {
        $data_mov = isset($dados['data_movimentacao']) && !empty($dados['data_movimentacao']) ? $dados['data_movimentacao'] : date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("INSERT INTO movimentacoes 
            (tipo, caixa_id, conta_financeira_id, natureza_financeira_id, forma_pagamento_id, item_movimentacao_id, valor, descricao, data_movimentacao, criado_por, status, funcionario_id, eh_parcela) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ativa', ?, ?)");
            
        return $stmt->execute([
            $dados['tipo'],
            $dados['caixa_id'],
            $dados['conta_financeira_id'],
            $dados['natureza_financeira_id'],
            $dados['forma_pagamento_id'],
            $dados['item_movimentacao_id'] ?? null,
            $dados['valor'],
            $dados['descricao'],
            $data_mov,
            $dados['criado_por'],
            $dados['funcionario_id'] ?? null,
            $dados['eh_parcela'] ?? 0
        ]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT m.*, i.tipo as item_tipo, i.numero_os, i.cliente, i.item, i.valor_total, u.nome as usuario_nome
            FROM movimentacoes m
            LEFT JOIN item_movimentacao i ON m.item_movimentacao_id = i.id
            LEFT JOIN usuarios u ON m.criado_por = u.id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function atualizar($id, $dados) {
        $stmt = $this->db->prepare("UPDATE movimentacoes 
            SET conta_financeira_id = ?, natureza_financeira_id = ?, forma_pagamento_id = ?, 
                item_movimentacao_id = ?, valor = ?, descricao = ?, funcionario_id = ?, eh_parcela = ?
            WHERE id = ?");
            
        return $stmt->execute([
            $dados['conta_financeira_id'],
            $dados['natureza_financeira_id'],
            $dados['forma_pagamento_id'],
            $dados['item_movimentacao_id'] ?? null,
            $dados['valor'],
            $dados['descricao'],
            $dados['funcionario_id'] ?? null,
            $dados['eh_parcela'] ?? 0,
            $id
        ]);
    }
}
