<?php

class EleitorController
{

	private $eleitorModel;

	public function __construct($eleitorModel)
	{
		$this->eleitorModel = $eleitorModel;
	}

	public function executar($acao)
    {
        switch ($acao) {
            case 'votar':
                $this->votar();
                return;
            case 'login_votacao':
                $this->loginVotacao();
                return;
            case 'registrar-voto':
                $this->registrarVoto();
                return;
            case 'sair-forçado':
                $this->sairForcado();
                return;
            default:
                $this->acaoNaoEncontrada();
        }
    }

    private function sairForcado()
    {
        if (isset($_SESSION['na_urna'])) {
            unset($_SESSION['eleitor_id']);
            unset($_SESSION['na_urna']);
            session_destroy();
        }
        exit;
    }

	private function votar()
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Allow: GET');
            http_response_code(405);
            echo '<h1>Método não permitido</h1>';
            return;
        }

        if (empty($_SESSION['eleitor_id'])) {
            header('Location: index.php?controller=eleitor&acao=login_votacao');
            return;
        }

        try {
            if (!$this->eleitorModel->eleitorPodeVotar((int) $_SESSION['eleitor_id'])) {
                unset($_SESSION['eleitor_id']);
                unset($_SESSION['na_urna']); // Limpa a flag
                session_regenerate_id(true);
                header('Location: index.php?controller=eleitor&acao=login_votacao');
                return;
            }
        } catch (PDOException $e) {
            error_log('Erro ao validar eleitor: ' . $e->getMessage());
            http_response_code(500);
            echo '<h1>Não foi possível validar o acesso à urna.</h1>';
            return;
        }

        $_SESSION['na_urna'] = true;

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        require __DIR__ . '/../view/view_urna.php';
    }
	
	private function loginVotacao()
	{	
		$metodo = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		if ($metodo !== 'GET' && $metodo !== 'POST') {
			header('Allow: GET, POST');
			http_response_code(405);
			echo '<h1>Método não permitido</h1>';
			return;
		}

		if (!empty($_SESSION['eleitor_id'])) {
			header('Location: index.php?controller=eleitor&acao=votar');
			return;
		}

		$erro = null;
		if ($metodo === 'POST') {
			$dados = [
				'nome' => isset($_POST['nome']) ? trim((string) $_POST['nome']) : '',
				'data' => isset($_POST['data']) ? trim((string) $_POST['data']) : '',
				'zona' => isset($_POST['zona']) ? trim((string) $_POST['zona']) : '',
				'municipio' => isset($_POST['municipio']) ? trim((string) $_POST['municipio']) : '',
				'cpf' => isset($_POST['cpf']) ? trim((string) $_POST['cpf']) : '',
				'secao' => isset($_POST['secao']) ? trim((string) $_POST['secao']) : '',
				'filiacao' => isset($_POST['filiacao']) ? trim((string) $_POST['filiacao']) : '',
			];

			try {
				$eleitor = $this->eleitorModel->autenticarEleitor($dados);
				if ($eleitor) {
					session_regenerate_id(true);
					$_SESSION['eleitor_id'] = (int) $eleitor['idEleitor'];
					header('Location: index.php?controller=eleitor&acao=votar');
					return;
				}
			} catch (PDOException $e) {
				error_log('Erro ao autenticar eleitor: ' . $e->getMessage());
				http_response_code(500);
				$erro = 'Não foi possível validar os dados agora.';
			}

			if ($erro === null) {
				$erro = 'Dados inválidos ou eleitor já votou.';
				http_response_code(401);
			}
		}

		require __DIR__ . '/../view/view_login_votacao.php';
	}

	private function registrarVoto()
	{	

		if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Allow: POST');
			$this->responderJson(['erro' => 'Método não permitido.'], 405);
			return;
		}

		if (empty($_SESSION['eleitor_id'])) {
			$this->responderJson(['erro' => 'Acesso não autorizado. Faça login como eleitor.'], 401);
			return;
		}

		$dados = json_decode(file_get_contents('php://input'), true);
		
		$numeroVoto = is_array($dados) && isset($dados['numero_voto'])
			? (string) $dados['numero_voto']
			: '';

		if ($numeroVoto !== 'branco' && !preg_match('/^\d{2}$/D', $numeroVoto)) {
			$this->responderJson(['erro' => 'Número de voto inválido.'], 422);
			return;
		}

		try {
			$voto = $this->eleitorModel->registrarVoto(
				$numeroVoto,
				(int) $_SESSION['eleitor_id']
			);

			if ($voto === false) {
				unset($_SESSION['eleitor_id']);
				session_regenerate_id(true);
				$this->responderJson(['erro' => 'Este eleitor já votou ou não está cadastrado.'], 403);
				return;
			}

			unset($_SESSION['eleitor_id']);
            unset($_SESSION['na_urna']); 
            session_regenerate_id(true);

            $this->responderJson([
                'tipo' => $voto['tipo'],
                'candidato' => $voto['candidato'],
            ], 200);

		} catch (PDOException $e) {
			error_log('Erro ao registrar voto: ' . $e->getMessage());
			$this->responderJson(['erro' => 'Não foi possível registrar o voto.'], 500);
			
		}
	}
	
	private function responderJson(array $dados, $status)
	{
		http_response_code($status);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($dados, JSON_UNESCAPED_UNICODE);
	}
	
	//Acao comum
	private function acaoNaoEncontrada()
	{
		http_response_code(404);
		echo '<h1>Ação não encontrada</h1>';
	}
}
