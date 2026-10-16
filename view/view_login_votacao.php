<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Login eleitor</title>
</head>
<body>
    <main class="pagina-login">
        <h1>Login do eleitor</h1>
        <?php if ($erro !== null): ?>
            <p class="erro-login" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form class="form-login" method="post" action="index.php?controller=eleitor&acao=login_votacao">
            <label for="nome">Nome eleitor</label>
            <input id="nome" name="nome" type="text" autocomplete="username" required>

            <label for="data">Data de nascimento</label>
            <input id="data" name="data" type="date" required>

            <label for="zona">Zona eleitoral</label>
            <input id="zona" name="zona" type="text" autocomplete="username" required>

            <label for="municipio">Município</label>
            <input id="municipio" name="municipio" type="text" placeholder = "Cidade/SP" autocomplete="username" required>

            <label for="cpf">CPF</label>
            <input id="cpf" name="cpf" type="text" placeholder = "xxxxxxxxxxx" autocomplete="username" required>

            <label for="secao">Seção</label>
            <input id="secao" name="secao" type="text" autocomplete="username" required>

            <label for="filiacao">Filiação</label>
            <input id="filiacao" name="filiacao" type="text" placeholder="Nome pai - Nome mãe" required>

            <button type="submit">Entrar</button>
        </form>
        <a href="index.php?controller=adm&acao=inicio_eleitor">Voltar</a>
    </main>
</body>
</html>