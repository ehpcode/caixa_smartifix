<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Relatorio;

class RelatorioController extends Controller {
    public function __construct() {
        if(isset($_SESSION['usuario_id'])) $this->verificarPermissao('relatorios:visualizar');
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) $this->redirect('/');
        
        $mes = $_GET['mes'] ?? date('m');
        $ano = $_GET['ano'] ?? date('Y');

        $relatorioModel = new Relatorio();
        $resumo = $relatorioModel->getResumoMensal($mes, $ano);

        $this->view('layouts/main', [
            'title' => 'Relatório Mensal',
            'contentView' => 'relatorios/index',
            'resumo' => $resumo,
            'mesSelecionado' => $mes,
            'anoSelecionado' => $ano
        ]);
    }
}
