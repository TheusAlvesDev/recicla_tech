const abasPerfil = document.querySelectorAll(".perfil-tab");
const paineisPerfil = document.querySelectorAll(".perfil-painel");
const botoesAbrirAba = document.querySelectorAll("[data-abrir-aba]");
const formularioPerfil = document.getElementById("form-perfil");
const mensagemPerfil = document.getElementById("perfil-mensagem");
const botaoAvatar = document.querySelector(".perfil-avatar-editar");
const imagemAvatar = document.querySelector(".perfil-avatar");
let temporizadorMensagem;

function ativarAbaPerfil(nomeAba, atualizarUrl = true) {
    const abaValida = document.querySelector(`[data-aba="${nomeAba}"]`) ? nomeAba : "visao";

    abasPerfil.forEach(aba => {
        const ativa = aba.dataset.aba === abaValida;
        aba.classList.toggle("ativo", ativa);
        aba.setAttribute("aria-selected", String(ativa));
        aba.tabIndex = ativa ? 0 : -1;
    });

    paineisPerfil.forEach(painel => {
        const ativo = painel.dataset.painel === abaValida;
        painel.classList.toggle("ativo", ativo);
        painel.hidden = !ativo;
    });

    if (atualizarUrl) {
        const url = new URL(window.location.href);
        if (abaValida === "visao") {
            url.searchParams.delete("aba");
        } else {
            url.searchParams.set("aba", abaValida);
        }
        window.history.replaceState({}, "", url);
    }
}

function mostrarMensagemPerfil(texto) {
    if (!mensagemPerfil) return;
    mensagemPerfil.lastChild.textContent = ` ${texto}`;
    mensagemPerfil.classList.add("visivel");
    window.clearTimeout(temporizadorMensagem);
    temporizadorMensagem = window.setTimeout(() => mensagemPerfil.classList.remove("visivel"), 3000);
}

abasPerfil.forEach(aba => {
    aba.addEventListener("click", () => ativarAbaPerfil(aba.dataset.aba));
    aba.addEventListener("keydown", evento => {
        if (evento.key !== "ArrowRight" && evento.key !== "ArrowLeft") return;
        evento.preventDefault();
        const lista = [...abasPerfil];
        const direcao = evento.key === "ArrowRight" ? 1 : -1;
        const indice = (lista.indexOf(aba) + direcao + lista.length) % lista.length;
        lista[indice].focus();
        ativarAbaPerfil(lista[indice].dataset.aba);
    });
});

botoesAbrirAba.forEach(botao => {
    botao.addEventListener("click", () => {
        ativarAbaPerfil(botao.dataset.abrirAba);
        document.querySelector(".perfil-tabs-wrap")?.scrollIntoView({ behavior: "smooth" });
    });
});

formularioPerfil?.addEventListener("submit", evento => {
    evento.preventDefault();
    if (!formularioPerfil.reportValidity()) return;

    const nome = document.getElementById("perfil-nome").value.trim();
    const tituloNome = document.querySelector(".perfil-identidade h2");
    if (tituloNome && nome) tituloNome.textContent = nome;
    mostrarMensagemPerfil("Dados atualizados com sucesso.");
});

botaoAvatar?.addEventListener("click", () => {
    const seletorArquivo = document.createElement("input");
    seletorArquivo.type = "file";
    seletorArquivo.accept = "image/png,image/jpeg,image/webp";
    seletorArquivo.addEventListener("change", () => {
        const arquivo = seletorArquivo.files?.[0];
        if (!arquivo || !imagemAvatar) return;
        imagemAvatar.src = URL.createObjectURL(arquivo);
        mostrarMensagemPerfil("Foto atualizada na visualização.");
    });
    seletorArquivo.click();
});

const abaInicial = new URLSearchParams(window.location.search).get("aba") || "visao";
ativarAbaPerfil(abaInicial, false);
