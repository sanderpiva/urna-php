<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Acesso aos resultados</title>
</head>
<body>
    <main class="pagina-login">
        <h1>Acesso administrativo</h1>
        <form class="form-login" method="post" action="index.php?controller=adm&acao=login_adm">
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" autocomplete="username" required>

            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" autocomplete="current-password" required>

            <button type="submit">Entrar</button>
        </form>
        <a href="index.php?controller=adm&acao=index">Voltar</a>
    </main>
</body>
</html>