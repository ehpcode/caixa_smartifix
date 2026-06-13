<?php
namespace App\Models;
use App\Core\Model;

class Caixa extends Model {
    public function getCaixaAbertoHoje() {
        $hoje = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT * FROM caixas WHERE data_operacao = ? AND status IN ('aberto', 'reaberto')");
        $stmt->execute([$hoje]);
        return $stmt->fetch();
    }

    public function getCaixaAtual() {
        $stmt = $this->db->query("SELECT * FROM caixas WHERE status IN ('aberto', 'reaberto') LIMIT 1");
        return $stmt->fetch();
    }

    public function getCaixaAnteriorAberto() {
        $hoje = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT * FROM caixas WHERE data_operacao < ? AND status IN ('aberto', 'reaberto') ORDER BY data_operacao ASC LIMIT 1");
        $stmt->execute([$hoje]);
        return $stmt->fetch();
    }

    public function getByData($data) {
        $stmt = $this->db->prepare("SELECT * FROM caixas WHERE data_operacao = ?");
        $stmt->execute([$data]);
        return $stmt->fetch();
    }

    public function getPrimeiraData() {
        $stmt = $this->db->query("SELECT MIN(data_operacao) as primeira_data FROM caixas");
        $row = $stmt->fetch();
        return $row['primeira_data'] ?? date('Y-m-d');
    }

    public function isPrimeiroUso() {
        $stmt = $this->db->query("SELECT COUNT(*) as qtd FROM caixa_saldos");
        return $stmt->fetch()['qtd'] == 0;
    }

    public function abrir($usuarioId, $saldos_manuais = []) {
        $hoje = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT id FROM caixas WHERE data_operacao = ?");
        $stmt->execute([$hoje]);
        if ($stmt->fetch()) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO caixas (data_operacao, status, aberto_por, aberto_em) VALUES (?, 'aberto', ?, NOW())");
        if ($stmt->execute([$hoje, $usuarioId])) {
            $caixaId = $this->db->lastInsertId();
            $isPrimeiroUso = $this->isPrimeiroUso();
            
            $stmtConta = $this->db->query("SELECT id FROM contas_financeiras WHERE ativo = 1");
            $contas = $stmtConta->fetchAll();
            
            foreach ($contas as $c) {
                $contaId = $c['id'];
                $saldo_inicial = 0;
                
                if ($isPrimeiroUso) {
                    $saldo_inicial = $saldos_manuais[$contaId] ?? 0;
                } else {
                    // Pega do último fechamento da conta
                    $stmtUltimo = $this->db->prepare("
                        SELECT saldo_final FROM caixa_saldos 
                        WHERE conta_financeira_id = ? 
                        ORDER BY id DESC LIMIT 1
                    ");
                    $stmtUltimo->execute([$contaId]);
                    $ultimo = $stmtUltimo->fetch();
                    $saldo_inicial = $ultimo ? $ultimo['saldo_final'] : 0;
                }
                
                $stmtSaldo = $this->db->prepare("INSERT INTO caixa_saldos (caixa_id, conta_financeira_id, saldo_inicial, saldo_final) VALUES (?, ?, ?, ?)");
                $stmtSaldo->execute([$caixaId, $contaId, $saldo_inicial, $saldo_inicial]);
            }
            return true;
        }
        return false;
    }
    
    public function fechar($id, $usuarioId, $observacao = '') {
        $caixa = $this->getById($id);
        $dataCaixa = $caixa['data_operacao'];

        // Garante a integridade dos saldos recalculando a partir deste caixa,
        // em cascata para todos os caixas posteriores.
        $stmtCaixas = $this->db->prepare("SELECT id FROM caixas WHERE data_operacao >= ? ORDER BY data_operacao ASC");
        $stmtCaixas->execute([$dataCaixa]);
        $caixas = $stmtCaixas->fetchAll();

        $contas = $this->db->query("SELECT id FROM contas_financeiras")->fetchAll();

        foreach ($contas as $conta) {
            $contaId = $conta['id'];
            $saldoFinalAnterior = null;

            foreach ($caixas as $c) {
                $caixaId = $c['id'];

                $stmtCs = $this->db->prepare("SELECT saldo_inicial FROM caixa_saldos WHERE caixa_id = ? AND conta_financeira_id = ?");
                $stmtCs->execute([$caixaId, $contaId]);
                $cs = $stmtCs->fetch();

                if (!$cs) continue; // Se não houver registro para a conta neste dia, ignora

                $novoSaldoInicial = ($saldoFinalAnterior !== null) ? $saldoFinalAnterior : floatval($cs['saldo_inicial']);

                $stmtMov = $this->db->prepare("
                    SELECT 
                        SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END) as entradas,
                        SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END) as saidas
                    FROM movimentacoes
                    WHERE caixa_id = ? AND conta_financeira_id = ? AND status = 'ativa'
                ");
                $stmtMov->execute([$caixaId, $contaId]);
                $movs = $stmtMov->fetch();
                $entradas = $movs ? floatval($movs['entradas']) : 0;
                $saidas = $movs ? floatval($movs['saidas']) : 0;

                $novoSaldoFinal = $novoSaldoInicial + $entradas - $saidas;

                $stmtUpd = $this->db->prepare("UPDATE caixa_saldos SET saldo_inicial = ?, saldo_final = ? WHERE caixa_id = ? AND conta_financeira_id = ?");
                $stmtUpd->execute([$novoSaldoInicial, $novoSaldoFinal, $caixaId, $contaId]);

                $saldoFinalAnterior = $novoSaldoFinal;
            }
        }

        $stmt = $this->db->prepare("UPDATE caixas SET status = 'fechado', fechado_por = ?, fechado_em = NOW(), observacao = ? WHERE id = ?");
        return $stmt->execute([$usuarioId, $observacao, $id]);
    }

    public function reabrir($id, $usuarioId, $justificativa) {
        $stmt = $this->db->prepare("UPDATE caixas SET status = 'reaberto' WHERE id = ?");
        if ($stmt->execute([$id])) {
            $stmtReabertura = $this->db->prepare("INSERT INTO reaberturas_caixa (caixas_id, reaberto_por, reaberto_em, justificativa) VALUES (?, ?, NOW(), ?)");
            $stmtReabertura->execute([$id, $usuarioId, $justificativa]);
            
            // Insert audit manually if needed
            $stmtAudit = $this->db->prepare("INSERT INTO logs_auditoria (usuarios_id, tabela_afetada, registro_id, operacao, dados_novos) VALUES (?, 'caixas', ?, 'reabertura_caixa', ?)");
            $stmtAudit->execute([$usuarioId, $id, json_encode(['justificativa' => $justificativa])]);

            return true;
        }
        return false;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM caixas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
