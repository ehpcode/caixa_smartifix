<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\ItemMovimentacao;

class VendaController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('venda:visualizar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $filtros = [
            'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'),
            'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),
            'tipo' => 'venda'
        ];

        $itemModel = new ItemMovimentacao();
        $itens = $itemModel->getFiltered($filtros);

        $fornModel = new \App\Models\Fornecedor();
        $fornecedores = $fornModel->getAll();

        $this->view('layouts/main', [
            'title' => 'Vendas Realizadas',
            'contentView' => 'vendas/index',
            'itens' => $itens,
            'filtros' => $filtros,
            'fornecedores' => $fornecedores
        ]);
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

        $itemModel = new ItemMovimentacao();
        $item = $itemModel->getById($id);

        if ($item) {
            $custoModel = new \App\Models\CustoOperacional();
            $item['custos'] = $custoModel->getPorItem($id);
            echo json_encode($item);
        } else {
            echo json_encode(['erro' => 'Item não encontrado']);
        }
    }

    public function atualizar() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['sucesso' => false, 'erro' => 'Não autorizado']);
            return;
        }

        // Verifica permissão (se não tiver específica de editar, garantimos por verificação genérica ou de acesso geral)
        $podeEditar = !empty($_SESSION['permissoes']['todas']) || !empty($_SESSION['permissoes']['venda:editar']);
        if (!$podeEditar) {
            http_response_code(403);
            echo json_encode(['sucesso' => false, 'erro' => 'Sem permissão para editar vendas']);
            return;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['sucesso' => false, 'erro' => 'ID não informado']);
            return;
        }

        $valorStr = $_POST['valor_total'] ?? '0';
        $valorStr = str_replace('.', '', $valorStr);
        $valorStr = str_replace(',', '.', $valorStr);
        $valorTotal = floatval($valorStr);

        $dados = [
            'cliente' => $_POST['cliente'] ?? '',
            'item' => $_POST['item'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'valor_total' => $valorTotal
        ];

        $itemModel = new ItemMovimentacao();
        $atualizado = $itemModel->atualizar($id, $dados);

        if ($atualizado) {
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
                            $custoModel->db->prepare("UPDATE custos_operacionais SET descricao=?, tipo=?, fornecedor_id=?, valor=? WHERE id=?")
                                       ->execute([$desc, $tipoCusto, $fornId, $cVal, $custoId]);
                            $idsMantidos[] = $custoId;
                        } else {
                            $custoModel->inserir([
                                'item_movimentacao_id' => $id,
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

            if (count($idsMantidos) > 0) {
                $in = str_repeat('?,', count($idsMantidos) - 1) . '?';
                $stmtDel = $custoModel->db->prepare("DELETE FROM custos_operacionais WHERE item_movimentacao_id = ? AND id NOT IN ($in)");
                $paramsDel = array_merge([$id], $idsMantidos);
                $stmtDel->execute($paramsDel);
            } else {
                $custoModel->db->prepare("DELETE FROM custos_operacionais WHERE item_movimentacao_id = ?")->execute([$id]);
            }

            $_SESSION['msg_sucesso'] = "Venda atualizada com sucesso!";
            $this->redirect('/vendas');
        } else {
            $_SESSION['msg_erro'] = "Erro ao atualizar venda.";
            $this->redirect('/vendas');
        }
    }
}
