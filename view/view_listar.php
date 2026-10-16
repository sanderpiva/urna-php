<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Resultados da votação</title>
</head>
<body>
    <nav class="navegacao" aria-label="Navegação">
        <a href="index.php?controller=adm&acao=logout_adm">Sair</a>
    </nav>
    
    <main class="pagina-resultados">
        <h1>Resultados da votação</h1>

        <section class="resumo-resultados" aria-label="Resumo">
            <?php 
                $validos = (int) ($totalVotos['total_votos_validos'] ?? 0);
                $brancos = (int) ($totalVotos['total_votos_brancos'] ?? 0);
                $nulos   = (int) ($totalVotos['total_votos_nulos'] ?? 0);
                $geral   = $validos + $brancos + $nulos;
            ?>
            <p>Votos válidos: <strong><?= number_format($validos, 0, ',', '.') ?></strong></p>
            <p>Votos brancos: <strong><?= number_format($brancos, 0, ',', '.') ?></strong></p>
            <p>Votos nulos: <strong><?= number_format($nulos, 0, ',', '.') ?></strong></p>
            <p>Total geral: <strong><?= number_format($geral, 0, ',', '.') ?></strong></p>
        </section>

        <table class="tabela-resultados">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Candidato</th>
                    <th>Votos válidos</th>
                    <th>Percentual dos votos válidos</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados)): ?>
                    <tr>
                        <td colspan="4">Nenhum candidato cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resultados as $resultado): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $resultado['numero_voto'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($resultado['nome_candidato'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format((int) $resultado['total_votos_validos'], 0, ',', '.') ?></td>
                            <td>
                                <?php
                                    $percentual = $validos > 0
                                        ? ((int) $resultado['total_votos_validos'] / $validos) * 100
                                        : 0;
                                    echo number_format($percentual, 2, ',', '.') . '%';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>