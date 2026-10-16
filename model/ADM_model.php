<?php

class ADMModel
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }


    public function listarResultados()
    {
        $consulta = $this->conexao->query(
            'SELECT leg.id, leg.numero_voto, leg.nome_candidato, '
            . 'COALESCE(SUM(v.votos_validos), 0) AS total_votos_validos '
            . 'FROM legenda AS leg '
            . 'LEFT JOIN votacao AS v ON v.legenda_id = leg.id '
            . 'GROUP BY leg.id, leg.numero_voto, leg.nome_candidato '
            . 'ORDER BY total_votos_validos DESC, leg.numero_voto ASC'
        );

        return $consulta->fetchAll();
    }

    public function obterTotalVotosGerais()
    {
        $consulta = $this->conexao->query(
            'SELECT '
            . 'COALESCE(SUM(votos_validos), 0) AS total_votos_validos, '
            . 'COALESCE(SUM(votos_brancos), 0) AS total_votos_brancos, '
            . 'COALESCE(SUM(votos_nulos), 0) AS total_votos_nulos '
            . 'FROM votacao'
        );

        return $consulta->fetch();
    }
}