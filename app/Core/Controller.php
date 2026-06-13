<?php
namespace App\Core;

class Controller {
    public function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View $viewFile não encontrada.");
        }
    }

    public function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    public function getPermissoesUsuario() {
        if (!isset($_SESSION['usuario_id'])) return [];
        
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT p.permissoes FROM usuarios u JOIN perfis p ON u.perfil_id = p.id WHERE u.id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $res = $stmt->fetch();
        
        if ($res && !empty($res['permissoes'])) {
            $json = json_decode($res['permissoes'], true);
            return is_array($json) ? $json : [];
        }
        return [];
    }

    public function verificarPermissao($permissaoRequerida) {
        $permissoes = $this->getPermissoesUsuario();
        
        if (empty($permissoes)) {
            return true;
        }

        if (isset($permissoes['todas']) && $permissoes['todas'] === true) {
            return true;
        }

        $temPermissao = false;

        // Se a permissão passada tiver um colon, ex: 'caixa:abrir'
        if (strpos($permissaoRequerida, ':') !== false) {
            if (isset($permissoes[$permissaoRequerida]) && $permissoes[$permissaoRequerida] === true) {
                $temPermissao = true;
            }
        } else {
            // Se for chamada no construtor como 'caixa', vamos verificar se ele tem a permissão de visualização ('caixa:visualizar') ou compatibilidade
            $viewPerm = $permissaoRequerida . ':visualizar';
            if ((isset($permissoes[$viewPerm]) && $permissoes[$viewPerm] === true) || 
                (isset($permissoes[$permissaoRequerida]) && $permissoes[$permissaoRequerida] === true) || 
                (isset($permissoes['perm_' . $permissaoRequerida]) && $permissoes['perm_' . $permissaoRequerida] === true) ||
                (isset($permissoes[$permissaoRequerida . '_view']) && $permissoes[$permissaoRequerida . '_view'] === true)) {
                $temPermissao = true;
            }
        }

        if (!$temPermissao) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                http_response_code(403);
                echo json_encode(['erro' => 'Você não tem permissão para realizar esta ação.']);
                exit;
            }
            $this->redirect('/');
        }
        return true;
    }
}
