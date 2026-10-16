<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    session_start();
}

$controllerAtual = strtolower(isset($_GET['controller']) ? (string) $_GET['controller'] : 'adm');
$acaoAtual = strtolower(isset($_GET['acao']) ? (string) $_GET['acao'] : 'index');

//1. PROTEÇÃO DA URNA (Eleitor)

if (isset($_SESSION['na_urna'])) {

    $permitidoNaUrna = (
        $controllerAtual === 'eleitor' &&
        in_array($acaoAtual, ['votar', 'registrar-voto'], true)
    );

    if (!$permitidoNaUrna) {
        // Mantém o eleitor na urna enquanto a votação estiver em andamento.
        header('Location: index.php?controller=eleitor&acao=votar');
        exit;
    }
}

// 2. PROTEÇÃO DO PAINEL ADMINISTRATIVO (ADM)
// 2.1. Se o admin tentar acessar a listagem de resultados sem estar autenticado

if ($controllerAtual === 'adm' && $acaoAtual === 'listar') {
    if (empty($_SESSION['admin_autenticado'])) {
        header('Location: index.php?controller=adm&acao=login_adm');
        exit;
    }
}

// 2.2. Se o admin estiver logado, mas voltar para a página inicial/index e você quiser 
// forçar logout por segurança ao sair do painel

if (isset($_SESSION['admin_autenticado']) && $controllerAtual === 'adm' && $acaoAtual === 'index') {
    unset($_SESSION['admin_autenticado']);
    session_destroy();
    header('Location: index.php?controller=adm&acao=index');
    exit;
}


require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/model/ADM_model.php';
require_once __DIR__ . '/model/Eleitor_model.php';
require_once __DIR__ . '/controller/Eleitor.php';
require_once __DIR__ . '/controller/ADM.php';

$conexao = conectarBD();

if ($conexao === null) {
    http_response_code(503);
    echo '<h1>Serviço indisponível</h1><p>Não foi possível conectar ao banco de dados.</p>';
    exit;
}

$rotas = [
    'adm' => 'ADMController',
    'eleitor' => 'EleitorController' 
];

$controller = $controllerAtual;
$acao = $acaoAtual;

if (!isset($rotas[$controller])) {
    http_response_code(404);
    echo '<h1>Controller não encontrado</h1>';
    exit;
}

$classeController = $rotas[$controller];

$model = null;

if ($controller === 'eleitor') {
    $model = new EleitorModel($conexao);
} elseif ($controller === 'adm') {
    $model = new ADMModel($conexao);
}

$instanciaController = new $classeController($model);
$instanciaController->executar($acao);