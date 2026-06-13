<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\ItemMovimentacao;

class OSController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('os:visualizar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $filtros = [
            'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'),
            'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),
            'tipo' => $_GET['tipo'] ?? ''
        ];
        
        if (empty($filtros['tipo'])) {
            $filtros['exclude_tipo'] = 'venda';
        }

        $itemModel = new ItemMovimentacao();
        $itens = $itemModel->getFiltered($filtros);
        
        $fornecedorModel = new \App\Models\Fornecedor();
        $fornecedores = $fornecedorModel->getAll();

        $this->view('layouts/main', [
            'title' => 'Ordens de Serviço',
            'contentView' => 'os/index',
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
            'numero_os' => $_POST['numero_os'] ?? '',
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

                        $custoDados = [
                            'item_movimentacao_id' => $id,
                            'descricao' => $desc,
                            'tipo' => $tipoCusto,
                            'fornecedor_id' => $fornId,
                            'valor' => $cVal,
                            'criado_por' => $_SESSION['usuario_id']
                        ];

                        if (!empty($custoId)) {
                            // Atualizar
                            $custoModel->atualizar($custoId, $custoDados);
                            $idsMantidos[] = $custoId;
                        } else {
                            // Inserir novo
                            $custoModel->inserir($custoDados);
                            $idsMantidos[] = $custoModel->getDb()->lastInsertId();
                        }
                    }
                }
            }

            // Deletar os ausentes
            $custoModel->deletarAusentes($id, $idsMantidos);

            $_SESSION['msg_sucesso'] = "Ordem de Serviço atualizada com sucesso.";
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Erro ao atualizar no banco de dados.']);
        }
    }
}
