<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Diretrizes e regras para uso seguro e responsável da plataforma ReciclaTech.">
    <title>Diretrizes da Plataforma | ReciclaTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/diretrizes.css">
</head>

<body class="diretrizes-page">
    <section class="diretrizes-topo">
        <?php require 'templates/header.php'; ?>
        <div class="diretrizes-hero">
            <div>
                <span class="diretrizes-etiqueta"><i class="bi bi-shield-check"></i> Comunidade segura e responsável</span>
                <h2>Diretrizes da plataforma</h2>
                <p>Regras simples para doar, solicitar e reutilizar tecnologia com transparência, respeito e segurança.</p>
            </div>
            <aside class="diretrizes-resumo">
                <i class="bi bi-people-fill"></i>
                <div><span>Nosso compromisso</span><strong>Uso consciente</strong><small>Proteção para doadores, adotantes e comunidade</small></div>
            </aside>
        </div>
    </section>

    <main class="diretrizes-conteudo">
        <aside class="diretrizes-indice" aria-label="Nesta página">
            <strong>Nesta página</strong>
            <nav>
                <a href="#principios">Princípios</a>
                <a href="#cadastro">Cadastro e conta</a>
                <a href="#doacoes">Regras para doações</a>
                <a href="#solicitacoes">Solicitações e entrega</a>
                <a href="#proibido">Condutas proibidas</a>
                <a href="#privacidade">Privacidade e segurança</a>
                <a href="#moderacao">Moderação</a>
            </nav>
            <small>Atualizado em 22 de junho de 2026.</small>
        </aside>

        <div class="diretrizes-documento">
            <section class="diretrizes-intro">
                <span>Antes de começar</span>
                <h1>Uma plataforma feita para prolongar a vida útil da tecnologia</h1>
                <p>A ReciclaTech aproxima pessoas que possuem equipamentos sem uso de pessoas que podem reaproveitá-los. Ao utilizar a plataforma, cada participante concorda em agir de boa-fé e seguir estas diretrizes.</p>
            </section>

            <section class="diretriz-secao" id="principios">
                <div class="diretriz-titulo"><span>01</span><div><small>Base da comunidade</small><h2>Princípios de convivência</h2></div></div>
                <div class="diretriz-cards tres-colunas">
                    <article><i class="bi bi-chat-heart"></i><h3>Respeito</h3><p>Trate todas as pessoas com educação, sem discriminação, intimidação ou linguagem ofensiva.</p></article>
                    <article><i class="bi bi-eye"></i><h3>Transparência</h3><p>Descreva corretamente o equipamento, seu estado e qualquer defeito conhecido.</p></article>
                    <article><i class="bi bi-recycle"></i><h3>Responsabilidade</h3><p>Priorize a reutilização e encaminhe itens sem aproveitamento para descarte ambiental adequado.</p></article>
                </div>
            </section>

            <section class="diretriz-secao" id="cadastro">
                <div class="diretriz-titulo"><span>02</span><div><small>Acesso à plataforma</small><h2>Cadastro e uso da conta</h2></div></div>
                <ul class="diretriz-lista">
                    <li><i class="bi bi-check-lg"></i><span>Forneça informações verdadeiras, atualizadas e pertencentes a você.</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Mantenha sua senha protegida e não permita que terceiros utilizem sua conta.</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Use apenas uma conta pessoal e comunique qualquer acesso suspeito.</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Não publique telefone, endereço completo, documentos ou senhas na descrição dos anúncios.</span></li>
                </ul>
            </section>

            <section class="diretriz-secao" id="doacoes">
                <div class="diretriz-titulo"><span>03</span><div><small>Para quem anuncia</small><h2>Regras para doações</h2></div></div>
                <div class="diretriz-duas-colunas">
                    <div>
                        <h3><i class="bi bi-box-seam"></i> O anúncio deve</h3>
                        <ul>
                            <li>Informar tipo, marca, modelo e condição real do item.</li>
                            <li>Usar fotos do próprio equipamento, sempre que possível.</li>
                            <li>Indicar defeitos, peças ausentes e limitações de funcionamento.</li>
                            <li>Ser atualizado ou removido quando o item não estiver mais disponível.</li>
                        </ul>
                    </div>
                    <div class="diretriz-destaque">
                        <h3><i class="bi bi-exclamation-triangle"></i> Antes de entregar</h3>
                        <p>Remova contas, arquivos pessoais, cartões de memória e bloqueios de tela. Restaure o aparelho para as configurações de fábrica quando isso for seguro e possível.</p>
                    </div>
                </div>
            </section>

            <section class="diretriz-secao" id="solicitacoes">
                <div class="diretriz-titulo"><span>04</span><div><small>Do pedido à reutilização</small><h2>Solicitações, aceite e entrega</h2></div></div>
                <ol class="diretriz-passos">
                    <li><span>1</span><div><strong>Solicitação</strong><p>O interessado explica a finalidade de uso e envia o pedido pelo anúncio.</p></div></li>
                    <li><span>2</span><div><strong>Decisão do doador</strong><p>O proprietário do dispositivo analisa o pedido e pode aceitar ou recusar, sem obrigação de aprovação.</p></div></li>
                    <li><span>3</span><div><strong>Combinação</strong><p>Após o aceite, as partes combinam local, data e forma de entrega diretamente.</p></div></li>
                    <li><span>4</span><div><strong>Entrega segura</strong><p>Prefira local público, movimentado e em horário adequado. Menores devem estar acompanhados por responsável.</p></div></li>
                </ol>
                <div class="diretriz-aviso"><i class="bi bi-info-circle-fill"></i><p>A ReciclaTech facilita o contato, mas não realiza transporte, pagamento, garantia técnica ou intermediação financeira entre usuários.</p></div>
            </section>

            <section class="diretriz-secao" id="proibido">
                <div class="diretriz-titulo"><span>05</span><div><small>Proteção da comunidade</small><h2>Itens e condutas proibidas</h2></div></div>
                <div class="diretriz-proibicoes">
                    <article><i class="bi bi-currency-dollar"></i><div><h3>Venda ou cobrança</h3><p>Os itens anunciados como doação não podem exigir pagamento, taxa ou vantagem em troca.</p></div></article>
                    <article><i class="bi bi-phone-flip"></i><div><h3>Origem irregular</h3><p>É proibido anunciar equipamentos roubados, falsificados ou sem autorização do proprietário.</p></div></article>
                    <article><i class="bi bi-battery-charging"></i><div><h3>Risco à segurança</h3><p>Baterias inchadas, vazando, perfuradas ou itens com risco imediato devem ir a um ponto de coleta adequado.</p></div></article>
                    <article><i class="bi bi-person-x"></i><div><h3>Abuso e fraude</h3><p>Não são tolerados golpes, spam, assédio, discriminação, perfis falsos ou tentativa de obter dados indevidos.</p></div></article>
                </div>
            </section>

            <section class="diretriz-secao" id="privacidade">
                <div class="diretriz-titulo"><span>06</span><div><small>Cuidado com seus dados</small><h2>Privacidade e segurança</h2></div></div>
                <div class="diretriz-cards">
                    <article><i class="bi bi-lock"></i><h3>Compartilhe o mínimo</h3><p>Envie dados de contato e localização exata apenas quando necessário para uma entrega já combinada.</p></article>
                    <article><i class="bi bi-shield-exclamation"></i><h3>Desconfie de pedidos incomuns</h3><p>Não informe senhas, códigos de verificação, dados bancários ou documentos a outros usuários.</p></article>
                </div>
            </section>

            <section class="diretriz-secao" id="moderacao">
                <div class="diretriz-titulo"><span>07</span><div><small>Aplicação das regras</small><h2>Moderação e medidas</h2></div></div>
                <p>A equipe pode revisar anúncios e atividades reportadas. Dependendo da gravidade ou repetição, poderá solicitar correções, remover conteúdo, cancelar solicitações, ajustar pontuações ou suspender contas.</p>
                <div class="diretriz-aviso contato"><i class="bi bi-flag-fill"></i><div><strong>Encontrou algo inadequado?</strong><p>Não prossiga com a entrega e comunique a equipe pelo e-mail <a href="mailto:contato@reciclatech.com">contato@reciclatech.com</a>.</p></div></div>
            </section>

            <section class="diretrizes-final">
                <i class="bi bi-leaf-fill"></i>
                <div><span>Obrigado por fazer parte</span><h2>Cada atitude responsável ajuda a tecnologia a circular por mais tempo.</h2></div>
                <a href="donation_list.php">Ver doações <i class="bi bi-arrow-right"></i></a>
            </section>
        </div>
    </main>

    <?php require 'templates/footer.php'; ?>
    <script src="js/dropdown.js"></script>
    <script src="js/infoFooter.js"></script>
</body>

</html>
