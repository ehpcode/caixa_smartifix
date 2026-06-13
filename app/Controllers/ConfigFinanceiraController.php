<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\ContaFinanceira;
use App\Models\FormaPagamento;
use App\Models\NaturezaFinanceira;

class ConfigFinanceiraController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('cadastros:gerenciar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $cModel = new ContaFinanceira();
        $fModel = new FormaPagamento();
        $nModel = new NaturezaFinanceira();

        $this->view('layouts/main', [
            'title' => 'Cadastros Financeiros',
            'contentView' => 'config/financeiro',
            'contas' => $cModel->getAll(),
            'formas' => $fModel->getAll(),
            'naturezas' => $nModel->getAll()
        ]);
    }

    public function store_conta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new ContaFinanceira();
            if (!empty($_POST['id'])) {
                if ($model->atualizar($_POST['id'], $_POST['nome'], $_POST['tipo'])) $_SESSION['msg_sucesso'] = "Conta atualizada com sucesso!";
            } else {
                if ($model->inserir($_POST['nome'], $_POST['tipo'])) $_SESSION['msg_sucesso'] = "Conta criada com sucesso!";
            }
        }
        $this->redirect('/config/financeiro');
    }
    public function toggle_conta($id) {
        if ((new ContaFinanceira())->toggleAtivo($id)) $_SESSION['msg_sucesso'] = "Status da conta alterado com sucesso!";
        $this->redirect('/config/financeiro');
    }

    public function store_forma() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new FormaPagamento();
            if (!empty($_POST['id'])) {
                if ($model->atualizar($_POST['id'], $_POST['nome'])) $_SESSION['msg_sucesso'] = "Forma de pagamento atualizada com sucesso!";
            } else {
                if ($model->inserir($_POST['nome'])) $_SESSION['msg_sucesso'] = "Forma de pagamento criada com sucesso!";
            }
        }
        $this->redirect('/config/financeiro');
    }
    public function toggle_forma($id) {
        if ((new FormaPagamento())->toggleAtivo($id)) $_SESSION['msg_sucesso'] = "Status alterado com sucesso!";
        $this->redirect('/config/financeiro');
    }

    public function store_natureza() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new NaturezaFinanceira();
            if (!empty($_POST['id'])) {
                if ($model->atualizar($_POST['id'], $_POST['nome'], $_POST['tipo'], $_POST['categoria_base'] ?? 'outro')) $_SESSION['msg_sucesso'] = "Natureza atualizada com sucesso!";
            } else {
                if ($model->inserir($_POST['nome'], $_POST['tipo'], $_POST['categoria_base'] ?? 'outro')) $_SESSION['msg_sucesso'] = "Natureza criada com sucesso!";
            }
        }
        $this->redirect('/config/financeiro');
    }
    public function toggle_natureza($id) {
        if ((new NaturezaFinanceira())->toggleAtivo($id)) $_SESSION['msg_sucesso'] = "Status alterado com sucesso!";
        $this->redirect('/config/financeiro');
    }
}
