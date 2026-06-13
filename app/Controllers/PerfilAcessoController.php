<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\PerfilAcesso;

class PerfilAcessoController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('configuracoes:visualizar');
    }

    public static function getListaPermissoesValidas() {
        return [
            'caixa:visualizar', 'caixa:abrir', 'caixa:fechar', 'caixa:reabrir', 'caixa:ver_saldo', 'sangria:criar',
            'movimentacao:visualizar_todas', 'movimentacao:criar', 'movimentacao:editar', 'movimentacao:cancelar',
            'os:visualizar', 'venda:visualizar', 'custos:gerenciar',
            'dashboard:visualizar', 'relatorios:visualizar', 'relatorios:gerar',
            'configuracoes:visualizar', 'cadastros:gerenciar', 'auditoria:visualizar'
        ];
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $pModel = new PerfilAcesso();
        $perfis = $pModel->getAll();

        $this->view('layouts/main', [
            'title' => 'Perfis de Acesso',
            'contentView' => 'config/perfis/index',
            'perfis' => $perfis
        ]);
    }

    public function create() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        $this->view('layouts/main', [
            'title' => 'Novo Perfil',
            'contentView' => 'config/perfis/form'
        ]);
    }

    public function store() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');
        
        $permissoes = [];
        if (isset($_POST['perm_todas'])) {
            $permissoes['todas'] = true;
        }
        foreach (self::getListaPermissoesValidas() as $perm) {
            $key = 'perm_' . str_replace(':', '_', $perm);
            $permissoes[$perm] = isset($_POST[$key]);
        }

        $pModel = new PerfilAcesso();
        $pModel->inserir([
            'nome' => trim($_POST['nome']),
            'descricao' => trim($_POST['descricao']),
            'permissoes' => json_encode($permissoes)
        ]);

        $this->redirect('/config/perfis');
    }

    public function edit($id) {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $pModel = new PerfilAcesso();
        $perfil = $pModel->getById($id);
        
        if (!$perfil) $this->redirect('/config/perfis');

        $permissoesAtuais = json_decode($perfil['permissoes'], true);
        if (!is_array($permissoesAtuais)) $permissoesAtuais = [];

        $this->view('layouts/main', [
            'title' => 'Editar Perfil',
            'contentView' => 'config/perfis/edit',
            'perfil' => $perfil,
            'permissoesAtuais' => $permissoesAtuais
        ]);
    }

    public function update($id) {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');
        
        $permissoes = [];
        if (isset($_POST['perm_todas'])) {
            $permissoes['todas'] = true;
        }
        foreach (self::getListaPermissoesValidas() as $perm) {
            $key = 'perm_' . str_replace(':', '_', $perm);
            $permissoes[$perm] = isset($_POST[$key]);
        }

        $pModel = new PerfilAcesso();
        $pModel->atualizar($id, [
            'nome' => trim($_POST['nome']),
            'descricao' => trim($_POST['descricao']),
            'permissoes' => json_encode($permissoes)
        ]);

        $this->redirect('/config/perfis');
    }
}
