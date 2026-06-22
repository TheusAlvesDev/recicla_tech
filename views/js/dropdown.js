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

    const estaAberto = menu.classList.toggle("aberto");
    botao.classList.toggle("ativo", estaAberto);
    botao.setAttribute("aria-expanded", String(estaAberto));
    botao.setAttribute("aria-label", estaAberto ? "Fechar menu" : "Abrir menu");
}

document.querySelectorAll(".menu-direita nav a, .menu-direita .botoes a").forEach(link => {
    link.addEventListener("click", () => {
        const menu = document.querySelector(".menu-direita");
        const botao = document.querySelector(".menu-mobile-toggle");
        if (!menu || !botao) return;

        menu.classList.remove("aberto");
        botao.classList.remove("ativo");
        botao.setAttribute("aria-expanded", "false");
        botao.setAttribute("aria-label", "Abrir menu");
    });
});

window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
        const menu = document.querySelector(".menu-direita");
        const botao = document.querySelector(".menu-mobile-toggle");
        if (!menu || !botao) return;

        menu.classList.remove("aberto");
        botao.classList.remove("ativo");
        botao.setAttribute("aria-expanded", "false");
        botao.setAttribute("aria-label", "Abrir menu");
    }
});