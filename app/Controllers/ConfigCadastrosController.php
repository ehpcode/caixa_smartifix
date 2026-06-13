<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Fornecedor;
use App\Models\Funcionario;

class ConfigCadastrosController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('cadastros:gerenciar');
    }

    public function fornecedores() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        $fModel = new Fornecedor();
        $this->view('layouts/main', [
            'title' => 'Fornecedores',
            'contentView' => 'config/fornecedores',
            'fornecedores' => $fModel->getAll()
        ]);
    }

    public function store_fornecedor() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ((new Fornecedor())->inserir($_POST['nome'], $_SESSION['usuario_id'])) {
                $_SESSION['msg_sucesso'] = "Fornecedor cadastrado com sucesso!";
            }
        }
        $this->redirect('/config/fornecedores');
    }

    public function funcionarios() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        $fModel = new Funcionario();
        $this->view('layouts/main', [
            'title' => 'Funcionários',
            'contentView' => 'config/funcionarios',
            'funcionarios' => $fModel->getAll()
        ]);
    }

    public function store_funcionario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Funcionario();
            if(!empty($_POST['id'])) {
                if ($model->atualizar($_POST['id'], $_POST['nome'], $_POST['cargo'])) $_SESSION['msg_sucesso'] = "Funcionário atualizado com sucesso!";
            } else {
                if ($model->inserir($_POST['nome'], $_POST['cargo'])) $_SESSION['msg_sucesso'] = "Funcionário cadastrado com sucesso!";
            }
        }
        $this->redirect('/config/funcionarios');
    }

    public function toggle_funcionario($id) {
        if ((new Funcionario())->toggleAtivo($id)) $_SESSION['msg_sucesso'] = "Status do funcionário alterado com sucesso!";
        $this->redirect('/config/funcionarios');
    }
}
