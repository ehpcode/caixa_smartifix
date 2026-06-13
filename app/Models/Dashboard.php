<?php
namespace App\Models;
use App\Core\Model;
use App\Models\NaturezaFinanceira;

class Dashboard extends Model {
    
    // Resumo de Entradas, Saídas e Saldo Disponível do dia
    public function getResumoDia($data_operacao) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN m.tipo = 'entrada' THEN m.valor ELSE 0 END) as total_entradas,
                SUM(CASE WHEN m.tipo = 'saida' THEN m.valor ELSE 0 END) as total_saidas
            FROM movimentacoes m
            JOIN caixas c ON m.caixa_id = c.id
            LEFT JOIN naturezas_financeiras nf ON m.natureza_financeira_id = nf.id
            WHERE c.data_operacao = ? AND m.status = 'ativa' AND (nf.categoria_base != '" . NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO . "' OR nf.categoria_base IS NULL)
        ");
        $stmt->execute([$data_operacao]);
        return $stmt->fetch();
    }

    // Soma do Saldo Inicial e Atual das contas
    public function getSaldosContasDia($data_operacao) {
        $stmt = $this->db->prepare("
            SELECT 
                cf.nome,
                cs.saldo_inicial,
                cs.saldo_final
            FROM caixa_saldos cs
            JOIN caixas c ON cs.caixa_id = c.id
            JOIN contas_financeiras cf ON cs.conta_financeira_id = cf.id
            WHERE c.data_operacao = ?
        ");
        $stmt->execute([$data_operacao]);
        return $stmt->fetchAll();
    }

    // Totais por Natureza Financeira (Entrada/Saída)
    public function getTotaisPorNatureza($data_operacao, $tipo) {
        $stmt = $this->db->prepare("
            SELECT 
                nf.nome, 
                SUM(m.valor) as total
            FROM movimentacoes m
            JOIN caixas c ON m.caixa_id = c.id
            JOIN naturezas_financeiras nf ON m.natureza_financeira_id = nf.id
            WHERE c.data_operacao = ? AND m.tipo = ? AND m.status = 'ativa' AND nf.categoria_base != '" . NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO . "'
            GROUP BY nf.id
        ");
        $stmt->execute([$data_operacao, $tipo]);
        return $stmt->fetchAll();
    }

    // Totais por Forma de Pagamento
    public function getTotaisPorFormaPagamento($data_operacao, $tipo) {
        $stmt = $this->db->prepare("
            SELECT 
                fp.nome, 
                SUM(m.valor) as total
            FROM movimentacoes m
            JOIN caixas c ON m.caixa_id = c.id
            JOIN formas_pagamento fp ON m.forma_pagamento_id = fp.id
            LEFT JOIN naturezas_financeiras nf ON m.natureza_financeira_id = nf.id
            WHERE c.data_operacao = ? AND m.tipo = ? AND m.status = 'ativa' AND (nf.categoria_base != '" . NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO . "' OR nf.categoria_base IS NULL)
            GROUP BY fp.id
        ");
        $stmt->execute([$data_operacao, $tipo]);
        return $stmt->fetchAll();
    }

    // Total de Custos Operacionais do dia
    public function getCustosDia($data_operacao) {
        $stmt = $this->db->prepare("
            SELECT SUM(co.valor) as total_custos
            FROM custos_operacionais co
            JOIN item_movimentacao im ON co.item_movimentacao_id = im.id
            JOIN movimentacoes m ON m.item_movimentacao_id = im.id
            WHERE DATE(co.criado_em) = ? AND m.status = 'ativa'
        ");
        $stmt->execute([$data_operacao]);
        $res = $stmt->fetch();
        return $res['total_custos'] ?? 0;
    }

    // Desempenho de Funcionários
    public function getDesempenhoFuncionarios($data_operacao) {
        $stmt = $this->db->prepare("
            SELECT 
                f.nome,
                SUM(CASE WHEN im.tipo = 'os' THEN 1 ELSE 0 END) as qtd_os,
                SUM(CASE WHEN im.tipo = 'venda' THEN 1 ELSE 0 END) as qtd_vendas,
                SUM(CASE WHEN im.tipo = 'servico_avulso' THEN 1 ELSE 0 END) as qtd_avulsos,
                SUM(CASE WHEN im.tipo = 'os' THEN m.valor ELSE 0 END) as valor_os,
                SUM(CASE WHEN im.tipo = 'venda' THEN m.valor ELSE 0 END) as valor_vendas,
                SUM(CASE WHEN im.tipo = 'servico_avulso' THEN m.valor ELSE 0 END) as valor_avulsos,
                SUM(m.valor) as valor_total
            FROM movimentacoes m
            JOIN caixas c ON m.caixa_id = c.id
            JOIN item_movimentacao im ON m.item_movimentacao_id = im.id
            JOIN funcionarios f ON m.funcionario_id = f.id
            WHERE c.data_operacao = ? AND m.status = 'ativa' AND m.tipo = 'entrada'
            GROUP BY f.id
            ORDER BY valor_total DESC
        ");
        $stmt->execute([$data_operacao]);
        return $stmt->fetchAll();
    }
}
