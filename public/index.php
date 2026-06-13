<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

// Carrega as configurações globais (incluindo BASE_URL e DB)
require_once __DIR__ . '/../config/database.php';

spl_autoload_register(function ($class) {
    $class = str_replace('App\\', 'app/', $class);
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;

$router = new Router();

// Define rotas
$router->add('GET', '/', ['AuthController', 'loginForm']);
$router->add('POST', '/login', ['AuthController', 'login']);
$router->add('GET', '/logout', ['AuthController', 'logout']);

$router->add('GET', '/dashboard', ['DashboardController', 'index']);
$router->add('GET', '/caixa', ['CaixaController', 'index']);
$router->add('POST', '/caixa/abrir', ['CaixaController', 'abrir']);
$router->add('POST', '/caixa/fechar', ['CaixaController', 'fechar']);
$router->add('POST', '/caixa/reabrir', ['CaixaController', 'reabrir']);
$router->add('GET', '/caixa/saldo-conta', ['CaixaController', 'saldoConta']);

$router->add('GET', '/movimentacoes', ['MovimentacaoController', 'index']);
$router->add('GET', '/movimentacoes/novo', ['MovimentacaoController', 'create']);
$router->add('GET', '/movimentacoes/buscar', ['MovimentacaoController', 'buscar']);
$router->add('POST', '/movimentacoes/update', ['MovimentacaoController', 'update']);
$router->add('GET', '/movimentacoes/cancelar', ['MovimentacaoController', 'cancelar']);
$router->add('POST', '/movimentacoes/salvar', ['MovimentacaoController', 'store']);
$router->add('POST', '/sangria/salvar', ['SangriaController', 'store']);

// OS e Serviços
$router->add('GET', '/os', ['OSController', 'index']);
$router->add('GET', '/os/buscar', ['OSController', 'buscar']);
$router->add('POST', '/os/atualizar', ['OSController', 'atualizar']);

$router->add('GET', '/vendas', ['VendaController', 'index']);
$router->add('GET', '/vendas/buscar', ['VendaController', 'buscar']);
$router->add('POST', '/vendas/atualizar', ['VendaController', 'atualizar']);
$router->add('GET', '/relatorios', ['RelatorioController', 'index']);
$router->add('GET', '/config', ['ConfigController', 'index']);
$router->add('GET', '/config/perfil', ['PerfilController', 'index']);
$router->add('POST', '/config/perfil/salvar', ['PerfilController', 'update']);

$router->add('GET', '/config/usuarios', ['UsuarioController', 'index']);
$router->add('GET', '/config/usuarios/novo', ['UsuarioController', 'create']);
$router->add('POST', '/config/usuarios/salvar', ['UsuarioController', 'store']);
$router->add('GET', '/config/usuarios/editar/{id}', ['UsuarioController', 'edit']);
$router->add('POST', '/config/usuarios/atualizar/{id}', ['UsuarioController', 'update']);
$router->add('GET', '/config/usuarios/toggle/{id}', ['UsuarioController', 'toggle']);

$router->add('GET', '/config/financeiro', ['ConfigFinanceiraController', 'index']);
$router->add('POST', '/config/financeiro/conta/salvar', ['ConfigFinanceiraController', 'store_conta']);
$router->add('GET', '/config/financeiro/conta/toggle/{id}', ['ConfigFinanceiraController', 'toggle_conta']);
$router->add('POST', '/config/financeiro/forma/salvar', ['ConfigFinanceiraController', 'store_forma']);
$router->add('GET', '/config/financeiro/forma/toggle/{id}', ['ConfigFinanceiraController', 'toggle_forma']);
$router->add('POST', '/config/financeiro/natureza/salvar', ['ConfigFinanceiraController', 'store_natureza']);
$router->add('GET', '/config/financeiro/natureza/toggle/{id}', ['ConfigFinanceiraController', 'toggle_natureza']);

$router->add('GET', '/config/fornecedores', ['ConfigCadastrosController', 'fornecedores']);
$router->add('POST', '/config/fornecedores/salvar', ['ConfigCadastrosController', 'store_fornecedor']);

$router->add('GET', '/config/funcionarios', ['ConfigCadastrosController', 'funcionarios']);
$router->add('POST', '/config/funcionarios/salvar', ['ConfigCadastrosController', 'store_funcionario']);
$router->add('GET', '/config/funcionarios/toggle/{id}', ['ConfigCadastrosController', 'toggle_funcionario']);

$router->add('GET', '/config/auditoria', ['ConfigAuditoriaController', 'index']);
$router->add('GET', '/config/perfis', ['PerfilAcessoController', 'index']);
$router->add('GET', '/config/perfis/novo', ['PerfilAcessoController', 'create']);
$router->add('POST', '/config/perfis/salvar', ['PerfilAcessoController', 'store']);
$router->add('GET', '/config/perfis/editar/{id}', ['PerfilAcessoController', 'edit']);
$router->add('POST', '/config/perfis/atualizar/{id}', ['PerfilAcessoController', 'update']);

$router->run();
