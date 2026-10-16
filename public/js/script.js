const btn1 = document.getElementById('btn-1');
const btn2 = document.getElementById('btn-2');
const btn3 = document.getElementById('btn-3');
const btn4 = document.getElementById('btn-4');
const btn5 = document.getElementById('btn-5');
const btn6 = document.getElementById('btn-6');
const btn7 = document.getElementById('btn-7');
const btn8 = document.getElementById('btn-8');
const btn9 = document.getElementById('btn-9');
const btn0 = document.getElementById('btn-0');
const btnbranco = document.getElementById('btn-branco');
const btncorrigir = document.getElementById('btn-corrigir');
const btnconfirmar = document.getElementById('btn-confirmar');
const conteudo = document.getElementById('conteudo');
const lista = [];

function handleButtonClick(value) {
    
    conteudo.classList.add('sem-fundo');

    if (value === 'branco') {
        lista.length = 0;
        lista.push('branco');
        conteudo.value = 'branco';
        return;
    }

    if (lista[0] === 'branco') {
        resetInput();
    }

    lista.push(value);
    conteudo.value = lista.join('');

    if (lista.length > 2) {
        alert('Você só pode digitar dois números.');
        resetInput();
        return;
    }

    if (lista.length === 2) {
        conteudo.value = lista.join('');
        
    }
}

function resetInput() {
    lista.length = 0;
    conteudo.value = '';
    conteudo.classList.remove('sem-fundo');
}


btn1.addEventListener('click', () => {
    handleButtonClick('1');
});
btn2.addEventListener('click', () => {
    handleButtonClick('2');
});
btn3.addEventListener('click', () => {
    handleButtonClick('3');
});
btn4.addEventListener('click', () => {
    handleButtonClick('4');
});
btn5.addEventListener('click', () => {
    handleButtonClick('5');
});
btn6.addEventListener('click', () => {
    handleButtonClick('6');
});
btn7.addEventListener('click', () => {
    handleButtonClick('7');
});
btn8.addEventListener('click', () => {
    handleButtonClick('8');
});
btn9.addEventListener('click', () => {
    handleButtonClick('9');
});
btn0.addEventListener('click', () => {
    handleButtonClick('0');
});

btnbranco.addEventListener('click', () => {   
    handleButtonClick('branco');
});

btncorrigir.addEventListener('click', () => {   
    resetInput();
    conteudo.classList.remove('sem-fundo');
});
btnconfirmar.addEventListener('click', async () => {
    
    const numeroVoto = conteudo.value.trim();

    if (!numeroVoto) {
        alert('Você não digitou nenhum número. Por favor, digite o número do candidato ou pressione "Branco" para votar em branco.');
        return; // Interrompe a execução antes do fetch
    }

    btnconfirmar.disabled = true;

    try {
        const resposta = await fetch('index.php?controller=eleitor&acao=registrar-voto', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ numero_voto: numeroVoto })
        });
        const resultado = await resposta.json();

        if (!resposta.ok) {
            alert(resultado.erro || 'Voto inválido.');

            conteudo.value = 'FIM';
            setTimeout(() => {
                window.location.href = 'index.php?controller=adm&acao=index';
            }, 2000);
            return;
        }

        if (resultado.tipo === 'branco') {
            alert('Voto em branco registrado.');
        } else if (resultado.tipo === 'nulo') {
            alert('Voto nulo registrado.');
        } else {
            alert('Voto válido registrado para ' + resultado.candidato + '.');
        }
        
        conteudo.value = 'FIM';
        setTimeout(() => {
            window.location.href = 'index.php?controller=adm&acao=index';
        }, 2000);

    } catch (erro) {
        alert('Não foi possível registrar o voto. Tente novamente.');
    } finally {
        btnconfirmar.disabled = false;
    }

});

window.addEventListener('beforeunload', () => {
    
    navigator.sendBeacon('index.php?controller=eleitor&acao=sair-forçado');
});

