<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller {
    public function loginForm() {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirectUsuario();
        }
        $this->view('auth/login');
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->findByEmail($email);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['perfil_nome'] = $usuario['perfil_nome'];
            $_SESSION['permissoes'] = json_decode($usuario['permissoes'], true);
            
            $this->redirectUsuario();
        } else {
            $_SESSION['erro_login'] = "E-mail ou senha inválidos.";
            $this->redirect('/');
        }
    }

    private function redirectUsuario() {
        $permissoes = $_SESSION['permissoes'] ?? [];
        if (isset($permissoes['todas']) && $permissoes['todas'] === true) {
            $this->redirect('/dashboard');
        } elseif (!empty($permissoes['dashboard:visualizar']) || !empty($permissoes['dashboard_view'])) {
            $this->redirect('/dashboard');
        } elseif (!empty($permissoes['caixa:visualizar']) || !empty($permissoes['caixa_view'])) {
            $this->redirect('/caixa');
        } elseif (!empty($permissoes['movimentacao:visualizar_todas']) || !empty($permissoes['movimentacoes_view'])) {
            $this->redirect('/movimentacoes');
        } elseif (!empty($permissoes['os:visualizar']) || !empty($permissoes['os_view'])) {
            $this->redirect('/os');
        } elseif (!empty($permissoes['venda:visualizar']) || !empty($permissoes['vendas_view'])) {
            $this->redirect('/vendas');
        } elseif (!empty($permissoes['relatorios:visualizar']) || !empty($permissoes['relatorios_view'])) {
            $this->redirect('/relatorios');
        } elseif (!empty($permissoes['configuracoes:visualizar']) || !empty($permissoes['configuracoes_view'])) {
            $this->redirect('/config');
        } else {
            session_destroy();
            $this->redirect('/?erro=Sem acesso. Entre em contato com um administrador.');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/');
    }
}
