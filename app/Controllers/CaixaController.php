<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Caixa;

class CaixaController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('caixa:visualizar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/');
        }
        
        $caixaModel = new Caixa();
        $caixaAbertoHoje = $caixaModel->getCaixaAbertoHoje();
        $caixaAnteriorAberto = $caixaModel->getCaixaAnteriorAberto();
        $isPrimeiroUso = $caixaModel->isPrimeiroUso();

        $qualquerCaixaAberto = ($caixaAbertoHoje || $caixaAnteriorAberto);
        
        if ($qualquerCaixaAberto) {
            $caixaAtual = $caixaAbertoHoje ?: $caixaAnteriorAberto;
            $data_visualizacao = $caixaAtual['data_operacao'];
            $podeNavegar = false;
        } else {
            $data_visualizacao = $_GET['data'] ?? date('Y-m-d');
            $caixaAtual = $caixaModel->getByData($data_visualizacao);
            $podeNavegar = true;
        }

        // Para manter compatibilidade com a view atual (que checa se está aberto)
        $caixaAberto = ($caixaAtual && in_array($caixaAtual['status'], ['aberto', 'reaberto'])) ? $caixaAtual : false;
        
        $contaModel = new \App\Models\ContaFinanceira();
        $contas = $contaModel->getAllAtivas();

        $movimentacoes = [];
        $funcionarios = [];
        $naturezas = [];
        $formas = [];
        $fornecedores = [];
        $os_cadastradas = [];
        $vendas_cadastradas = [];
        $saldos_fechamento = [];

        // Se o caixa atual estiver fechado, mas queremos mostrar as movimentações dele:
        // A view original só carrega isso se $caixaAberto for true. Mas se $caixaAtual existe (fechado), precisamos mostrar as movs (sem botões de ação).
        if ($caixaAtual) {
            $movModel = new \App\Models\Movimentacao();
            $movimentacoes = $movModel->getDoCaixa($caixaAtual['id']);
            
            $naturezaModel = new \App\Models\NaturezaFinanceira();
            $naturezas = $naturezaModel->getAllAtivas();
            
            $formaModel = new \App\Models\FormaPagamento();
            $formas = $formaModel->getAllAtivas();
            
            $funcModel = new \App\Models\Funcionario();
            $funcionarios = $funcModel->getAllAtivos();

            $fornModel = new \App\Models\Fornecedor();
            $fornecedores = $fornModel->getAll();

            $itemModel = new \App\Models\ItemMovimentacao();
            $os_cadastradas = $itemModel->getFiltered(['tipo' => 'os']);
            $vendas_cadastradas = $itemModel->getFiltered(['tipo' => 'venda']);

            $dashModel = new \App\Models\Dashboard();
            $saldos_fechamento = $dashModel->getSaldosContasDia($caixaAtual['data_operacao']);
        }

        $this->view('layouts/main', [
            'title' => 'Controle de Caixa',
            'contentView' => 'caixa/index',
            'data_visualizacao' => $data_visualizacao,
            'podeNavegar' => $podeNavegar,
            'data_primeiro_caixa' => $caixaModel->getPrimeiraData(),
            'caixaAtual' => $caixaAtual,
            'caixaAberto' => $caixaAberto,
            'caixaAnteriorAberto' => $caixaAnteriorAberto,
            'isPrimeiroUso' => $isPrimeiroUso,
            'contas' => $contas,
            'movimentacoes' => $movimentacoes,
            'funcionarios' => $funcionarios,
            'naturezas' => $naturezas,
            'formas' => $formas,
            'fornecedores' => $fornecedores,
            'os_cadastradas' => $os_cadastradas ?? [],
            'vendas_cadastradas' => $vendas_cadastradas ?? [],
            'saldos_fechamento' => $saldos_fechamento ?? [],
            'saldos_fechamento_anterior' => $saldos_fechamento_anterior ?? []
        ]);
    }

    public function abrir() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/');
        }
        $this->verificarPermissao('caixa:abrir');
        $saldos = $_POST['saldos'] ?? [];
        // Converte valores formatados como "1.200,50" para "1200.50"
        foreach ($saldos as $k => $v) {
            $saldos[$k] = str_replace(',', '.', str_replace('.', '', $v));
        }
        
        $caixaModel = new Caixa();
        
        if ($caixaModel->getCaixaAnteriorAberto()) {
            $_SESSION['msg_erro'] = "Existe um caixa de dia anterior aberto. Feche-o antes de abrir um novo.";
            $this->redirect('/caixa');
            return;
        }

        if ($caixaModel->abrir($_SESSION['usuario_id'], $saldos)) {
            $_SESSION['msg_sucesso'] = "Caixa aberto com sucesso!";
        } else {
            $_SESSION['msg_erro'] = "Não foi possível abrir o caixa. Pode já existir um caixa aberto hoje.";
        }
        $this->redirect('/caixa');
    }

    public function fechar() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/');
        }
        $this->verificarPermissao('caixa:fechar');
        $caixaId = $_POST['caixa_id'] ?? null;
        $observacao = !empty($_POST['observacao']) ? trim($_POST['observacao']) : 'Fechamento diário padrão';
        if ($caixaId) {
            $caixaModel = new Caixa();
            if ($caixaModel->fechar($caixaId, $_SESSION['usuario_id'], $observacao)) {
                $_SESSION['msg_sucesso'] = "Caixa fechado com sucesso!";
            } else {
                $_SESSION['msg_erro'] = "Erro ao fechar o caixa.";
            }
        } else {
            $_SESSION['msg_erro'] = "ID do caixa não informado.";
        }
        $this->redirect('/caixa');
    }

    public function reabrir() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/');
        }
        $this->verificarPermissao('caixa:reabrir');
        
        $caixaId = $_POST['caixa_id'] ?? null;
        $justificativa = trim($_POST['justificativa'] ?? '');

        if (!$caixaId || empty($justificativa)) {
            $_SESSION['msg_erro'] = "Dados inválidos para reabertura.";
            $this->redirect('/caixa');
            return;
        }

        $caixaModel = new Caixa();
        $caixa = $caixaModel->getById($caixaId);

        if (!$caixa || $caixa['status'] != 'fechado') {
            $_SESSION['msg_erro'] = "Caixa não encontrado ou não está fechado.";
            $this->redirect('/caixa');
            return;
        }

        // Verifica limite de 3 dias
        $dataCaixa = strtotime($caixa['data_operacao']);
        $hoje = strtotime(date('Y-m-d'));
        $diasPassados = floor(($hoje - $dataCaixa) / (60 * 60 * 24));

        if ($diasPassados > 3) {
            $_SESSION['msg_erro'] = "Não é permitido reabrir caixas com mais de 3 dias de fechamento.";
            $this->redirect('/caixa');
            return;
        }

        // Verifica se há OUTRO caixa aberto
        $caixaAbertoHoje = $caixaModel->getCaixaAbertoHoje();
        $caixaAnteriorAberto = $caixaModel->getCaixaAnteriorAberto();
        
        if ($caixaAbertoHoje || $caixaAnteriorAberto) {
            $_SESSION['msg_erro'] = "Você não pode reabrir um caixa enquanto houver outro caixa em aberto no sistema.";
            $this->redirect('/caixa');
            return;
        }

        if ($caixaModel->reabrir($caixaId, $_SESSION['usuario_id'], $justificativa)) {
            $_SESSION['msg_sucesso'] = "Caixa reaberto com sucesso!";
        } else {
            $_SESSION['msg_erro'] = "Erro ao reabrir o caixa.";
        }

        $this->redirect('/caixa');
    }

    public function saldoConta() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(403);
            echo json_encode(['erro' => 'Não autorizado']);
            return;
        }

        $contaId = $_GET['conta_id'] ?? null;
        if (!$contaId) {
            echo json_encode(['saldo' => 0]);
            return;
        }

        $caixaModel = new Caixa();
        $caixaAberto = $caixaModel->getCaixaAbertoHoje();

        if (!$caixaAberto) {
            echo json_encode(['saldo' => 0]);
            return;
        }

        $caixaId = $caixaAberto['id'];

        // Buscar saldo inicial
        $stmt = $caixaModel->db->prepare("SELECT saldo_inicial FROM caixa_saldos WHERE caixa_id = ? AND conta_financeira_id = ?");
        $stmt->execute([$caixaId, $contaId]);
        $saldo = $stmt->fetch();
        $saldoInicial = $saldo ? floatval($saldo['saldo_inicial']) : 0;

        // Buscar entradas e saídas
        $stmtMov = $caixaModel->db->prepare("
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

        $saldoAtual = $saldoInicial + $entradas - $saidas;

        echo json_encode(['saldo' => $saldoAtual]);
    }
}
