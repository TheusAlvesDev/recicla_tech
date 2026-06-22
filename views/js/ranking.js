const doadoresRankingCompleto = Array.isArray(window.rankingData) ? window.rankingData : [];

const listaRanking = document.getElementById("ranking-lista");
const buscaRanking = document.getElementById("busca-ranking");
const periodoRanking = document.getElementById("periodo-ranking");
const contagemRanking = document.getElementById("ranking-contagem");

function normalizarTexto(texto) {
    return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

function formatarPontos(valor) {
    return new Intl.NumberFormat("pt-BR").format(valor);
}

function criarLinhaRanking(doador, posicao, periodo) {
    const linha = document.createElement("div");
    linha.className = `ranking-linha${doador.atual ? " usuario-atual" : ""}`;
    linha.setAttribute("role", "row");

    const posicaoElemento = document.createElement("span");
    posicaoElemento.className = "ranking-numero";
    posicaoElemento.textContent = posicao;

    const doadorElemento = document.createElement("div");
    doadorElemento.className = "ranking-doador";
    doadorElemento.innerHTML = `
        <img src="../img/User.png" alt="">
        <div><strong>${doador.nome}</strong><small>${doador.cidade}</small></div>
    `;

    const doacoesElemento = document.createElement("span");
    doacoesElemento.textContent = doador.doacoes;

    const impactoElemento = document.createElement("span");
    impactoElemento.className = "ranking-impacto";
    impactoElemento.textContent = doador.impacto;

    const pontosElemento = document.createElement("span");
    pontosElemento.className = "ranking-pontos";
    pontosElemento.textContent = `${formatarPontos(doador[periodo])} pts`;

    linha.append(posicaoElemento, doadorElemento, doacoesElemento, impactoElemento, pontosElemento);
    return linha;
}

function renderizarRanking() {
    if (!listaRanking || !buscaRanking || !periodoRanking || !contagemRanking) return;

    const termo = normalizarTexto(buscaRanking.value.trim());
    const periodo = periodoRanking.value;
    const ordenados = [...doadoresRankingCompleto].sort((a, b) => b[periodo] - a[periodo]);
    const filtrados = ordenados.filter(doador => normalizarTexto(doador.nome).includes(termo));

    listaRanking.replaceChildren();

    if (filtrados.length === 0) {
        const vazio = document.createElement("p");
        vazio.className = "ranking-vazio";
        vazio.textContent = "Nenhum doador encontrado.";
        listaRanking.appendChild(vazio);
    } else {
        filtrados.forEach(doador => {
            const posicaoReal = ordenados.indexOf(doador) + 1;
            listaRanking.appendChild(criarLinhaRanking(doador, posicaoReal, periodo));
        });
    }

    contagemRanking.textContent = `${filtrados.length} ${filtrados.length === 1 ? "doador encontrado" : "doadores encontrados"}`;
}

buscaRanking?.addEventListener("input", renderizarRanking);
periodoRanking?.addEventListener("change", renderizarRanking);
renderizarRanking();
