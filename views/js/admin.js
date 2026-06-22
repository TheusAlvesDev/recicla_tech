const paginaAdmin = document.querySelector(".admin-page");
const botaoMenuAdmin = document.querySelector(".admin-menu-toggle");
const botoesFecharMenu = document.querySelectorAll("[data-fechar-menu]");
const linksNavegacaoAdmin = document.querySelectorAll(".admin-nav a[href^='#']");
const buscaDoacoesAdmin = document.getElementById("admin-busca-doacoes");
const tabelaDoacoesAdmin = document.getElementById("admin-tabela-doacoes");
const semResultadosAdmin = document.getElementById("admin-sem-resultados");
const botaoExportarAdmin = document.getElementById("admin-exportar");
const botaoAtualizarAdmin = document.getElementById("admin-atualizar");
const toastAdmin = document.getElementById("admin-toast");
let temporizadorToastAdmin;

function normalizarAdmin(texto) {
    return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

function definirMenuAdmin(aberto) {
    paginaAdmin?.classList.toggle("menu-aberto", aberto);
    botaoMenuAdmin?.setAttribute("aria-expanded", String(aberto));
    document.body.style.overflow = aberto ? "hidden" : "";
}

function mostrarToastAdmin(mensagem) {
    if (!toastAdmin) return;
    const textoToast = toastAdmin.querySelector("span");
    if (textoToast) textoToast.textContent = mensagem;
    toastAdmin.classList.add("visivel");
    window.clearTimeout(temporizadorToastAdmin);
    temporizadorToastAdmin = window.setTimeout(() => toastAdmin.classList.remove("visivel"), 2800);
}

botaoMenuAdmin?.addEventListener("click", () => definirMenuAdmin(true));
botoesFecharMenu.forEach(botao => botao.addEventListener("click", () => definirMenuAdmin(false)));

linksNavegacaoAdmin.forEach(link => {
    link.addEventListener("click", () => {
        linksNavegacaoAdmin.forEach(item => item.classList.remove("ativo"));
        link.classList.add("ativo");
        if (window.innerWidth <= 1050) definirMenuAdmin(false);
    });
});

window.addEventListener("resize", () => {
    if (window.innerWidth > 1050) definirMenuAdmin(false);
});

buscaDoacoesAdmin?.addEventListener("input", () => {
    const termo = normalizarAdmin(buscaDoacoesAdmin.value.trim());
    const linhas = tabelaDoacoesAdmin?.querySelectorAll("tbody tr[data-dispositivo]") || [];
    let visiveis = 0;

    linhas.forEach(linha => {
        const corresponde = normalizarAdmin(linha.dataset.dispositivo || "").includes(termo);
        linha.hidden = !corresponde;
        if (corresponde) visiveis++;
    });

    if (semResultadosAdmin) semResultadosAdmin.hidden = visiveis !== 0;
});

botaoAtualizarAdmin?.addEventListener("click", () => {
    const icone = botaoAtualizarAdmin.querySelector("i");
    icone?.classList.add("admin-girando");
    botaoAtualizarAdmin.disabled = true;

    window.setTimeout(() => {
        icone?.classList.remove("admin-girando");
        botaoAtualizarAdmin.disabled = false;
        mostrarToastAdmin("Dados atualizados com sucesso.");
    }, 650);
});

botaoExportarAdmin?.addEventListener("click", () => {
    const linhas = tabelaDoacoesAdmin?.querySelectorAll("tbody tr[data-dispositivo]") || [];
    const cabecalho = ["ID", "Dispositivo", "Doador", "Doado em", "Status"];
    const registros = [...linhas].map(linha => {
        const celulas = linha.querySelectorAll("td");
        return [
            celulas[0]?.textContent.trim(),
            linha.dataset.dispositivo,
            celulas[2]?.textContent.trim(),
            celulas[3]?.textContent.trim(),
            celulas[4]?.textContent.trim()
        ];
    });

    const escapar = valor => `"${String(valor || "").replaceAll('"', '""')}"`;
    const csv = [cabecalho, ...registros].map(linha => linha.map(escapar).join(";")).join("\n");
    const arquivo = new Blob(["\ufeff", csv], { type: "text/csv;charset=utf-8" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(arquivo);
    link.download = "historico-doacoes-reciclatech.csv";
    link.click();
    URL.revokeObjectURL(link.href);
    mostrarToastAdmin("Histórico exportado em CSV.");
});
