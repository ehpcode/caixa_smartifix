<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Usuario;

class PerfilController extends Controller {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getById($_SESSION['usuario_id']);

        $this->view('layouts/main', [
            'title' => 'Meu Perfil',
            'contentView' => 'config/perfil',
            'usuario' => $usuario,
            'sucesso' => $_SESSION['msg_sucesso'] ?? null,
            'erro' => $_SESSION['msg_erro'] ?? null
        ]);

        unset($_SESSION['msg_sucesso'], $_SESSION['msg_erro']);
    }

    public function update() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');
        
        $id = $_SESSION['usuario_id'];
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha_nova = trim($_POST['senha_nova']);

        $usuarioModel = new Usuario();
        
        $usuarioModel->atualizarPerfil($id, $nome, $email);
        $_SESSION['usuario_nome'] = $nome; 

        if (!empty($senha_nova)) {
            $senhaHash = password_hash($senha_nova, PASSWORD_DEFAULT);
            $usuarioModel->atualizarSenha($id, $senhaHash);
        }

        $_SESSION['msg_sucesso'] = "Perfil atualizado com sucesso!";
        $this->redirect('/config/perfil');
    }
}
