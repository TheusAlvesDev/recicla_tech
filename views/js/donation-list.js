document.addEventListener('DOMContentLoaded', () => {
    
    const botoes = document.querySelectorAll('#sortButtonsContainer .botao-card');

    botoes.forEach(botao => {
        botao.addEventListener('click', function() {
            
            botoes.forEach(b => {
                b.classList.add('not-selected');
                b.classList.remove('gradiente-bts-principais', 'botao-transicao');
            });

            
            this.classList.remove('not-selected');
            this.classList.add('gradiente-bts-principais', 'botao-transicao');
        });
    });
});