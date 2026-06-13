<?php
namespace App\Models;
use App\Core\Model;

class Relatorio extends Model {
    public function getResumoMensal($mes, $ano) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN m.tipo = 'entrada' THEN m.valor ELSE 0 END) as total_entradas,
                SUM(CASE WHEN m.tipo = 'saida' THEN m.valor ELSE 0 END) as total_saidas
            FROM movimentacoes m
            LEFT JOIN naturezas_financeiras nf ON m.natureza_financeira_id = nf.id
            WHERE MONTH(m.data_movimentacao) = ? AND YEAR(m.data_movimentacao) = ? AND m.status = 'ativa' AND (nf.categoria_base != '" . \App\Models\NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO . "' OR nf.categoria_base IS NULL)
        ");
        $stmt->execute([$mes, $ano]);
        $fluxo = $stmt->fetch();

        $stmtTM = $this->db->prepare("
            SELECT AVG(valor_total) as ticket_medio, COUNT(id) as qtd_os
            FROM item_movimentacao
            WHERE (tipo = 'os' OR tipo = 'venda') AND MONTH(data) = ? AND YEAR(data) = ?
        ");
        $stmtTM->execute([$mes, $ano]);
        $ticket = $stmtTM->fetch();

        $stmtNatEntrada = $this->db->prepare("
            SELECT n.nome, SUM(m.valor) as total
            FROM movimentacoes m
            JOIN naturezas_financeiras n ON m.natureza_financeira_id = n.id
            WHERE m.tipo = 'entrada' AND MONTH(m.data_movimentacao) = ? AND YEAR(m.data_movimentacao) = ? AND m.status = 'ativa' AND n.categoria_base != '" . \App\Models\NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO . "'
            GROUP BY n.id
        ");
        $stmtNatEntrada->execute([$mes, $ano]);
        $entradas_natureza = $stmtNatEntrada->fetchAll();

        $stmtNatSaida = $this->db->prepare("
            SELECT n.nome, SUM(m.valor) as total
            FROM movimentacoes m
            JOIN naturezas_financeiras n ON m.natureza_financeira_id = n.id
            WHERE m.tipo = 'saida' AND MONTH(m.data_movimentacao) = ? AND YEAR(m.data_movimentacao) = ? AND m.status = 'ativa' AND n.categoria_base != '" . \App\Models\NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO . "'
            GROUP BY n.id
        ");
        $stmtNatSaida->execute([$mes, $ano]);
        $saidas_natureza = $stmtNatSaida->fetchAll();

        return [
            'entradas' => $fluxo['total_entradas'] ?? 0,
            'saidas' => $fluxo['total_saidas'] ?? 0,
            'ticket_medio' => $ticket['ticket_medio'] ?? 0,
            'qtd_os' => $ticket['qtd_os'] ?? 0,
            'resultado' => ($fluxo['total_entradas'] ?? 0) - ($fluxo['total_saidas'] ?? 0),
            'entradas_natureza' => $entradas_natureza,
            'saidas_natureza' => $saidas_natureza,
            'volume_forma_pagamento' => $this->getVolumePorFormaPagamento($mes, $ano),
            'saldo_por_conta' => $this->getSaldoPorConta($mes, $ano),
            'rentabilidade_os' => $this->getRentabilidadeOSVendas($mes, $ano, 'os'),
            'rentabilidade_vendas' => $this->getRentabilidadeOSVendas($mes, $ano, 'venda'),
            'performance_funcionario' => $this->getPerformanceFuncionario($mes, $ano),
            'movimentacao_diaria' => $this->getMovimentacaoDiaria($mes, $ano)
        ];
    }

    private function getVolumePorFormaPagamento($mes, $ano) {
        $stmt = $this->db->prepare("
            SELECT fp.nome, SUM(m.valor) as total
            FROM movimentacoes m
            JOIN formas_pagamento fp ON m.forma_pagamento_id = fp.id
            WHERE m.tipo = 'entrada' AND m.status = 'ativa' AND MONTH(m.data_movimentacao) = ? AND YEAR(m.data_movimentacao) = ?
            GROUP BY fp.id
            ORDER BY total DESC
        ");
        $stmt->execute([$mes, $ano]);
        return $stmt->fetchAll();
    }

    private function getSaldoPorConta($mes, $ano) {
        $stmtContas = $this->db->prepare("SELECT id, nome FROM contas_financeiras WHERE ativo = 1");
        $stmtContas->execute();
        $contas = $stmtContas->fetchAll();

        $resultados = [];
        foreach ($contas as $conta) {
            $contaId = $conta['id'];
            
            $stmtInicial = $this->db->prepare("
                SELECT cs.saldo_inicial 
                FROM caixa_saldos cs
                JOIN caixas c ON cs.caixa_id = c.id
                WHERE cs.conta_financeira_id = ? AND MONTH(c.data_operacao) = ? AND YEAR(c.data_operacao) = ?
                ORDER BY c.data_operacao ASC LIMIT 1
            ");
            $stmtInicial->execute([$contaId, $mes, $ano]);
            $saldoInicialRow = $stmtInicial->fetch();
            $saldoInicial = $saldoInicialRow ? $saldoInicialRow['saldo_inicial'] : 0;

            $stmtFinal = $this->db->prepare("
                SELECT cs.saldo_final 
                FROM caixa_saldos cs
                JOIN caixas c ON cs.caixa_id = c.id
                WHERE cs.conta_financeira_id = ? AND MONTH(c.data_operacao) = ? AND YEAR(c.data_operacao) = ?
                ORDER BY c.data_operacao DESC LIMIT 1
            ");
            $stmtFinal->execute([$contaId, $mes, $ano]);
            $saldoFinalRow = $stmtFinal->fetch();
            $saldoFinal = $saldoFinalRow ? $saldoFinalRow['saldo_final'] : 0;

            $stmtMovs = $this->db->prepare("
                SELECT 
                    SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END) as entradas,
                    SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END) as saidas
                FROM movimentacoes
                WHERE conta_financeira_id = ? AND status = 'ativa' AND MONTH(data_movimentacao) = ? AND YEAR(data_movimentacao) = ?
            ");
            $stmtMovs->execute([$contaId, $mes, $ano]);
            $movs = $stmtMovs->fetch();

            if($saldoInicial == 0 && $saldoFinal == 0 && ($movs['entradas'] ?? 0) == 0 && ($movs['saidas'] ?? 0) == 0) continue;

            $resultados[] = [
                'conta' => $conta['nome'],
                'saldo_inicial' => $saldoInicial,
                'entradas' => $movs['entradas'] ?? 0,
                'saidas' => $movs['saidas'] ?? 0,
                'saldo_final' => $saldoFinal
            ];
        }
        return $resultados;
    }

    private function getRentabilidadeOSVendas($mes, $ano, $tipo) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(id) as qtd,
                SUM(valor_total) as faturado,
                IFNULL(AVG(valor_total), 0) as ticket_medio
            FROM item_movimentacao
            WHERE tipo = ? AND MONTH(data) = ? AND YEAR(data) = ?
        ");
        $stmt->execute([$tipo, $mes, $ano]);
        $dados = $stmt->fetch();
        
        $stmtCustos = $this->db->prepare("
            SELECT SUM(co.valor) as total_custos
            FROM custos_operacionais co
            JOIN item_movimentacao im ON co.item_movimentacao_id = im.id
            WHERE im.tipo = ? AND MONTH(im.data) = ? AND YEAR(im.data) = ?
        ");
        $stmtCustos->execute([$tipo, $mes, $ano]);
        $custos = $stmtCustos->fetch();

        $faturado = $dados['faturado'] ?? 0;
        $total_custos = $custos['total_custos'] ?? 0;
        
        return [
            'qtd' => $dados['qtd'] ?? 0,
            'faturado' => $faturado,
            'ticket_medio' => $dados['ticket_medio'] ?? 0,
            'custos' => $total_custos,
            'lucro_bruto' => $faturado - $total_custos
        ];
    }

    private function getPerformanceFuncionario($mes, $ano) {
        $stmt = $this->db->prepare("
            SELECT 
                f.nome as funcionario, 
                COUNT(DISTINCT m.item_movimentacao_id) as qtd, 
                SUM(m.valor) as valor_total
            FROM movimentacoes m
            JOIN funcionarios f ON m.funcionario_id = f.id
            WHERE m.tipo = 'entrada' AND m.status = 'ativa' AND m.item_movimentacao_id IS NOT NULL AND MONTH(m.data_movimentacao) = ? AND YEAR(m.data_movimentacao) = ?
            GROUP BY f.id
            ORDER BY valor_total DESC
        ");
        $stmt->execute([$mes, $ano]);
        return $stmt->fetchAll();
    }

    private function getMovimentacaoDiaria($mes, $ano) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE(data_movimentacao) as dia,
                SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END) as entradas,
                SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END) as saidas
            FROM movimentacoes
            WHERE status = 'ativa' AND MONTH(data_movimentacao) = ? AND YEAR(data_movimentacao) = ?
            GROUP BY DATE(data_movimentacao)
            ORDER BY DATE(data_movimentacao) ASC
        ");
        $stmt->execute([$mes, $ano]);
        $diario = $stmt->fetchAll();

        foreach($diario as &$d) {
            $diaDate = $d['dia'];
            $stmtSaldo = $this->db->prepare("
                SELECT SUM(cs.saldo_final) as saldo_fechamento
                FROM caixa_saldos cs
                JOIN caixas c ON cs.caixa_id = c.id
                WHERE c.data_operacao = ?
            ");
            $stmtSaldo->execute([$diaDate]);
            $saldo = $stmtSaldo->fetch();
            
            $d['resultado'] = $d['entradas'] - $d['saidas'];
            $d['saldo_fechamento'] = $saldo['saldo_fechamento'] ?? 0;
        }
        return $diario;
    }
}
