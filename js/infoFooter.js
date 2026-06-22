const plataformaItens = [
    { texto: "Como funciona", href: "index.php#como-funciona" },
    { texto: "Ranking de doadores", href: "ranking.php" },
    { texto: "Pontos de coleta", href: "index.php#ecopontos" },
    { texto: "Doações disponíveis", href: "donation_list.php" }
];

const linksItens = [
    { texto: "Doar um aparelho", href: "donate.php" },
    { texto: "Adotar um aparelho", href: "adote.php" },
    { texto: "Diretrizes e regras", href: "diretrizes.php" },
    { texto: "Meu perfil", href: "perfil.php" }
];

const contatoItens = ["contato@reciclatech.com", "Iguatu - Ceará", "Atendimento de segunda a sexta", "Envie sua dúvida por e-mail"];

function preencherListaComLinks(id, itens) {
    const lista = document.getElementById(id);
    if (!lista || lista.children.length) return;
    itens.forEach(item => {
        const li = document.createElement("li");
        const link = document.createElement("a");
        link.href = item.href;
        link.textContent = item.texto;
        li.appendChild(link);
        lista.appendChild(li);
    });
}

function preencherListaDeTexto(id, itens) {
    const lista = document.getElementById(id);
    if (!lista || lista.children.length) return;
    itens.forEach(item => {
        const li = document.createElement("li");
        li.textContent = item;
        lista.appendChild(li);
    });
}

preencherListaComLinks("listaFooterP", plataformaItens);
preencherListaComLinks("listaFooterL", linksItens);
preencherListaDeTexto("listaFooterC", contatoItens);
