<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Caixa;
use App\Models\Movimentacao;
use App\Models\ContaFinanceira;
use App\Models\NaturezaFinanceira;
use App\Models\FormaPagamento;

class MovimentacaoController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('movimentacao:visualizar_todas');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $caixaModel = new Caixa();
        $caixaAberto = $caixaModel->getCaixaAtual();

        // Advanced filter data
        $filtros = [
            'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'),
            'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),
            'tipo' => $_GET['tipo'] ?? '',
            'conta' => $_GET['conta'] ?? ''
        ];

        $movModel = new Movimentacao();
        $movimentacoes = $movModel->getFiltered($filtros);

        $contaModel = new \App\Models\ContaFinanceira();
        $contas = $contaModel->getAllAtivas();

        $funcionarios = [];
        $naturezas = [];
        $formas = [];
        $fornecedores = [];

        if ($caixaAberto) {
            $naturezaModel = new \App\Models\NaturezaFinanceira();
            $naturezas = $naturezaModel->getAllAtivas();
            
            $formaModel = new \App\Models\FormaPagamento();
            $formas = $formaModel->getAllAtivas();
            
            $stmtFunc = $caixaModel->db->query("SELECT id, nome FROM funcionarios WHERE ativo = 1");
            $funcionarios = $stmtFunc->fetchAll();

            $fornModel = new \App\Models\Fornecedor();
            $fornecedores = $fornModel->getAll();

            // Buscar OS Cadastradas
            $itemModel = new \App\Models\ItemMovimentacao();
            $os_cadastradas = $itemModel->getFiltered(['tipo' => 'os']);
            $vendas_cadastradas = $itemModel->getFiltered(['tipo' => 'venda']);
        }

        $this->view('layouts/main', [
            'title' => 'Histórico de Movimentações',
            'contentView' => 'movimentacoes/index',
            'caixaAberto' => $caixaAberto,
            'movimentacoes' => $movimentacoes,
            'filtros' => $filtros,
            'contas' => $contas,
            'funcionarios' => $funcionarios,
            'naturezas' => $naturezas,
            'formas' => $formas,
            'fornecedores' => $fornecedores,
            'os_cadastradas' => $os_cadastradas ?? [],
            'vendas_cadastradas' => $vendas_cadastradas ?? []
        ]);
    }

    public function create() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $caixaModel = new Caixa();
        $caixaAberto = $caixaModel->getCaixaAtual();

        if (!$caixaAberto) {
            $this->redirect('/movimentacoes');
        }

        $contaModel = new ContaFinanceira();
        $naturezaModel = new NaturezaFinanceira();
        $formaModel = new FormaPagamento();

        $this->view('layouts/main', [
            'title' => 'Nova Movimentação',
            'contentView' => 'movimentacoes/create',
            'caixaAberto' => $caixaAberto,
            'contas' => $contaModel->getAllAtivas(),
            'naturezasEntrada' => $naturezaModel->getByTipo('entrada'),
            'naturezasSaida' => $naturezaModel->getByTipo('saida'),
            'formasPagamento' => $formaModel->getAllAtivas()
        ]);
    }

    public function store() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }
        $this->verificarPermissao('movimentacao:criar');
        $caixaModel = new Caixa();
        $caixaAberto = $caixaModel->getCaixaAtual();

        if ($caixaAberto && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_movimento = $_POST['tipo_movimento']; // 'entrada' ou 'saida'
            
            $valorStr = str_replace('.', '', $_POST['valor'] ?? '0');
            $valorStr = str_replace(',', '.', $valorStr);
            $valor = floatval($valorStr);

            $item_id = null;
            $eh_parcela = isset($_POST['eh_parcela']) ? 1 : 0;
            $funcionario_id = !empty($_POST['funcionario_id']) ? $_POST['funcionario_id'] : null;

            $item_tipo = $_POST['item_tipo'] ?? 'simples';
            
            if (in_array($item_tipo, ['os', 'venda', 'servico_avulso'])) {
                $valTotStr = str_replace('.', '', $_POST['valor_total_item'] ?? '0');
                $valTotStr = str_replace(',', '.', $valTotStr);
                $valTot = floatval($valTotStr);
                
                $itemModel = new \App\Models\ItemMovimentacao();
                
                $numeroOs = !empty($_POST['numero_os']) ? $_POST['numero_os'] : null;
                $itemExistente = null;
                
                if ($item_tipo === 'os' && $numeroOs) {
                    $itemExistente = $itemModel->getByNumeroOs($numeroOs);
                } else if ($item_tipo === 'venda' && !empty($_POST['venda_id'])) {
                    $itemExistente = $itemModel->getById($_POST['venda_id']);
                }

                if ($itemExistente) {
                    $item_id = $itemExistente['id'];
                } else {
                    $item_id = $itemModel->inserir([
                        'tipo' => $item_tipo,
                        'numero_os' => $numeroOs,
                        'valor_total' => $valTot > 0 ? $valTot : 0, // se saida, maybe valor_total is not valor
                        'descricao' => $_POST['descricao'],
                        'item' => $_POST['item'] ?? '',
                        'cliente' => $_POST['cliente'] ?? '',
                        'criado_por' => $_SESSION['usuario_id']
                    ]);
                }

                    // Inserir Custos Dinâmicos se existirem
                    if (!empty($_POST['custo_descricao']) && is_array($_POST['custo_descricao'])) {
                        $custoModel = new \App\Models\CustoOperacional();
                        foreach ($_POST['custo_descricao'] as $i => $desc) {
                            if (!empty($desc) && !empty($_POST['custo_valor'][$i])) {
                                $cValStr = str_replace('.', '', $_POST['custo_valor'][$i]);
                                $cValStr = str_replace(',', '.', $cValStr);
                                $cVal = floatval($cValStr);
                                
                                $fornId = (!empty($_POST['custo_fornecedor'][$i]) && $_POST['custo_tipo'][$i] === 'fornecedor') ? $_POST['custo_fornecedor'][$i] : null;
                                $tipoCusto = $_POST['custo_tipo'][$i] ?? 'estoque';

                                $custoModel->inserir([
                                    'item_movimentacao_id' => $item_id,
                                    'descricao' => $desc,
                                    'tipo' => $tipoCusto,
                                    'fornecedor_id' => $fornId,
                                    'valor' => $cVal,
                                    'criado_por' => $_SESSION['usuario_id']
                                ]);
                            }
                        }
                    }
                }

            $dados = [
                'tipo' => $tipo_movimento,
                'caixa_id' => $_POST['caixa_id'],
                'conta_financeira_id' => $_POST['conta_financeira_id'],
                'natureza_financeira_id' => $_POST['natureza_financeira_id'],
                'forma_pagamento_id' => $_POST['forma_pagamento_id'],
                'item_movimentacao_id' => $item_id,
                'valor' => $valor,
                'descricao' => $_POST['descricao'],
                'funcionario_id' => $funcionario_id,
                'eh_parcela' => $eh_parcela,
                'criado_por' => $_SESSION['usuario_id']
            ];

            // Update Saldo da Conta
            $caixaSaldoModel = new \App\Models\Caixa(); // Precisamos atualizar a tabela caixa_saldos
            $operador = $tipo_movimento === 'entrada' ? '+' : '-';
            $stmt = $caixaSaldoModel->db->prepare("UPDATE caixa_saldos SET saldo_final = saldo_final {$operador} ? WHERE caixa_id = ? AND conta_financeira_id = ?");
            $stmt->execute([$valor, $_POST['caixa_id'], $_POST['conta_financeira_id']]);

            $movModel = new Movimentacao();
            if ($movModel->inserir($dados)) {
                $_SESSION['msg_sucesso'] = "Movimentação registrada com sucesso!";
            } else {
                $_SESSION['msg_erro'] = "Erro ao registrar a movimentação.";
            }
        }
        
        // Retornar para a página que originou a requisição (Caixa ou Movimentações)
        $referer = $_SERVER['HTTP_REFERER'] ?? '/movimentacoes';
        $this->redirect(str_replace(BASE_URL, '', $referer));
    }

    public function buscar() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(403);
            echo json_encode(['erro' => 'Não autorizado']);
            return;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $movModel = new Movimentacao();
        $mov = $movModel->getById($id);

        if ($mov) {
            if (!empty($mov['item_movimentacao_id'])) {
                $custoModel = new \App\Models\CustoOperacional();
                $mov['custos'] = $custoModel->getPorItem($mov['item_movimentacao_id']);
            } else {
                $mov['custos'] = [];
            }
            echo json_encode($mov);
        } else {
            echo json_encode(['erro' => 'Movimentação não encontrada']);
        }
    }

    public function update() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }
        $this->verificarPermissao('movimentacao:editar');
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $this->redirect('/movimentacoes');
        }

        $movModel = new Movimentacao();
        $movimentoAntigo = $movModel->getById($id);

        if (!$movimentoAntigo) {
            $this->redirect('/movimentacoes');
        }

        $caixaModel = new Caixa();
        $caixaDaMovimentacao = $caixaModel->getById($movimentoAntigo['caixa_id']);

        if (!$caixaDaMovimentacao || !in_array($caixaDaMovimentacao['status'], ['aberto', 'reaberto'])) {
            // Verifica se tem permissão para editar em caixa fechado? (Apenas caixa aberto no momento ou reaberto permite edição)
            $_SESSION['erro'] = "A movimentação pertence a um caixa já fechado. Reabra o caixa para editá-la.";
            $this->redirect('/movimentacoes');
        }

        $valorStr = str_replace('.', '', $_POST['valor']);
        $valorStr = str_replace(',', '.', $valorStr);
        $valorNovo = floatval($valorStr);

        $contaAntiga = $movimentoAntigo['conta_financeira_id'];
        $contaNova = $_POST['conta_financeira_id'];
        $valorAntigo = $movimentoAntigo['valor'];
        $tipo = $movimentoAntigo['tipo']; // 'entrada' ou 'saida'

        // 1. Estornar valor antigo na conta antiga
        $caixaSaldoModel = new \App\Models\Caixa();
        $operadorEstorno = $tipo === 'entrada' ? '-' : '+';
        $stmtEstorno = $caixaSaldoModel->db->prepare("UPDATE caixa_saldos SET saldo_final = saldo_final {$operadorEstorno} ? WHERE caixa_id = ? AND conta_financeira_id = ?");
        $stmtEstorno->execute([$valorAntigo, $movimentoAntigo['caixa_id'], $contaAntiga]);

        // 2. Aplicar valor novo na conta nova
        $operadorAplicar = $tipo === 'entrada' ? '+' : '-';
        $stmtAplicar = $caixaSaldoModel->db->prepare("UPDATE caixa_saldos SET saldo_final = saldo_final {$operadorAplicar} ? WHERE caixa_id = ? AND conta_financeira_id = ?");
        $stmtAplicar->execute([$valorNovo, $movimentoAntigo['caixa_id'], $contaNova]);

        // Tratar ItemMovimentacao se houver mudança de OS/Venda
        $item_id = null;
        $eh_parcela = isset($_POST['eh_parcela']) ? 1 : 0;
        $item_tipo = $_POST['item_tipo'] ?? 'simples';
        $funcionario_id = !empty($_POST['funcionario_id']) ? $_POST['funcionario_id'] : null;

        if (in_array($item_tipo, ['os', 'venda', 'servico_avulso'])) {
            $itemModel = new \App\Models\ItemMovimentacao();
            $numeroOs = !empty($_POST['numero_os']) ? $_POST['numero_os'] : null;
            $itemExistente = null;
            
            if ($item_tipo === 'os' && $numeroOs) {
                $itemExistente = $itemModel->getByNumeroOs($numeroOs);
            } else if ($item_tipo === 'venda' && !empty($_POST['venda_id'])) {
                $itemExistente = $itemModel->getById($_POST['venda_id']);
            } else if (!empty($movimentoAntigo['item_movimentacao_id'])) {
                $itemExistente = $itemModel->getById($movimentoAntigo['item_movimentacao_id']);
            }

            $valTotStr = str_replace('.', '', $_POST['valor_total_item'] ?? '0');
            $valTotStr = str_replace(',', '.', $valTotStr);
            $valTot = floatval($valTotStr);

            $itemDados = [
                'tipo' => $item_tipo,
                'numero_os' => $numeroOs,
                'valor_total' => $valTot > 0 ? $valTot : 0,
                'descricao' => $_POST['descricao'],
                'item' => $_POST['item'] ?? '',
                'cliente' => $_POST['cliente'] ?? ''
            ];

            if ($itemExistente) {
                $item_id = $itemExistente['id'];
                $itemModel->atualizar($item_id, $itemDados);
            } else {
                $itemDados['criado_por'] = $_SESSION['usuario_id'];
                $item_id = $itemModel->inserir($itemDados);
            }
            
            // Tratar custos (inserir, atualizar, deletar)
            if ($item_id) {
                $custoModel = new \App\Models\CustoOperacional();
                $idsMantidos = [];

                if (!empty($_POST['custo_descricao']) && is_array($_POST['custo_descricao'])) {
                    foreach ($_POST['custo_descricao'] as $i => $desc) {
                        if (!empty($desc) && !empty($_POST['custo_valor'][$i])) {
                            $cValStr = str_replace('.', '', $_POST['custo_valor'][$i]);
                            $cValStr = str_replace(',', '.', $cValStr);
                            $cVal = floatval($cValStr);
                            $fornId = (!empty($_POST['custo_fornecedor'][$i]) && $_POST['custo_tipo'][$i] === 'fornecedor') ? $_POST['custo_fornecedor'][$i] : null;
                            $tipoCusto = $_POST['custo_tipo'][$i] ?? 'estoque';
                            $custoId = $_POST['custo_id'][$i] ?? null;

                            if ($custoId) {
                                // Atualiza
                                $custoModel->db->prepare("UPDATE custos_operacionais SET descricao=?, tipo=?, fornecedor_id=?, valor=? WHERE id=?")
                                    ->execute([$desc, $tipoCusto, $fornId, $cVal, $custoId]);
                                $idsMantidos[] = $custoId;
                            } else {
                                // Insere
                                $custoModel->inserir([
                                    'item_movimentacao_id' => $item_id,
                                    'descricao' => $desc,
                                    'tipo' => $tipoCusto,
                                    'fornecedor_id' => $fornId,
                                    'valor' => $cVal,
                                    'criado_por' => $_SESSION['usuario_id']
                                ]);
                                $idsMantidos[] = $custoModel->db->lastInsertId();
                            }
                        }
                    }
                }
                
                // Remove custos deletados
                if (count($idsMantidos) > 0) {
                    $in = str_repeat('?,', count($idsMantidos) - 1) . '?';
                    $stmtDel = $custoModel->db->prepare("DELETE FROM custos_operacionais WHERE item_movimentacao_id = ? AND id NOT IN ($in)");
                    $paramsDel = array_merge([$item_id], $idsMantidos);
                    $stmtDel->execute($paramsDel);
                } else {
                    $custoModel->db->prepare("DELETE FROM custos_operacionais WHERE item_movimentacao_id = ?")->execute([$item_id]);
                }
            }
        }

        $dadosAtualizacao = [
            'conta_financeira_id' => $contaNova,
            'natureza_financeira_id' => $_POST['natureza_financeira_id'],
            'forma_pagamento_id' => $_POST['forma_pagamento_id'],
            'item_movimentacao_id' => $item_id,
            'valor' => $valorNovo,
            'descricao' => $_POST['descricao'],
            'funcionario_id' => $funcionario_id,
            'eh_parcela' => $eh_parcela
        ];

        if ($movModel->atualizar($id, $dadosAtualizacao)) {
            $_SESSION['msg_sucesso'] = "Movimentação atualizada com sucesso!";
        } else {
            $_SESSION['msg_erro'] = "Erro ao atualizar movimentação.";
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '/movimentacoes';
        $this->redirect(str_replace(BASE_URL, '', $referer));
    }

    public function cancelar() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/');
        }
        $this->verificarPermissao('movimentacao:cancelar');
        // Implementar a lógica de cancelar/estornar permanentemente
        $this->redirect('/movimentacoes');
    }

    public function sangria() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        $this->verificarPermissao('sangria:criar');
        $caixaModel = new Caixa();
        $caixaAberto = $caixaModel->getCaixaAtual();

        if ($caixaAberto && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $origem = $_POST['conta_origem_id'];
            $destino = $_POST['conta_destino_id'];
            
            $valorStr = str_replace('.', '', $_POST['valor']);
            $valorStr = str_replace(',', '.', $valorStr);
            $valor = floatval($valorStr);
            
            if ($origem != $destino && $valor > 0) {
                // Pegar ou Criar Natureza "Sangria"
                $stmtNat = $caixaModel->db->query("SELECT id FROM naturezas_financeiras WHERE categoria_base = 'sangria' LIMIT 1");
                $natSangria = $stmtNat->fetch();
                if (!$natSangria) {
                    $caixaModel->db->query("INSERT INTO naturezas_financeiras (categoria_base, nome, ativo) VALUES ('sangria', 'Sangria / Transferência', 1)");
                    $natId = $caixaModel->db->lastInsertId();
                } else {
                    $natId = $natSangria['id'];
                }

                // Forma PGTO Dinheiro/Transferencia genérica, pegamos a primeira
                $stmtForma = $caixaModel->db->query("SELECT id FROM formas_pagamento LIMIT 1");
                $formaId = $stmtForma->fetch()['id'];

                $movModel = new Movimentacao();
                
                // Saída
                $movModel->inserir([
                    'tipo' => 'saida', 'caixa_id' => $_POST['caixa_id'], 'conta_financeira_id' => $origem,
                    'natureza_financeira_id' => $natId, 'forma_pagamento_id' => $formaId,
                    'valor' => $valor, 'descricao' => 'Sangria para outra conta', 'criado_por' => $_SESSION['usuario_id']
                ]);
                $caixaModel->db->prepare("UPDATE caixa_saldos SET saldo_final = saldo_final - ? WHERE caixa_id = ? AND conta_financeira_id = ?")->execute([$valor, $_POST['caixa_id'], $origem]);

                // Entrada
                $movModel->inserir([
                    'tipo' => 'entrada', 'caixa_id' => $_POST['caixa_id'], 'conta_financeira_id' => $destino,
                    'natureza_financeira_id' => $natId, 'forma_pagamento_id' => $formaId,
                    'valor' => $valor, 'descricao' => 'Recebimento de Sangria', 'criado_por' => $_SESSION['usuario_id']
                ]);
                $caixaModel->db->prepare("UPDATE caixa_saldos SET saldo_final = saldo_final + ? WHERE caixa_id = ? AND conta_financeira_id = ?")->execute([$valor, $_POST['caixa_id'], $destino]);
            }
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '/caixa';
        $this->redirect(str_replace(BASE_URL, '', $referer));
    }
}
