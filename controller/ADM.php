<?php

class ADMController
{
    const ADMIN_USERNAME = 'ADM';
    const ADMIN_PASSWORD_HASH = '$2y$10$2DCqOyYytnQqyyaYqgISzuA.oAUX/vUCMAro4VRB8B6KxdqvYkP42';

    private $admModel;

    public function __construct($admModel)
    {
        $this->admModel = $admModel;
    }

    public function executar($acao)
    {
        switch ($acao) {
            case 'index':
                $this->index();
                return;
            case 'inicio_eleitor':
                $this->inicio_eleitor();
                return;
            case 'inicio_adm':
                $this->inicio_adm();
                return;
            case 'listar':
                $this->listar();
                return;
            case 'login_adm':
                $this->login_adm();
                return;
            case 'logout_adm':
                $this->logout_adm();
                return;
            default:
                $this->acaoNaoEncontrada();
        }
    }

    public function index()
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Allow: GET');
            http_response_code(405);
            echo '<h1>Método não permitido</h1>';
            return;
        }
        
        if (!isset($_GET['tipo'])) {
            require __DIR__ . '/../view/view_selecao.php';
            return;
        }

        $tipo = strtolower(trim((string) $_GET['tipo']));

        if ($tipo === 'adm') {
            require __DIR__ . '/../view/view_login_adm.php';
        } elseif ($tipo === 'eleitor') {
            require __DIR__ . '/../view/view_inicio_eleitor.php';
        } else {
            
            echo '<h1>Perfil inválido</h1>';
        }
    }

    private function inicio_eleitor()
    {
     
        require __DIR__ . '/../view/view_inicio_eleitor.php';
    }

    private function inicio_adm()
    {
     
        require __DIR__ . '/../view/view_inicio_adm.php';
    }


    private function listar()
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Allow: GET');
            http_response_code(405);
            echo '<h1>Método não permitido</h1>';
            return;
        }

        if (empty($_SESSION['admin_autenticado'])) {
            header('Location: index.php?controller=adm&acao=listar');
            return;
        }

        try {
            $resultados = $this->admModel->listarResultados();
            
            $totalVotos = $this->admModel->obterTotalVotosGerais();

            require __DIR__ . '/../view/view_listar.php';
        } catch (PDOException $e) {
            error_log('Erro ao consultar resultados: ' . $e->getMessage());
            http_response_code(500);
            echo '<h1>Não foi possível carregar os resultados.</h1>';
        }
    }

    private function login_adm()
    {
        $metodo = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        if ($metodo !== 'GET' && $metodo !== 'POST') {
            header('Allow: GET, POST');
            http_response_code(405);
            echo '<h1>Método não permitido</h1>';
            return;
        }

        if (!empty($_SESSION['admin_autenticado'])) {
            header('Location: index.php?controller=adm&acao=inicio_adm');
            return;
        }

        $erro = null;
        if ($metodo === 'POST') {
            $nome = isset($_POST['nome']) ? trim((string) $_POST['nome']) : '';
            $senha = isset($_POST['senha']) ? (string) $_POST['senha'] : '';

            if ($nome === self::ADMIN_USERNAME && password_verify($senha, self::ADMIN_PASSWORD_HASH)) {
                session_regenerate_id(true);
                $_SESSION['admin_autenticado'] = true;
                header('Location: index.php?controller=adm&acao=inicio_adm');
                return;
            }

            $erro = 'Nome ou senha inválidos.';
            http_response_code(401);
        }

        require __DIR__ . '/../view/view_login_adm.php';
    }

    private function logout_adm()
    {
        unset($_SESSION['admin_autenticado']);
        session_regenerate_id(true);
        header('Location: index.php?controller=adm&acao=index');
    }
    
    private function acaoNaoEncontrada()
    {
        http_response_code(404);
        echo '<h1>Ação não encontrada</h1>';
    }
}