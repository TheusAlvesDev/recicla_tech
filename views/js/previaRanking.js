const doadoresRanking = [
    {
        doador: "Usuario 1",
        quantidadeD: 0,
        pontuacao: 0
    },
    {
        doador: "Usuario 2",
        quantidadeD: 0,
        pontuacao: 0
    },
    {
        doador: "Usuario 3",
        quantidadeD: 0,
        pontuacao: 0
    },
    {
        doador: "Usuario 1",
        quantidadeD: 0,
        pontuacao: 0
    }
];

function doadores() {
    const tabela = document.querySelector(".ranking");
    tabela.innerHTML = "";

    doadoresRanking.forEach((x, index) => {
        const div = document.createElement("div");
        div.classList.add("teste");
        div.innerHTML = `
            <span class="colocacao">${index + 1}</span>
            <div class="user">
                <img src="../asset/img/User.png" alt="">
                <span>${x.doador}</span>
            </div>
            <span>${x.quantidadeD}</span>
            <span class="pontos">${x.pontuacao}</span>
        `;
        tabela.appendChild(div);
    });
}

doadores();
