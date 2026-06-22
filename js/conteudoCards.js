const itensDoar = ["Celulares", "Notebooks", "Roteadores", "Acessórios"];
const itensNaoDoar = ["Pilhas", "Baterias vazadas", "Lâmpadas", "Equipamentos quebrados"];

const imagensItensDoar = [
    {
        titulo: "Celulares",
        img: "img/celulares.webp",
        alt: "Imagem de celulares"        
    },
    {
        titulo: "Notebooks",
        img: "img/notebooks.webp",
        alt: "Imagem de notebooks"
    },
    {
        titulo: "Roteadores",
        img: "img/roteadores.webp",
        alt: "Imagem de roteadores"
    },
    {
        titulo: "Acessórios",
        img: "img/acessorios.webp",
        alt: "Imagem de acessórios de computador"
    },
]

const imagensItensNaoDoar = [
    {
        titulo: "Pilhas",
        img: "img/pilhas.webp",
        alt: "Imagem de pilhas usadas"
    },
    {
        titulo: "Baterias vazadas",
        img: "img/bateriasVazadas.webp",
        alt: "Imagem de baterias vazadas"
    },
    {
        titulo: "Lâmpadas",
        img: "img/lampada.webp",
        alt: "Imagem de lâmpadas"
    },
    {
        titulo: "Equipamentos quebrados",
        img: "img/equipamentosQuebrados.webp",
        alt: "Imagem de equipamentos tecnologicos quebrados"
    },
]

function cardItensDoar() {
    const lista = document.getElementById("lista");
    itensDoar.forEach(x => {
        const li = document.createElement("li");
        li.innerHTML = `<img src="/img/setinha.svg">`;
        li.textContent = x;
        li.onclick = function() {
            trocarImagensItensDoar.call(li);
            const todosLis = document.querySelectorAll("#lista li");
            todosLis.forEach(item => {
                item.classList.remove("selecionado");
            });
            this.classList.add("selecionado");
        };
        lista.appendChild(li);
    });
}

function cardItensNaoDoar() {
    const lista = document.getElementById("lista2");
    itensNaoDoar.forEach(x => {
        const li = document.createElement("li");
        li.textContent = x;
        li.onclick = function() {
            trocarImagensItensNaoDoar.call(li);
            const todosLis = document.querySelectorAll("#lista2 li");
            todosLis.forEach(item => {
                item.classList.remove("selecionado");
            });
            this.classList.add("selecionado");
        };
        lista.appendChild(li);
    });
}

function trocarImagensItensDoar() {
    const foto = document.getElementById("foto");
    const textoLi = this.textContent;
    imagensItensDoar.forEach(x => {
        if(textoLi === x.titulo){
            foto.src = x.img;
            foto.alt = x.alt;
        }
    });
}

function trocarImagensItensNaoDoar() {
    const foto = document.getElementById("foto2");
    const textoLi = this.textContent;
    imagensItensNaoDoar.forEach(x => {
        if(textoLi === x.titulo){
            foto.src = x.img;
            foto.alt = x.alt;
        }
    });
}

cardItensDoar();
cardItensNaoDoar();