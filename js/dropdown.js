function mostrarDropdown() {
    const menu = document.querySelector(".dropdown");
    if (!menu) return;
    const displayAtual = window.getComputedStyle(menu).display;

    if (displayAtual === "none") {
        menu.style.display = "block";
    } else {
        menu.style.display = "none";
    }
}

function alternarMenuMobile() {
    const menu = document.querySelector(".menu-direita");
    const botao = document.querySelector(".menu-mobile-toggle");
    if (!menu || !botao) return;
    const aberto = menu.classList.toggle("aberto");
    botao.classList.toggle("ativo", aberto);
    botao.setAttribute("aria-expanded", String(aberto));
    botao.setAttribute("aria-label", aberto ? "Fechar menu" : "Abrir menu");
}

document.querySelectorAll(".menu-direita a").forEach(link => link.addEventListener("click", () => {
    document.querySelector(".menu-direita")?.classList.remove("aberto");
    const botao = document.querySelector(".menu-mobile-toggle");
    botao?.classList.remove("ativo");
    botao?.setAttribute("aria-expanded", "false");
}));

window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
        document.querySelector(".menu-direita")?.classList.remove("aberto");
        document.querySelector(".menu-mobile-toggle")?.classList.remove("ativo");
    }
});