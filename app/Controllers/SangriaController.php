<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Caixa;
use App\Models\Movimentacao;
use App\Models\NaturezaFinanceira;

class SangriaController extends Controller {
    public function store() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        $caixaId = $_POST['caixa_id'] ?? null;
        $contaOrigemId = $_POST['conta_origem_id'] ?? null;
        $contaDestinoId = $_POST['conta_destino_id'] ?? null;
        $valorStr = $_POST['valor'] ?? '0';
        $dataMov = $_POST['data_movimentacao'] ?? date('Y-m-d H:i:s');
        $descricao = $_POST['descricao'] ?? 'Sangria/Transferência entre contas';

        // Tratar valor
        $valorStr = str_replace('.', '', $valorStr);
        $valorStr = str_replace(',', '.', $valorStr);
        $valor = floatval($valorStr);

        // Validações básicas
        if ($valor <= 0) {
            $_SESSION['msg_erro'] = "Valor da sangria deve ser maior que zero.";
            $this->redirect('/caixa');
            return;
        }

        if (!$caixaId || !$contaOrigemId || !$contaDestinoId) {
            $_SESSION['msg_erro'] = "Dados obrigatórios não preenchidos.";
            $this->redirect('/caixa');
            return;
        }
        
        if ($contaOrigemId == $contaDestinoId) {
            $_SESSION['msg_erro'] = "Conta de origem não pode ser a mesma de destino.";
            $this->redirect('/caixa');
            return;
        }

        // RN-SANG-001 - Caixa aberto obrigatório
        $caixaModel = new Caixa();
        $caixaAberto = $caixaModel->getCaixaAbertoHoje();
        if (!$caixaAberto || $caixaAberto['id'] != $caixaId) {
            $_SESSION['msg_erro'] = "Não há um caixa aberto válido para realizar a sangria hoje.";
            $this->redirect('/caixa');
            return;
        }

        // Garantir que existam naturezas de sangria (uma de saída e uma de entrada)
        $natModel = new NaturezaFinanceira();
        $naturezasSangria = $natModel->getByCategoriaBase(NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO);
        
        $natSaidaId = null;
        $natEntradaId = null;
        
        foreach ($naturezasSangria as $ns) {
            if ($ns['tipo'] == 'saida' && !$natSaidaId) $natSaidaId = $ns['id'];
            if ($ns['tipo'] == 'entrada' && !$natEntradaId) $natEntradaId = $ns['id'];
        }
        
        // Se não tiver natureza de saída, cria
        if (!$natSaidaId) {
            $natModel->inserir("Saída por Sangria", "saida", NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO);
            $natSaidaId = $natModel->getDb()->lastInsertId();
        }
        // Se não tiver natureza de entrada, cria
        if (!$natEntradaId) {
            $natModel->inserir("Entrada por Sangria", "entrada", NaturezaFinanceira::CATEGORIA_EXCLUIDA_RESULTADO);
            $natEntradaId = $natModel->getDb()->lastInsertId();
        }

        $formaId = $_POST['forma_pagamento_id'] ?? null;
        if (!$formaId) {
            $formaModel = new \App\Models\FormaPagamento();
            $formas = $formaModel->getAllAtivas();
            $formaId = $formas[0]['id'] ?? 1;
        }

        $movModel = new Movimentacao();
        
        // Iniciar transação seria o ideal, mas vamos inserir a saída primeiro
        $inseridoSaida = $movModel->inserir([
            'tipo' => 'saida',
            'caixa_id' => $caixaId,
            'conta_financeira_id' => $contaOrigemId,
            'natureza_financeira_id' => $natSaidaId,
            'forma_pagamento_id' => $formaId,
            'item_movimentacao_id' => null,
            'valor' => $valor,
            'descricao' => $descricao . ' (Retirada)',
            'data_movimentacao' => $dataMov,
            'criado_por' => $_SESSION['usuario_id'],
            'funcionario_id' => null,
            'eh_parcela' => 0
        ]);
        
        if ($inseridoSaida) {
            // Atualiza o saldo da conta de origem (Saída)
            $caixaModel->db->prepare("UPDATE caixa_saldos SET saldo_final = saldo_final - ? WHERE caixa_id = ? AND conta_financeira_id = ?")->execute([$valor, $caixaId, $contaOrigemId]);

            // Inserir a entrada no destino
            $inseridoEntrada = $movModel->inserir([
                'tipo' => 'entrada',
                'caixa_id' => $caixaId,
                'conta_financeira_id' => $contaDestinoId,
                'natureza_financeira_id' => $natEntradaId,
                'forma_pagamento_id' => $formaId,
                'item_movimentacao_id' => null,
                'valor' => $valor,
                'descricao' => $descricao . ' (Aporte)',
                'data_movimentacao' => $dataMov,
                'criado_por' => $_SESSION['usuario_id'],
                'funcionario_id' => null,
                'eh_parcela' => 0
            ]);
            
            if ($inseridoEntrada) {
                // Atualiza o saldo da conta de destino (Entrada)
                $caixaModel->db->prepare("UPDATE caixa_saldos SET saldo_final = saldo_final + ? WHERE caixa_id = ? AND conta_financeira_id = ?")->execute([$valor, $caixaId, $contaDestinoId]);
                
                // Sincroniza dias futuros caso este seja um caixa reaberto
                if (method_exists($caixaModel, 'sincronizarSaldosPostFechamento')) {
                    $caixaModel->sincronizarSaldosPostFechamento($caixaId);
                }

                $_SESSION['msg_sucesso'] = "Sangria registrada com sucesso.";
            } else {
                $_SESSION['msg_erro'] = "Saída registrada, mas erro ao registrar a entrada no destino.";
            }
        } else {
            $_SESSION['msg_erro'] = "Erro ao registrar a sangria.";
        }

        // Redirecionar
        $referer = $_SERVER['HTTP_REFERER'] ?? '/caixa';
        header("Location: $referer");
        exit;
    }
}
