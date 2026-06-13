<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Auditoria;

class ConfigAuditoriaController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('auditoria:visualizar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        $aModel = new Auditoria();
        $this->view('layouts/main', [
            'title' => 'Auditoria do Sistema',
            'contentView' => 'config/auditoria',
            'logs' => $aModel->getAll()
        ]);
    }
}
