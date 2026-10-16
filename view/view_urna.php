<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Urna</title>
</head>
<body>
      
    <div class="master-container">
        <div class="container">
            
            <input type="text" id="conteudo">
                       
            <div class="btns-1-9">

                <img src="public/img/1.jpg" alt="Urna" class="urna-img1">
                
                <button id="btn-1">1</button>
                <button id="btn-2">2</button>
                <button id="btn-3">3</button>
                <button id="btn-4">4</button>
                <button id="btn-5">5</button>
                <button id="btn-6">6</button>
                <button id="btn-7">7</button>
                <button id="btn-8">8</button>
                <button id="btn-9">9</button>
                <button id="btn-espaco"></button>
                <button id="btn-0">0</button>
                <button id="btn-espaco"></button>
                <button type="submit" id="btn-branco">BRANCO</button>
                <button type="submit" id="btn-corrigir">CORRIGIR</button>
                <button type="submit" id="btn-confirmar">CONFIRMAR</button>
                
            </div>

        </div>

    </div>

    
    <script src="public/js/script.js?v=<?= filemtime(__DIR__ . '/../public/js/script.js') ?>"></script>
</body>
</html>