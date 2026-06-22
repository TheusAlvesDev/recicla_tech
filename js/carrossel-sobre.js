// 1. Novos dados para o carrossel "Sobre"
const listaSobre = [
    {
        imagem: '../img/pilhas.webp', 
        titulo: 'Descarte de pilhas e baterias',
        descricao: 'Pilhas e baterias exigem pontos de coleta específicos e não devem ser colocadas no lixo comum.'
    },
    {
        imagem: '../img/celulares.webp',
        titulo: 'Proteja seus dados pessoais',
        descricao: 'Antes de doar um celular ou notebook, remova suas contas e restaure o aparelho para as configurações de fábrica.'
    },
    {
        imagem: '../img/equipamentosQuebrados.webp',
        titulo: 'Reciclagem de componentes',
        descricao: 'Placas e componentes sem possibilidade de reutilização devem seguir para empresas especializadas em reciclagem eletrônica.'
    },
    {
        imagem: '../img/roteadores.webp',
        titulo: 'Reutilizar antes de reciclar',
        descricao: 'Um aparelho funcional pode atender outra pessoa antes que seus materiais precisem ser reciclados.'
    },
    {
        imagem: '../img/notebooks.webp',
        titulo: 'Cuide dos seus aparelhos',
        descricao: 'Descubra como conservar seus aparelhos por mais tempo e evitar substituições desnecessárias.'
    }
];

// 2. Selecionando o trilho exclusivo deste carrossel
const trilhoSobre = document.getElementById('trilho-sobre');

// 3. Injetando a estrutura de card
listaSobre.forEach(item => {
    const cardHTML = `
        <div class="card">
            <div class="imagem-card-container">
                <img class="imagem-doacao" src="${item.imagem}" alt="Foto de ${item.titulo}" style="width:100%; height:200px; object-fit:cover;">
            </div>
            <div class="descricao-doacao" style="padding: 15px;">
                <p class="fw-bold titulo-sobre">${item.titulo}</p>
                <p class="descricao text-muted">${item.descricao}</p>
            </div>
            <div class="rodape-card d-flex justify-content-end" style="padding: 15px; text-align:center;">
                <a href="#" class="botao-card gradiente-bts-principais botao-transicao" >Saiba mais</a>
            </div>
        </div>
    `;
    trilhoSobre.innerHTML += cardHTML;
});

// 4. Lógica de movimento separada
const btnAvancarSobre = document.getElementById('btn-avancar-sobre');
const btnVoltarSobre = document.getElementById('btn-voltar-sobre');

let posicaoAtualSobre = 0;

btnAvancarSobre.addEventListener('click', () => {
    const totalDeCardsSobre = document.querySelectorAll('#trilho-sobre .card').length;
    const limiteSobre = totalDeCardsSobre - 3; 

    if (posicaoAtualSobre < limiteSobre) {
        posicaoAtualSobre++;
        atualizarCarrosselSobre();
    }
});

btnVoltarSobre.addEventListener('click', () => {
    if (posicaoAtualSobre > 0) {
        posicaoAtualSobre--;
        atualizarCarrosselSobre();
    }
});

function atualizarCarrosselSobre() {
    const card = document.querySelector('#trilho-sobre .card');
    if (!card) return; 
    
    const larguraCard = card.offsetWidth;
    const gap = 20; 
    const distancia = (larguraCard + gap) * posicaoAtualSobre;
    
    trilhoSobre.style.transform = `translateX(-${distancia}px)`;
}