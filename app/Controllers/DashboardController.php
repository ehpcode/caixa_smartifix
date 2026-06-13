<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Dashboard;

class DashboardController extends Controller {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/');
        }
        $this->verificarPermissao('dashboard:visualizar');
        
        $data_operacao = $_GET['data'] ?? date('Y-m-d');
        
        $dashModel = new Dashboard();
        
        $resumo = $dashModel->getResumoDia($data_operacao);
        $entradas = $resumo['total_entradas'] ?? 0;
        $saidas = $resumo['total_saidas'] ?? 0;
        
        $saldos = $dashModel->getSaldosContasDia($data_operacao);
        $saldo_inicial_total = array_sum(array_column($saldos, 'saldo_inicial'));
        $saldo_disponivel_total = array_sum(array_column($saldos, 'saldo_final'));
        
        $custos = $dashModel->getCustosDia($data_operacao);
        $lucro_bruto = $entradas - $saidas;

        $totais = [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'lucro_bruto' => $lucro_bruto,
            'saldo_inicial' => $saldo_inicial_total,
            'saldo_disponivel' => $saldo_disponivel_total,
            'custos' => $custos
        ];

        $graficos = [
            'entradas_natureza' => $dashModel->getTotaisPorNatureza($data_operacao, 'entrada'),
            'saidas_natureza' => $dashModel->getTotaisPorNatureza($data_operacao, 'saida'),
            'entradas_forma' => $dashModel->getTotaisPorFormaPagamento($data_operacao, 'entrada'),
            'saidas_forma' => $dashModel->getTotaisPorFormaPagamento($data_operacao, 'saida')
        ];

        $desempenho = $dashModel->getDesempenhoFuncionarios($data_operacao);

        $this->view('layouts/main', [
            'title' => 'Dashboard',
            'contentView' => 'dashboard/index',
            'data_operacao' => $data_operacao,
            'totais' => $totais,
            'saldos' => $saldos,
            'graficos' => $graficos,
            'desempenho' => $desempenho
        ]);
    }
}
