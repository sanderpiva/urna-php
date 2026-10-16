#O MySQL disponibiliza a função ANY_VALUE(), 
#feita especificamente para indicar ao banco que ele pode escolher qualquer
#linha do grupo para preencher os campos restantes:
#pois, por padrão, voce precisaria colocar todas as clausulas do select no GROUP BY
#com o ANY_VALUE() voce evita o problema se entrar em produção 

SELECT 
    leg.partido_coligacao, 
    ANY_VALUE(leg.numero_voto) AS numero_voto, 
    ANY_VALUE(leg.nome_candidato) AS nome_candidato,
    SUM(v.votos_validos) AS total_votos_validos
FROM votacao AS v
INNER JOIN legenda AS leg
    ON v.legenda_id = leg.id
GROUP BY 
    leg.partido_coligacao
ORDER BY 
    total_votos_validos DESC;
    

#Subconsulta

SELECT 
    -- Total de votos válidos (através do seu JOIN com legenda)
    (
        SELECT SUM(v.votos_validos) 
        FROM votacao AS v
        INNER JOIN legenda AS leg ON v.legenda_id = leg.id
    ) AS total_votos_validos,
    
    -- Total de votos brancos na tabela geral
    SUM(v.votos_brancos) AS total_votos_brancos,
    
    -- Total de votos nulos na tabela geral
    SUM(v.votos_nulos) AS total_votos_nulos,
    
    -- Total geral de votos
    SUM(v.votos_validos + v.votos_brancos + v.votos_nulos) AS total_geral_votos

FROM votacao AS v;

    
    