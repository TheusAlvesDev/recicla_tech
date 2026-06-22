const listaDeDoacoes = [
    {
        imagem: '../img/equipamentosQuebrados.webp',
        nome: 'iPhone 11',
        descricao: 'Tela levemente trincada, mas funcionando perfeitamente.',
        localizacao: 'Centro - Iguatu, CE'
    },
    {
        imagem: '../img/notebooks.webp',
        nome: 'Notebook Dell Inspiron',
        descricao: 'Falta bateria, funciona apenas na tomada. 8GB RAM.',
        localizacao: 'Bairro Flores - Iguatu, CE'
    },
    {
        imagem: '../img/roteadores.webp',
        nome: 'Teclado Mecânico Redragon',
        descricao: 'Algumas teclas falhando, bom para retirada de peças.',
        localizacao: 'Bairro Veneza - Iguatu, CE'
    },
    {
        imagem: '../img/acessorios.webp',
        nome: 'Monitor LG 21"',
        descricao: 'Mancha na tela, mas liga normalmente.',
        localizacao: 'Bairro Prado - Iguatu, CE'
    },
    {
        imagem: '../img/celulares.webp',
        nome: 'celular moto G',
        descricao: 'A tela descolou, mas liga normalmente.',
        localizacao: 'bairro areias - Iguatu, CE'
    }
];
const trilho = document.getElementById('trilho-doacoes');
listaDeDoacoes.forEach(item => {
    const cardHTML = `
        <div class="card">
            <div class="imagem-card-container">
                <img class="imagem-doacao" src="${item.imagem}" alt="Foto de ${item.nome}">
            </div>
            <div class="descricao-doacao" >
                <p class="fw-bold nome-dispositivo">${item.nome}</p>
                <p class="descricao text-muted">${item.descricao}</p>
            </div>
            <div class="rodape-card">
                <p>${item.localizacao}</p>
                <a href="#" class="botao-card gradiente-bts-principais botao-transicao">Eu quero</a>
            </div>
        </div>
    `;
    trilho.innerHTML += cardHTML;
});
const btnAvancar = document.getElementById('btn-avancar');
const btnVoltar = document.getElementById('btn-voltar');
let posicaoAtual = 0;
btnAvancar.addEventListener('click', () => {
    const totalDeCards = document.querySelectorAll('.carrossel-track .card').length;
    const limite = totalDeCards - 3; 
    if (posicaoAtual < limite) {
        posicaoAtual++;
        atualizarCarrossel();
    }
});
btnVoltar.addEventListener('click', () => {
    if (posicaoAtual > 0) {
        posicaoAtual--;
        atualizarCarrossel();
    }
});
function atualizarCarrossel() {
    const larguraCard = document.querySelector('.carrossel-track .card').offsetWidth;
    const gap = 20;
    const distancia = (larguraCard + gap) * posicaoAtual;
    trilho.style.transform = `translateX(-${distancia}px)`;
}