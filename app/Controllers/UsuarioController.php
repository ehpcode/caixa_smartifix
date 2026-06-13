<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Usuario;

class UsuarioController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('cadastros:gerenciar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAll();

        $this->view('layouts/main', [
            'title' => 'Gestão de Equipe',
            'contentView' => 'config/usuarios/index',
            'usuarios' => $usuarios
        ]);
    }

    public function create() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $pModel = new \App\Models\PerfilAcesso();
        $perfis = $pModel->getAll();

        $this->view('layouts/main', [
            'title' => 'Novo Usuário',
            'contentView' => 'config/usuarios/form',
            'perfis' => $perfis
        ]);
    }

    public function store() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');
        
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);
        $perfil_id = $_POST['perfil_id'];

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $usuarioModel = new Usuario();
        try {
            if ($usuarioModel->inserir([
                'nome' => $nome,
                'email' => $email,
                'senha' => $senhaHash,
                'perfil_id' => $perfil_id
            ])) {
                $_SESSION['msg_sucesso'] = "Usuário cadastrado com sucesso!";
            } else {
                $_SESSION['msg_erro'] = "Erro ao cadastrar usuário.";
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['msg_erro'] = "O e-mail informado já está em uso.";
            } else {
                $_SESSION['msg_erro'] = "Erro de banco de dados ao cadastrar usuário.";
            }
        }

        $this->redirect('/config/usuarios');
    }

    public function edit($id) {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getById($id);
        
        if (!$usuario) $this->redirect('/config/usuarios');

        $pModel = new \App\Models\PerfilAcesso();
        $perfis = $pModel->getAll();

        $this->view('layouts/main', [
            'title' => 'Editar Funcionário',
            'contentView' => 'config/usuarios/edit',
            'usuario' => $usuario,
            'perfis' => $perfis
        ]);
    }

    public function update($id) {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');
        
        $dados = [
            'nome' => trim($_POST['nome']),
            'email' => trim($_POST['email']),
            'perfil_id' => $_POST['perfil_id'],
            'senha' => !empty($_POST['senha']) ? password_hash(trim($_POST['senha']), PASSWORD_DEFAULT) : null
        ];

        $usuarioModel = new Usuario();
        try {
            if ($usuarioModel->atualizar($id, $dados)) {
                $_SESSION['msg_sucesso'] = "Usuário atualizado com sucesso!";
            } else {
                $_SESSION['msg_erro'] = "Erro ao atualizar usuário.";
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['msg_erro'] = "O e-mail informado já está em uso.";
            } else {
                $_SESSION['msg_erro'] = "Erro de banco de dados ao atualizar usuário.";
            }
        }

        $this->redirect('/config/usuarios');
    }

    public function toggle($id) {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $usuarioModel = new Usuario();
        // Impede de se inativar por acidente
        if ($id != $_SESSION['usuario_id']) {
            if ($usuarioModel->toggleAtivo($id)) {
                $_SESSION['msg_sucesso'] = "Status do usuário alterado com sucesso!";
            } else {
                $_SESSION['msg_erro'] = "Erro ao alterar status do usuário.";
            }
        } else {
            $_SESSION['msg_erro'] = "Você não pode inativar seu próprio usuário.";
        }

        $this->redirect('/config/usuarios');
    }
}
