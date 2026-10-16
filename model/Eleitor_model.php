<?php

class EleitorModel
{
	private $conexao;

	public function __construct($conexao)
	{
		$this->conexao = $conexao;
	}

	public function autenticarEleitor(array $dados)
	{
		$consulta = $this->conexao->prepare(
			'SELECT idEleitor FROM eleitor '
			. 'WHERE nome = :nome AND data = :data AND zona = :zona '
			. 'AND municipio = :municipio AND cpf = :cpf AND secao = :secao '
			. 'AND filiacao = :filiacao AND votou = 0 LIMIT 1'
		);
		$consulta->execute($dados);
		$eleitor = $consulta->fetch();

		return $eleitor ?: null;
	}

	public function eleitorPodeVotar($idEleitor)
	{
		$consulta = $this->conexao->prepare(
			'SELECT idEleitor FROM eleitor WHERE idEleitor = :id_eleitor AND votou = 0'
		);
		$consulta->execute(['id_eleitor' => $idEleitor]);

		return (bool) $consulta->fetch();
	}

	public function registrarVoto($numeroVoto, $idEleitor)
	{
		$this->conexao->beginTransaction();

		try {
			$consultaEleitor = $this->conexao->prepare(
				'SELECT idEleitor FROM eleitor '
				. 'WHERE idEleitor = :id_eleitor AND votou = 0 FOR UPDATE'
			);
			$consultaEleitor->execute(['id_eleitor' => $idEleitor]);
			if (!$consultaEleitor->fetch()) {
				$this->conexao->rollBack();
				return false;
			}

			$legenda = null;
			if ($numeroVoto !== 'branco') {
				$consultaLegenda = $this->conexao->prepare(
					'SELECT id, nome_candidato FROM legenda '
					. 'WHERE numero_voto = :numero_voto FOR UPDATE'
				);
				$consultaLegenda->execute(['numero_voto' => (int) $numeroVoto]);
				$legenda = $consultaLegenda->fetch();
			}

			$tipoVoto = $numeroVoto === 'branco' ? 'branco' : ($legenda ? 'valido' : 'nulo');
			
			$inserirVotacao = $this->conexao->prepare(
				'INSERT INTO votacao '
				. '(eleitor_idEleitor, legenda_id, votos_validos, votos_brancos, votos_nulos) '
				. 'VALUES (:id_eleitor, :legenda_id, :votos_validos, :votos_brancos, :votos_nulos)'
			);
			$inserirVotacao->execute([
				'id_eleitor' => $idEleitor,
				'legenda_id' => $tipoVoto === 'valido' ? $legenda['id'] : null,
				'votos_validos' => $tipoVoto === 'valido' ? 1 : 0,
				'votos_brancos' => $tipoVoto === 'branco' ? 1 : 0,
				'votos_nulos' => $tipoVoto === 'nulo' ? 1 : 0
			]);

			$marcarEleitor = $this->conexao->prepare(
				'UPDATE eleitor SET votou = 1 WHERE idEleitor = :id_eleitor AND votou = 0'
			);
			$marcarEleitor->execute(['id_eleitor' => $idEleitor]);
			if ($marcarEleitor->rowCount() !== 1) {
				$this->conexao->rollBack();
				return false;
			}

			$this->conexao->commit();
			return [
				'tipo' => $tipoVoto,
				'candidato' => $legenda ? $legenda['nome_candidato'] : null,
			];
		} catch (PDOException $e) {
			if ($this->conexao->inTransaction()) {
				$this->conexao->rollBack();
			}

			throw $e;
		}
	}	
}