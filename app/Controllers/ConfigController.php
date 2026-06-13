<?php
namespace App\Controllers;
use App\Core\Controller;

class ConfigController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('configuracoes:visualizar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $this->view('layouts/main', [
            'title' => 'Painel de Configurações',
            'contentView' => 'config/index'
        ]);
    }
}
